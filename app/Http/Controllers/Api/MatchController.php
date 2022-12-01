<?php

namespace App\Http\Controllers\Api;

use Throwable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Http\Requests\Api\Match\StoreRequest;
use App\Http\Resources\MatchResource;
use App\Http\Resources\User\IndexResource;

use App\Models\UserMatch;
use App\Models\Users\User;

use function App\send_push_notify;

class MatchController extends ApiBaseController
{
    /**
     * @OA\Get(
     *     path="/match-users",
     *     security={{ "passport": {"*"} }},
     *     operationId="match-users",
     *     tags={"Match"},
     *     summary="Users for match",
     *     @OA\Parameter(name="X-Localization", in="header", required=true, @OA\Schema(type="string"), description="Lang code", example="en"),
     *     @OA\Response(
     *          response="200",
     *          description="**OK** Users list",
     *          @OA\JsonContent(ref="#/components/schemas/UserIndexResource")
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
    public function getUsers()
    {
        $users = User::query()
                     ->role('user')
                     ->whereNotIn('id', [\Auth::id()])
                     ->whereIn('gender', \Auth::user()->look_for)
                     ->whereDoesntHave('matches', function (Builder $query) {
                        $query->whereIn('from_user_id', [\Auth::id()]);
                     })
                     ->whereDoesntHave('settings', function (Builder $query) {
                        $query->whereIn('matches', [false]);
                     })
                     ->has('media')
                     ->get();

        $users->whereNotNull('location')->map->addDistance(\Auth::user());

        return $this->sendResponse(IndexResource::collection($users->sortBy('distance')->take(100)), __('Users list'));
    }

    /**
     * @OA\Get(
     *     path="/match",
     *     security={{ "passport": {"*"} }},
     *     operationId="get-match",
     *     tags={"Match"},
     *     summary="Match list",
     *     @OA\Parameter(name="X-Localization", in="header", required=true, @OA\Schema(type="string"), description="Lang code", example="en"),
     *     @OA\Response(
     *          response="200",
     *          description="**OK** Match list",
     *          @OA\JsonContent(ref="#/components/schemas/MatchResource")
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
        $matches = UserMatch::where([
                                'user_id' => \Auth::id(),
                                'matched' => true,
                            ])
                            ->paginate(20);

        return $this->sendResponse(MatchResource::collection($matches), __('Match list'));
    }

    /**
     * @OA\Post(
     *     path="/match",
     *     security={{ "passport": {"*"} }},
     *     operationId="store-match",
     *     tags={"Match"},
     *     summary="Add match",
     *     @OA\Parameter(name="X-Localization", in="header", required=true, @OA\Schema(type="string"), description="Lang code", example="en"),
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/MatchStoreRequest")),
     *     @OA\Response(
     *          response="200",
     *          description="**OK** Matched successfully",
     *          @OA\JsonContent(ref="#/components/schemas/MatchResource")
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
        try {
            DB::beginTransaction();

            $match = UserMatch::where([
                ['user_id', \Auth::id()],
                ['from_user_id', $request->input('user_id')]])
              ->update(['matched' => true]);

            $newMatch = UserMatch::create([
                'user_id' => $request->input('user_id'),
                'from_user_id' => \Auth::id(),
                'viewed' => false,
                'matched' => $match ? true : false,
            ]);

            DB::commit();
        } catch (Throwable $e) {
            DB::rollback();
            Log::debug($e->getMessage());
            return $this->sendError(__('Something wrong.'), JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = User::find($request->input('user_id'));
        if ($user->canNotify('match') && $match) {
            send_push_notify(
                'match',
                __('New match'),
                __('New match with ') . \Auth::user()->name,
                $user
            );
        }

        return $this->sendResponse(MatchResource::make($newMatch), __('Matched successfully'));
    }
}
