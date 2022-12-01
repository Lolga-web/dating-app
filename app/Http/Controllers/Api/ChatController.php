<?php

namespace App\Http\Controllers\Api;

use App\Events\DeletePrivateChatMessage;
use App\Events\PrivateChatMessage;
use App\Events\PrivateChatReadEvent;
use App\Events\PrivateChatTypingEvent;
use App\Events\UpdatePrivateChatMessage;
use App\helpers;
use Throwable;

use App\Http\Requests\Api\Chat\MessageDeleteRequest;
use App\Http\Requests\Api\Chat\MessageMediaRequest;
use App\Http\Requests\Api\Chat\MessageStoreRequest;
use App\Http\Requests\Api\Chat\MessageUpdateRequest;
use App\Http\Requests\Api\Chat\SendEventRequest;
use App\Http\Resources\Chat\IndexResource;
use App\Http\Resources\Chat\MessageResource;

use App\Models\Chat;
use App\Models\Users\User;

use App\Services\ChatService;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use function App\send_push_notify;

class ChatController extends ApiBaseController
{
    /**
     * @OA\Get(
     *     path="/chats",
     *     security={{ "passport": {"*"} }},
     *     operationId="chats-list",
     *     tags={"Chats"},
     *     summary="Chats list",
     *     @OA\Parameter(name="X-Localization", in="header", required=true, @OA\Schema(type="string"), description="Lang code", example="en"),
     *     @OA\Response(
     *          response="200",
     *          description="**OK** Chats list",
     *          @OA\JsonContent(ref="#/components/schemas/ChatIndexResource")
     *     ),
     *     @OA\Response(
     *          response="405",
     *          description="**Not allowed method**. You have to use **correct method**",
     *     ),
     *     @OA\Response(
     *          response="401",
     *          description="**Unauthorized** Invalid credentials.",
     *     ),
     * )
     *
     * @return JsonResponse
     */
    public function getChatsList()
    {
        $chatUsers = User::whereIn('id', Chat::getChatsUsers())
                          ->get()
                          ->map(fn($item) => $item->setAttribute('last_message', Chat::chat(User::find($item->id))->lastMessage()));

        return $this->sendResponse(IndexResource::collection($chatUsers->sortBy([['last_message.created_at', 'desc']])), __('Chats list'));
    }

    /**
     * @OA\Get(
     *     path="/chat/{user}",
     *     security={{ "passport": {"*"} }},
     *     operationId="get-user-chat",
     *     tags={"Chats"},
     *     summary="Get chat with user",
     *     @OA\Parameter(name="X-Localization", in="header", required=true, @OA\Schema(type="string"), description="Lang code", example="en"),
     *     @OA\Parameter(name="user", in="path", required=true, @OA\Schema(type="string"), description="user ID", example=1),
     *     @OA\Response(
     *          response="200",
     *          description="**OK** Chat with user",
     *          @OA\JsonContent(ref="#/components/schemas/MessageResource")
     *     ),
     *     @OA\Response(
     *          response="405",
     *          description="**Not allowed method**. You have to use **correct method**",
     *     ),
     *     @OA\Response(
     *          response="401",
     *          description="**Unauthorized** Invalid credentials.",
     *     ),
     * )
     *
     * @return JsonResponse
     */
    public function showChat(User $user)
    {
        $messages = Chat::query()
                         ->chat($user)
                         ->latest()
                         ->get();

        return $this->sendResponse(MessageResource::collection($messages), __('Chat with user'));
    }

    /**
     * @OA\Delete(
     *     path="/chat/{user}",
     *     security={{ "passport": {"*"} }},
     *     operationId="delete-chat",
     *     tags={"Chats"},
     *     summary="Delete chat with user",
     *     @OA\Parameter(name="X-Localization", in="header", required=true, @OA\Schema(type="string"), description="Lang code", example="en"),
     *     @OA\Parameter(name="user", in="path", required=true, @OA\Schema(type="string"), description="user ID", example=1),
     *     @OA\Response(
     *          response="200",
     *          description="**OK** Chat deleted",
     *     ),
     *     @OA\Response(
     *          response="412",
     *          description="**HTTP_PRECONDITION_FAILED** You can not delete this message",
     *     ),
     * )
     *
     * @param User $user
     * @return JsonResponse
     */
    public function deleteChat(User $user, ChatService $chatService)
    {
        $chatService->deleteChat($user);

        return $this->sendResponse([], __('Message deleted'));
    }

    /**
     * @OA\Post(
     *     path="/message/{recipient}/{parent?}",
     *     security={{ "passport": {"*"} }},
     *     operationId="send-message",
     *     tags={"Chats"},
     *     summary="Send message",
     *     @OA\Parameter(name="X-Localization", in="header", required=true, @OA\Schema(type="string"), description="Lang code", example="en"),
     *     @OA\Parameter(name="recipient", in="path", required=true, @OA\Schema(type="integer"), description="User id", example=1),
     *     @OA\Parameter(name="parent", in="path", required=false, @OA\Schema(type="integer"), description="Message id", example=1),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(ref="#/components/schemas/MessageStoreRequest")
     *         )
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="**OK** Message sent successfully",
     *          @OA\JsonContent(ref="#/components/schemas/MessageResource")
     *     ),
     *     @OA\Response(
     *          response="405",
     *          description="**Not allowed method**. You have to use **correct method**",
     *     ),
     *     @OA\Response(
     *          response="401",
     *          description="**Unauthorized** Invalid credentials.",
     *     ),
     * )
     *
     * @return JsonResponse
     */
    public function store(MessageStoreRequest $request)
    {
        try {
            DB::beginTransaction();

            $message = Chat::create($request->validated());

            if ($request->has('image')) {
                $media = $message->addMedia($request->file('image'))
                                ->usingFileName(helpers::getImageNameByFileName($request->file('image')))
                                ->toMediaCollection($message->imageCollection);

                $message->update(['media_id' => $media->id]);
            }

            broadcast(new PrivateChatMessage(MessageResource::make($message), (int) $message->recipient_id));

            DB::commit();
        } catch (Throwable $e) {
            DB::rollback();
            Log::debug($e->getMessage());
            return $this->sendError(__('Something wrong.'), JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = User::find($request->input('recipient_id'));
        if ($user->canNotify('message')) {
            send_push_notify(
                'message',
                __('New message'),
                __('New message from ') . \Auth::user()->name,
                $user
            );
        }

        return $this->sendResponse(MessageResource::make($message), __('Message sent successfully'));
    }

    /**
     * @OA\Put(
     *     path="/message/{message}",
     *     security={{ "passport": {"*"} }},
     *     operationId="update-message",
     *     tags={"Chats"},
     *     summary="Update message",
     *     @OA\Parameter(name="X-Localization", in="header", required=true, @OA\Schema(type="string"), description="Lang code", example="en"),
     *     @OA\Parameter(name="message", in="path", required=true, @OA\Schema(type="string"), description="message ID", example=1),
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/MessageUpdateRequest")),
     *     @OA\Response(
     *          response="200",
     *          description="**OK** Message update successfully",
     *          @OA\JsonContent(ref="#/components/schemas/MessageResource")
     *     ),
     *     @OA\Response(
     *          response="405",
     *          description="**Not allowed method**. You have to use **correct method**",
     *     ),
     *     @OA\Response(
     *          response="401",
     *          description="**Unauthorized** Invalid credentials.",
     *     ),
     * )
     *
     * @return JsonResponse
     */
    public function update(Chat $message, MessageUpdateRequest $request)
    {
        $message->update(['text' => $request->input('text')]);

        broadcast(new UpdatePrivateChatMessage(MessageResource::make($message), (int) $message->recipient_id));

        return $this->sendResponse(MessageResource::make($message), __('Message update successfully'));
    }

    /**
     * @OA\Delete(
     *     path="/message/{message}",
     *     security={{ "passport": {"*"} }},
     *     operationId="delete-message",
     *     tags={"Chats"},
     *     summary="Delete message",
     *     @OA\Parameter(name="X-Localization", in="header", required=true, @OA\Schema(type="string"), description="Lang code", example="en"),
     *     @OA\Parameter(name="message", in="path", required=true, @OA\Schema(type="string"), description="message ID", example=1),
     *     @OA\Response(
     *          response="200",
     *          description="**OK** Message deleted",
     *     ),
     *     @OA\Response(
     *          response="412",
     *          description="**HTTP_PRECONDITION_FAILED** You can not delete this message",
     *     ),
     * )
     *
     * @param Chat $message
     * @return JsonResponse
     */
    public function delete(Chat $message, MessageDeleteRequest $request)
    {
        $message->delete();

        broadcast(new DeletePrivateChatMessage(MessageResource::make($message), (int) $message->recipient_id));

        return $this->sendResponse([], __('Message deleted'));
    }

    /**
     * @OA\Post(
     *     path="/chat-typing-event/{recipient}",
     *     security={{ "passport": {"*"} }},
     *     operationId="chat-typing-event",
     *     tags={"Chats"},
     *     summary="Send chat typing event",
     *     @OA\Parameter(name="X-Localization", in="header", required=true, @OA\Schema(type="string"), description="Lang code", example="en"),
     *     @OA\Parameter(name="recipient", in="path", required=true, @OA\Schema(type="string"), description="User ID", example=1),
     *     @OA\Response(
     *          response="200",
     *          description="**OK** Event send successfully",
     *     ),
     *     @OA\Response(
     *          response="405",
     *          description="**Not allowed method**. You have to use **correct method**",
     *     ),
     *     @OA\Response(
     *          response="401",
     *          description="**Unauthorized** Invalid credentials.",
     *     ),
     * )
     *
     * @return JsonResponse
     */
    public function sendChatTypingEvent(SendEventRequest $request)
    {
        broadcast(new PrivateChatTypingEvent((int) $request->input('recipient_id'), (int) \Auth::id()));

        return $this->sendResponse([], __('Event send successfully'));
    }

    /**
     * @OA\Post(
     *     path="/chat-read-event/{recipient}",
     *     security={{ "passport": {"*"} }},
     *     operationId="chat-read-event",
     *     tags={"Chats"},
     *     summary="Send chat read event",
     *     @OA\Parameter(name="X-Localization", in="header", required=true, @OA\Schema(type="string"), description="Lang code", example="en"),
     *     @OA\Parameter(name="recipient", in="path", required=true, @OA\Schema(type="string"), description="User ID", example=1),
     *     @OA\Response(
     *          response="200",
     *          description="**OK** Event send successfully",
     *     ),
     *     @OA\Response(
     *          response="405",
     *          description="**Not allowed method**. You have to use **correct method**",
     *     ),
     *     @OA\Response(
     *          response="401",
     *          description="**Unauthorized** Invalid credentials.",
     *     ),
     * )
     *
     * @return JsonResponse
     */
    public function sendChatReadEvent(SendEventRequest $request)
    {
        $messages = Chat::where([
                ['recipient_id', \Auth::id()],
                ['sender_id', $request->input('recipient_id')]
            ])
            ->whereNull('read_at')
            ->get();

        $lastMessage = $messages->last();

        if ($lastMessage) {
            $messages->map(function ($item) {
                $item->read_at = now();
                $item->save();
            });

            broadcast(new PrivateChatReadEvent(MessageResource::make($lastMessage), (int) $request->input('recipient_id')));

            return $this->sendResponse([], __('Event send successfully'));
        }

        return $this->sendResponse([], __('No unread messages'));
    }

}
