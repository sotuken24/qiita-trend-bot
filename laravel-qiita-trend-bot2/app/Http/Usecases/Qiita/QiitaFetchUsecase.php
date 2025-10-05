<?php

namespace App\Http\Usecases\Qiita;

use App\Http\Usecases\Common\DTO\BaseResponse;
use App\Http\Usecases\Common\Error\BaseError;
use App\Http\Usecases\Responses\QiitaFetchUsecaseResponse;
use App\Services\QiitaService;

readonly class QiitaFetchUsecase
{
    public function __construct(
        private QiitaService $qiitaService,
    ) {
    }

    /**
     * @param string $tag
     *
     * @return QiitaFetchUsecaseResponse
     */
    public function handle(string $tag): QiitaFetchUsecaseResponse
    {
        try {
            $result = $this->qiitaService->getTrendingArticlesByTag($tag);

            if (is_null($result)) {
                $error = new BaseError('No articles found for the given tag.');
                return QiitaFetchUsecaseResponse::failed($error);
            }

            return QiitaFetchUsecaseResponse::success($result);

        } catch (\Exception $e) {
            $error = new BaseError('Failed to fetch articles from Qiita: ' . $e->getMessage());
            return QiitaFetchUsecaseResponse::failed($error, true);
        }
    }
}
