<?php

namespace App\Response;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Formato único de respuesta para todas las APIs (intranet, landing, mobile):
 * { success, status, message, data?, errors? }
 */
class ApiResponse
{
    public static function success(
        mixed $data = null,
        string $message = 'OK',
        int $status = 200,
        bool $includeData = true,
    ): JsonResponse {
        $response = [
            'success' => true,
            'status' => $status,
            'message' => $message,
        ];

        if ($includeData) {
            $response['data'] = $data;
        }

        return response()->json($response, $status, [], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    public static function error(
        string $message = 'Error',
        int $status = 400,
        mixed $errors = null,
    ): JsonResponse {
        $response = [
            'success' => false,
            'status' => $status,
            'message' => $message,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $status, [], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Respuesta paginada. $items son los registros ya transformados por un Resource:
     * XResource::collection($paginator)->resolve()
     */
    public static function paginated(
        LengthAwarePaginator $paginator,
        array $items,
        string $message = 'OK',
        int $status = 200,
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'status' => $status,
            'message' => $message,
            'data' => $items,
            'links' => [
                'first' => $paginator->url(1),
                'last' => $paginator->url($paginator->lastPage()),
                'prev' => $paginator->previousPageUrl(),
                'next' => $paginator->nextPageUrl(),
            ],
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'from' => $paginator->firstItem(),
                'last_page' => $paginator->lastPage(),
                'path' => $paginator->path(),
                'per_page' => $paginator->perPage(),
                'to' => $paginator->lastItem(),
                'total' => $paginator->total(),
            ],
        ], $status, [], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
}
