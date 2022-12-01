<?php

namespace App\Http\Controllers\Api\User;

use App\helpers;
use Throwable;

use App\Http\Controllers\Api\ApiBaseController;

use App\Http\Requests\Api\User\PhotoDestroyRequest;
use App\Http\Requests\Api\User\PhotoIdRequest;
use App\Http\Requests\Api\User\PhotoStoreRequest;
use App\Http\Requests\Api\User\PhotoUpdateRequest;

use App\Http\Resources\User\PhotoResource;

use App\Models\Media;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PhotoController extends ApiBaseController
{
    /**
     * @OA\Post(
     *     path="/user/photos",
     *     security={{ "passport": {"*"} }},
     *     operationId="store-user-photo",
     *     tags={"User"},
     *     summary="Load user photo",
     *     @OA\Parameter(name="X-Localization", in="header", required=true, @OA\Schema(type="string"), description="Lang code", example="en"),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(@OA\Property(description="Photo to upload", property="photo", type="file"), required={"photo"})
     *         )
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="**OK** Photo uploaded successfully",
     *          @OA\JsonContent(ref="#/components/schemas/PhotoResource")
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
    public function store(PhotoStoreRequest $request)
    {
        $user = \Auth::user();
        $photo = $request->file('photo');

        if($user->hasPhotos()) {
            $main = false;
        } else {
            $main = true;
            // $user->settings()->update(['matches' => true]);
        }

        $media = $user->addMedia($photo)
             ->usingFileName(helpers::getImageNameByFileName($photo))
             ->withCustomProperties([
                    'main' => $main,
                    'top' => true,
                ])
             ->toMediaCollection($user->mediaCollection);

        return $this->sendResponse(PhotoResource::make($media), __('Photo uploaded successfully'));
    }

    /**
     * @OA\Delete(
     *     path="/user/photos/{photo}",
     *     security={{ "passport": {"*"} }},
     *     operationId="delete-user-photo",
     *     tags={"User"},
     *     summary="Delete user photos",
     *     @OA\Parameter(name="X-Localization", in="header", required=true, @OA\Schema(type="string"), description="Lang code", example="en"),
     *     @OA\Parameter(name="photo", in="path", required=true, @OA\Schema(type="string"), description="photo ID", example=1),
     *     @OA\Response(
     *          response="200",
     *          description="**OK** Photo deleted successfully",
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
    public function destroy(Media $photo)
    {
        try {
            DB::beginTransaction();

            $photo->delete();

            $user = \Auth::user();
            if ($user->hasPhotos() && !$user->hasMainPhoto())
                $user->getFirstMedia($user->mediaCollection)
                    ->setCustomProperty('main', true)
                    ->save();

            if (!$user->hasPhotos()) $user->settings()->update(['matches' => false]);

            DB::commit();

            return $this->sendResponse([], __('Photo deleted successfully'));
        } catch (Throwable $e) {
            DB::rollback();
            Log::debug($e->getMessage());
            return $this->sendError(__('Something wrong.'), JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * @OA\Delete(
     *     path="/user/photos",
     *     security={{ "passport": {"*"} }},
     *     operationId="delete-user-photo-by-array",
     *     tags={"User"},
     *     summary="Delete user photos by array",
     *     @OA\Parameter(name="X-Localization", in="header", required=true, @OA\Schema(type="string"), description="Lang code", example="en"),
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/PhotoDestroyRequest")),
     *     @OA\Response(
     *          response="200",
     *          description="**OK** Photos deleted successfully",
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
    public function destroyArray(PhotoDestroyRequest $request)
    {
        try {
            DB::beginTransaction();

            Media::whereIn('id', $request->input('ids'))->delete();

            $user = \Auth::user();
            if ($user->hasPhotos() && !$user->hasMainPhoto())
                $user->getFirstMedia($user->mediaCollection)
                    ->setCustomProperty('main', true)
                    ->save();

            if (!$user->hasPhotos()) $user->settings()->update(['matches' => false]);

            DB::commit();

            return $this->sendResponse([], __('Photos deleted successfully'));
        } catch (Throwable $e) {
            DB::rollback();
            Log::debug($e->getMessage());
            return $this->sendError(__('Something wrong.'), JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * @OA\Put(
     *     path="/user/photos/{photo}",
     *     security={{ "passport": {"*"} }},
     *     operationId="update-photo",
     *     tags={"User"},
     *     summary="Update photo",
     *     @OA\Parameter(name="X-Localization", in="header", required=true, @OA\Schema(type="string"), description="Lang code", example="en"),
     *     @OA\Parameter(name="photo", in="path", required=true, @OA\Schema(type="string"), description="photo ID", example=1),
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/PhotoUpdateRequest")),
     *     @OA\Response(
     *          response="200",
     *          description="**OK** Photo settings updated successfully",
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
    public function update(Media $photo, PhotoUpdateRequest $request)
    {
        try {
            DB::beginTransaction();

            $photo->setCustomProperty('top', $request->input('top'))
                  ->save();

            if ($photo->getCustomProperty('main') !== $request->input('main'))
                $photo->changeMainStatus($request->input('main'));

            DB::commit();

            return $this->sendResponse([], __('Photo settings updated successfully'));
        } catch (Throwable $e) {
            DB::rollback();
            Log::debug($e->getMessage());
            return $this->sendError(__('Something wrong.'), JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

}
