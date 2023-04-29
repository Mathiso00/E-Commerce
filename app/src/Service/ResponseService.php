<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\JsonResponse;

class ResponseService
{

    public function __construct()
    {
    }

    public function returnErrorMessage(string $message, int $status): JsonResponse
    {
        return new JsonResponse(["error" => $message], $status);
    }

    public function returnStringMessage(string $message, int $status): JsonResponse
    {
        return new JsonResponse(["message" => $message], $status);
    }
}
