<?php

namespace App\Http\Actions\Qiita;

use App\Http\Controllers\Controller;
use App\Services\QiitaService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Responders\CommonResponder;
use App\Http\Usecases\Qiita\QiitaFetchUsecase;
use App\Services\LineService;

class QiitaFetchAction extends Controller
{
    /**
     * @param QiitaService $qiitaService
     */
    public function __construct(
        private readonly QiitaService $qiitaService,
        private readonly CommonResponder $responder,
        private readonly QiitaFetchUsecase $usecase,
        private readonly LineService $lineService,
    ){
    }

    /**
     * @param string $tag
     * @return JsonResponse
     */
    public function __invoke(string $tag): JsonResponse
    {
        $result = $this->usecase->handle($tag);

        return $this->responder->response($result);
    }
}
