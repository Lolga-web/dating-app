<?php

namespace App\Http\Controllers\Api;

use App\Events\DepositCoinsEvent;
use App\Http\Requests\Api\Voting\GetPhotosRequest;
use App\Http\Requests\Api\Voting\IndexRequest;
use App\Http\Requests\Api\Voting\StoreRequest;

use App\Http\Resources\TopResource;
use App\Http\Resources\User\PhotoResource;
use App\Http\Resources\User\VoteResource;
use App\Models\Media;
use App\Models\Voting;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use Throwable;

class VotingController extends ApiBaseController
{
    /**
     * @OA\Post(
     *     path="/voting-photos",
     *     security={{ "passport": {"*"} }},
     *     operationId="photos-for-voting",
     *     tags={"Top"},
     *     summary="Photos for voting",
     *     @OA\Parameter(name="X-Localization", in="header", required=true, @OA\Schema(type="string"), description="Lang code", example="en"),
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/GetVotingPhotosRequest")),
     *     @OA\Response(
     *          response="200",
     *          description="**OK** Photos for voting",
     *          @OA\JsonContent(ref="#/components/schemas/PhotoResource")
     *     ),
     *     @OA\Response(
     *          response="404",
     *          description="**Not found**. No photos for voting",
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
    public function getPhotos(GetPhotosRequest $request)
    {
        $photos = Media::where('collection_name', 'user_photo')
                        ->has('user')
                        ->inRandomOrder()
                        ->get()
                        ->filter(function ($item) use ($request) {
                            return (
                                $item->getCustomProperty('top') &&
                                !\Auth::user()->isVoted($item->id) &&
                                $item->user()->isNot(\Auth::user()) &&
                                $item->user->gender == $request->input('gender') &&
                                ($request->has('country') ? $item->user->location->country == $request->input('country') : true)
                            );
                        })
                        ->unique('model_id');

        if ($photos->count() >= 2) {
            return $this->sendResponse(PhotoResource::collection($photos->random(2)), __('Photos for voting'));
        }

        return $this->sendError(__('No photos for voting'), JsonResponse::HTTP_NOT_FOUND);
    }

    /**
     * @OA\Post(
     *     path="/top",
     *     security={{ "passport": {"*"} }},
     *     operationId="get-top",
     *     tags={"Top"},
     *     summary="Get top list",
     *     @OA\Parameter(name="X-Localization", in="header", required=true, @OA\Schema(type="string"), description="Lang code", example="en"),
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/VotingIndexRequest")),
     *     @OA\Response(
     *          response="200",
     *          description="**OK** Top list",
     *          @OA\JsonContent(ref="#/components/schemas/TopResource")
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
    public function index(IndexRequest $request)
    {
        $photos = Media::where('collection_name', 'user_photo')
                        ->has('winning')
                        ->get()
                        ->filter(function ($item) use ($request) {
                            return (
                                $item->getCustomProperty('top') &&
                                $item->winning->count() >= 10 &&
                                $item->user->gender == $request->input('gender') &&
                                ($request->has('country') ? $item->user->location->country == $request->input('country') : true) &&
                                $item->setAttribute('rating', $item->calcRating())
                            );
                        })
                        ->sortByDesc('rating');

        return $this->sendResponse(TopResource::collection($photos), __('Top list'));
    }

    /**
     * @OA\Get(
     *     path="/voting",
     *     security={{ "passport": {"*"} }},
     *     operationId="get-voting",
     *     tags={"Top"},
     *     summary="Get auth user votes",
     *     @OA\Parameter(name="X-Localization", in="header", required=true, @OA\Schema(type="string"), description="Lang code", example="en"),
     *     @OA\Response(
     *          response="200",
     *          description="**OK** Votes list",
     *          @OA\JsonContent(ref="#/components/schemas/VoteResource")
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
    public function show()
    {
        return $this->sendResponse(VoteResource::collection(\Auth::user()->getVotes()->paginate(20)), __('Votes list'));
    }

    /**
     * @OA\Post(
     *     path="/voting",
     *     security={{ "passport": {"*"} }},
     *     operationId="vote-photo",
     *     tags={"Top"},
     *     summary="Vote for a photo",
     *     @OA\Parameter(name="X-Localization", in="header", required=true, @OA\Schema(type="string"), description="Lang code", example="en"),
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/VotingStoreRequest")),
     *     @OA\Response(
     *          response="200",
     *          description="**OK** Voted successfully",
     *          @OA\JsonContent(ref="#/components/schemas/TopResource")
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

            Voting::create([
                'winning_photo' => $request->input('winning_photo'),
                'loser_photo' => $request->input('loser_photo'),
                'voter_id' => \Auth::id(),
            ]);

            DB::commit();
        } catch (Throwable $e) {
            DB::rollback();
            Log::debug($e->getMessage());
            return $this->sendError(__('Something wrong.'), JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        \Auth::user()->depositCoin();
        broadcast(new DepositCoinsEvent(\Auth::user()->getWallet('coins-wallet')->balance, (int) \Auth::id()));

        return $this->sendResponse(TopResource::collection(Media::whereIn('id', [
            $request->input('winning_photo'),
            $request->input('loser_photo')
        ])->get()), __('Voted successfully'));
    }
}
