<?php

namespace App\Http\Traits;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait ApiResponseTrait
{
    /**
     * Success Response.
     */
    public function successResponse(mixed $data, string $message = '', int $statusCode = Response::HTTP_OK): JsonResponse
    {

        if (! $message) {
            $message = Response::$statusTexts[$statusCode];
        }

        $data = [
            'responseCode' => $statusCode,
            'message' => $message,
            'payload' => $data,
        ];

        return new JsonResponse($data, $statusCode);
    }

    /**
     * Error Response.
     */
    public function errorResponse(mixed $data, string $message = '', int $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR): JsonResponse
    {
        if (! $message) {
            $message = Response::$statusTexts[$statusCode];
        }

        $data = [
            'responseCode' => $statusCode,
            'message' => $message,
            'payload' => $data,
        ];

        return new JsonResponse($data, $statusCode);
    }

    /**
     * Response with status code 200.
     */
    public function okResponse(mixed $data, string $message = ''): JsonResponse
    {
        return $this->successResponse($data, $message);
    }

    /**
     * Response with status code 201.
     */
    public function createdResponse(mixed $data, string $message = ''): JsonResponse
    {
        return $this->successResponse($data, $message, Response::HTTP_CREATED);
    }

    /**
     * Response with status code 204.
     */
    public function noContentResponse(string $message): JsonResponse
    {
        return $this->successResponse([], $message, Response::HTTP_NO_CONTENT);
    }

    /**
     * Response with status code 400.
     */
    public function badRequestResponse(mixed $data, string $message = ''): JsonResponse
    {
        return $this->errorResponse($data, $message, Response::HTTP_BAD_REQUEST);
    }

    /**
     * Response with status code 401.
     */
    public function unauthorizedResponse(mixed $data, string $message = ''): JsonResponse
    {
        return $this->errorResponse($data, $message, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Response with status code 403.
     */
    public function forbiddenResponse(mixed $data, string $message = ''): JsonResponse
    {
        return $this->errorResponse($data, $message, Response::HTTP_FORBIDDEN);
    }

    /**
     * Response with status code 404.
     */
    public function notFoundResponse(mixed $data, string $message = ''): JsonResponse
    {
        return $this->errorResponse($data, $message, Response::HTTP_NOT_FOUND);
    }

    /**
     * Response with status code 409.
     */
    public function conflictResponse(mixed $data, string $message = ''): JsonResponse
    {
        return $this->errorResponse($data, $message, Response::HTTP_CONFLICT);
    }

    /**
     * Response with status code 422.
     */
    public function unprocessableResponse(mixed $data, string $message = ''): JsonResponse
    {
        return $this->errorResponse($data, $message, Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * Response with status code 405.
     */
    public function methodNotAllowedResponse(mixed $data, string $message = ''): JsonResponse
    {
        return $this->errorResponse($data, $message, Response::HTTP_METHOD_NOT_ALLOWED);
    }

    /**
     * Response with status code 500.
     */
    public function serverErrorResponse(mixed $data, string $message = ''): JsonResponse
    {
        return $this->errorResponse($data, $message, Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Response with status code 500.
     */
    public function throttleRequestsResponse(mixed $data, string $message = ''): JsonResponse
    {
        return $this->errorResponse($data, $message, Response::HTTP_TOO_MANY_REQUESTS);
    }
}
