<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Api\ApiBaseController;

use App\Http\Requests\Api\Like\StoreRequest;

use App\Http\Resources\User\LikeResource;
use App\Http\Resources\User\PhotoResource;

use App\Models\Like;
use App\Models\Media;
use App\Models\Users\User;

use function App\send_push_notify;

class LikeController extends ApiBaseController
{
    /**
     * @OA\Get(
     *     path="/likes",
     *     security={{ "passport": {"*"} }},
     *     operationId="get-user-likes",
     *     tags={"Like"},
     *     summary="Get auth user likes",
     *     @OA\Parameter(name="X-Localization", in="header", required=true, @OA\Schema(type="string"), description="Lang code", example="en"),
     *     @OA\Response(
     *          response="200",
     *          description="**OK** Like list",
     *          @OA\JsonContent(ref="#/components/schemas/LikeResource")
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
    public function index()
    {
        $user = \Auth::user();
        $likes = $user->getLikes()->orderByDesc('created_at')->get();

        return $this->sendResponse(LikeResource::collection($likes), __('Like list'));
    }

    /**
     * @OA\Post(
     *     path="/likes",
     *     security={{ "passport": {"*"} }},
     *     operationId="store-photo-like",
     *     tags={"Like"},
     *     summary="Create or delete like",
     *     @OA\Parameter(name="X-Localization", in="header", required=true, @OA\Schema(type="string"), description="Lang code", example="en"),
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/LikeStoreRequest")),
     *     @OA\Response(
     *          response="200",
     *          description="**OK** Liked successfully/Like successfully deleted",
     *          @OA\JsonContent(ref="#/components/schemas/PhotoResource")
     *     ),
     *     @OA\Response(
     *          response="412",
     *          description="**HTTP_PRECONDITION_FAILED** You can not delete this message",
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
    public function store(StoreRequest $request)
    {
        $like = Like::where([['media_id', $request->input('photo_id')], ['from_user_id', \Auth::id()]])->first();
        $photo = Media::find($request->input('photo_id'));

        if ($like) {
            $like->delete();

            return $this->sendResponse(PhotoResource::make($photo), __('Like successfully deleted'));
        } else {
            Like::create([
                'media_id' => $request->input('photo_id'),
                'from_user_id' => \Auth::id(),
            ]);

            $user = User::find($photo->model_id);
            if ($user->canNotify('like')) {
                send_push_notify(
                    'like',
                    __('New like'),
                    \Auth::user()->name . __(' liked your photo'),
                    $user
                );
            }

            return $this->sendResponse(PhotoResource::make($photo), __('Liked successfully'));
        }
    }

}
