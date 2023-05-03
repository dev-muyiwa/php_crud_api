<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;


    public static function onSuccess($data, string $message, int $status = ResponseAlias::HTTP_OK): JsonResponse
    {
        return response()->json([
            "success" => true,
            "data" => $data,
            "message" => $message
        ], $status);
    }

    public static function onError(string $message, object $data = null, int $status = ResponseAlias::HTTP_INTERNAL_SERVER_ERROR): JsonResponse
    {
        return response()->json([
            "success" => false,
            "data" => $data,
            "error" => $message
        ], $status);
    }
}
