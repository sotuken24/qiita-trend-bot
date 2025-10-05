<?php

namespace App\Http\Usecases\Qiita;

use App\Http\Usecases\Common\Error\BaseError;
use App\Services\LineService;
use App\Services\QiitaService;

readonly class QiitaSendAction
{
    public function __construct(
        private QiitaService $qiitaService,
        private LineService $lineService,
    ) {
    }

    /**
     * @param string $tag
     *
     * @return bool
     */
    public function handle(string $tag): bool
    {
        try {
            $articles = $this->qiitaService->getTrendingArticlesByTag($tag);

            if (is_null($articles)) {
                $error = new BaseError('No articles found for the given tag.');
                return false;
            }

            $this->lineService->pushMessage(config('services.line.user_id'), $articles);

            return true;

        } catch (\Exception $e) {
            $error = new BaseError('Failed to fetch articles from Qiita: ' . $e->getMessage());
            return false;
        }
    }
}

{
}
