<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiBaseController;
use App\Http\Requests\Api\Admin\User\DestroyRequest;
use App\Http\Requests\Api\Admin\User\StoreRequest;
use App\Http\Requests\Api\Admin\User\UpdateRequest;

use App\Http\Resources\Admin\User\IndexResource;
use App\Http\Resources\Admin\User\ShowResource;

use App\Models\Users\User;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use Throwable;
use App\helpers;

class AdminUsersController extends ApiBaseController
{
    /**
     * @OA\Get(
     *     path="/admin/users",
     *     security={{ "passport": {"*"} }},
     *     operationId="admin-users-list",
     *     tags={"Admin"},
     *     summary="Get user's list",
     *     @OA\Parameter(name="X-Localization", in="header", required=true, @OA\Schema(type="string"), description="Lang code", example="en"),
     *     @OA\Response(
     *          response="200",
     *          description="**OK** Users list",
     *          @OA\JsonContent(ref="#/components/schemas/AdminUserIndexResource")
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
        $users = User::get();

        return $this->sendResponse(IndexResource::collection($users), __('Users list'));
    }

    /**
     * @OA\Post(
     *     path="/admin/users",
     *     security={{ "passport": {"*"} }},
     *     operationId="admin-create-user",
     *     tags={"Admin"},
     *     summary="Create user",
     *     @OA\Parameter(name="X-Localization", in="header", required=true, @OA\Schema(type="string"), description="Lang code", example="en"),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(ref="#/components/schemas/AdninUserStoreRequest")
     *         )
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="**OK** User successfully registered",
     *          @OA\JsonContent(ref="#/components/schemas/AdminUserShowResource")
     *     ),
     *     @OA\Response(
     *          response="401",
     *          description="**Unauthorized** Invalid credentials.",
     *     ),
     *     @OA\Response(
     *          response="422",
     *          description="**Unprocessable Entity** Required fields are missing or cannot be processed.",
     *     ),
     *     @OA\Response(
     *          response="500",
     *          description="**Server Errors**",
     *     ),
     * )
     *
     * @return JsonResponse
     */
    public function store(StoreRequest $request)
    {
        try {
            DB::beginTransaction();

                $user = User::create($request->all());

                if ($request->has('email')) $user->markEmailAsVerified();
                if ($request->has('phone')) $user->markPhoneAsVerified();
                if ($request->has('photo')) $user->addMedia($request->file('photo'))
                                                 ->usingFileName(helpers::getImageNameByFileName($request->file('photo')))
                                                 ->withCustomProperties([
                                                        'main' => true,
                                                        'top' => true,
                                                    ])
                                                 ->toMediaCollection($user->mediaCollection);

            DB::commit();
        } catch (Throwable $e) {
            DB::rollback();
            Log::debug($e->getMessage());
            return $this->sendError(__('Something wrong.'), JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
        return $this->sendResponse(ShowResource::make($user), __('User successfully registered'));
    }

    /**
     * @OA\Get(
     *     path="/admin/users/{user}",
     *     security={{ "passport": {"*"} }},
     *     operationId="admin-user-data",
     *     tags={"Admin"},
     *     summary="Get user data by ID",
     *     @OA\Parameter(name="X-Localization", in="header", required=true, @OA\Schema(type="string"), description="Lang code", example="en"),
     *     @OA\Parameter(name="user", in="path", required=true, @OA\Schema(type="integer"), description="User id", example=1),
     *     @OA\Response(
     *          response="200",
     *          description="**OK** User data",
     *          @OA\JsonContent(ref="#/components/schemas/AdminUserShowResource")
     *     ),
     *     @OA\Response(
     *          response="401",
     *          description="**Unauthorized** Invalid credentials.",
     *     ),
     * )
     *
     * @return JsonResponse
     */
    public function show(User $user)
    {
        return $this->sendResponse(ShowResource::make($user), __('User data'));
    }

    /**
     * @OA\Put(
     *     path="/admin/users/{user}",
     *     security={{ "passport": {"*"} }},
     *     operationId="admin-update-user",
     *     tags={"Admin"},
     *     summary="Update user",
     *     @OA\Parameter(name="X-Localization", in="header", required=true, @OA\Schema(type="string"), description="Lang code", example="en"),
     *     @OA\Parameter(name="user", in="path", required=true, @OA\Schema(type="integer"), description="User id", example=1),
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/AdminUserUpdateRequest")),
     *     @OA\Response(
     *          response="200",
     *          description="**OK** User data successfully updated",
     *          @OA\JsonContent(ref="#/components/schemas/AdminUserShowResource")
     *     ),
     *     @OA\Response(
     *          response="401",
     *          description="**Unauthorized** Invalid credentials.",
     *     ),
     *     @OA\Response(
     *          response="422",
     *          description="**Unprocessable Entity** Required fields are missing or cannot be processed.",
     *     ),
     *     @OA\Response(
     *          response="500",
     *          description="**Server Errors**",
     *     ),
     * )
     *
     * @return JsonResponse
     */
    public function update(UpdateRequest $request, User $user)
    {
        $user->update($request->all());

        if ($request->has('delete_photos')) {
            Media::whereIn('id', $request->input('delete_photos'))->delete();

            if ($user->hasPhotos() && !$user->hasMainPhoto())
            $user->getFirstMedia($user->mediaCollection)
                ->setCustomProperty('main', true)
                ->save();
        }

        return $this->sendResponse(ShowResource::make($user), __('User data successfully updated'));
    }

    /**
     * @OA\Delete(
     *     path="/admin/users",
     *     security={{ "passport": {"*"} }},
     *     operationId="admin-delete-user",
     *     tags={"Admin"},
     *     summary="Delete users",
     *     @OA\Parameter(name="X-Localization", in="header", required=true, @OA\Schema(type="string"), description="Lang code", example="en"),
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/AdninUserDestroyRequest")),
     *     @OA\Response(
     *          response="200",
     *          description="**OK** Users successfully deleted",
     *     ),
     *     @OA\Response(
     *          response="401",
     *          description="**Unauthorized** Invalid credentials.",
     *     ),
     *     @OA\Response(
     *          response="500",
     *          description="**Server Errors**",
     *     ),
     * )
     *
     * @return JsonResponse
     */
    public function destroy(DestroyRequest $request)
    {
        try {
            User::whereIn('id', $request->input('ids'))->delete();

            return $this->sendResponse([], 'Users successfully deleted');
        } catch (\Throwable $e) {
            Log::debug($e->getMessage());

            return $this->sendError($e->getMessage());
        }
    }
}
