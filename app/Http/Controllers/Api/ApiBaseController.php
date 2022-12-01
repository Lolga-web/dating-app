<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Support\Collection as BaseCollection;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Class APIBaseController
 * @package App\Http\Controllers\Api
 *
 * @OA\Schema(schema="Response",
 *     @OA\Property(property="success", type="boolean", description="", example="true / false"),
 *     @OA\Property(property="message", type="string", description="", readOnly=true),
 *     @OA\Property(property="errors", type="object", description="", readOnly=true),
 *     @OA\Property(property="data", type="object", description="", example="[]"),
 * )
 */
class ApiBaseController extends Controller
{
    private array $headers = ['Content-Type' => 'application/json; charset=UTF-8', 'charset' => 'utf-8'];

    /**
     * success response method.
     *
     * @param array|int|Collection|BaseCollection|JsonResponse|JsonResource|Model $data
     * @param string $message
     * @param int $httpStatusCode
     *
     * @return JsonResponse
     */
    public function sendResponse($data, string $message = 'OK', int $httpStatusCode = JsonResponse::HTTP_OK): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message,
            'errors' => null,
            'data' => $data
        ];

        # pagination fix
        if ($data instanceof AnonymousResourceCollection) {
            $response = array_merge($response, $data->response()->getData(true));
        }

        return (new JsonResponse($response, $httpStatusCode, $this->headers, JSON_UNESCAPED_UNICODE))
            ->setStatusCode($httpStatusCode, !empty($message) ? $message : null);
    }

    /**
     * return error response.
     *
     * @param string $message
     * @param int $httpStatusCode
     * @param null $errors
     * @param null $data
     *
     * @return JsonResponse
     */
    public function sendError(string $message = 'NOT OK', int $httpStatusCode = JsonResponse::HTTP_INTERNAL_SERVER_ERROR, $errors = null, $data = null): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
            'errors'  => $errors,
            'data' => $data,
        ];

        return (new JsonResponse($response, $httpStatusCode, $this->headers, JSON_UNESCAPED_UNICODE))
            ->setStatusCode($httpStatusCode, !empty($message) ? $message : null);
    }

    /**
     * return error OAuth response.
     *
     * @param $message
     * @param string $errorType
     * @param int $httpStatusCode
     *
     * @return JsonResponse
     */
    public function sendOAuthError($message, string $errorType = '', int $httpStatusCode = JsonResponse::HTTP_BAD_REQUEST): JsonResponse
    {
        $response = [
            'error'             => $errorType,
            'error_description' => $message,
            'message'           => $message,
        ];

        return (new JsonResponse($response, $httpStatusCode, $this->headers, JSON_UNESCAPED_UNICODE))
            ->setStatusCode($httpStatusCode, !empty($message) ? $message : null);
    }
}
