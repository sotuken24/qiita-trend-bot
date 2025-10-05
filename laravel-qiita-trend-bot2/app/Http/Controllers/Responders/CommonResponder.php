<?php

namespace App\Http\Controllers\Responders;

use App\Http\Usecases\Common\DTO\BaseResponse;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
class CommonResponder
{
/**
 * @param BaseResponse $response
 * @return JsonResponse
 */
public function response(BaseResponse $result): JsonResponse
{
    if ($result->isSystemError()) {
        return response()->json([
            'status' => 'false',
            'data' => $result->getError(),
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
    return response()->json([
            'status' => 'true',
            'data' => $result->getData(),
        ]);
    }
}
