<?php

namespace App\Http\Usecases\Responses;

use App\Http\Usecases\Common\DTO\BaseResponse;
use App\Http\Usecases\Common\Error\BaseError;

readonly class QiitaFetchUsecaseResponse extends BaseResponse
{
    /**
     * @param array $result
     * @param BaseError|null $error
     * @param bool $systemErrorFlag
     */
    public function __construct(
        array $result,
        ?BaseError $error,
        bool $systemErrorFlag
    ) {
        if (is_null($result)){
            parent::__construct(
                [],
                $error,
                $systemErrorFlag
            );
            return;
        }
        $data = collect($result)->map(function ($article) {
            return [
                'title' => $article['title'],
                'url' => $article['url'],
            ];
        })->toArray();
        parent::__construct(
            $data,
            $error,
            $systemErrorFlag
        );
    }

    /**
     * @param array $result
     * @return self
     */
    public static function success(array $result): self
    {
        return new self(
            $result,
            null,
            false
        );
    }

    /**
     * @param BaseError $error
     * @return self
     */
    public static function failed(BaseError $error, bool $systemErrorFlag = false): self
    {
        return new self(
            [],
            $error,
            $systemErrorFlag
        );
    }

    public static function criticalError(BaseError $error): self
    {
        return new self(
            [],
            $error,
            true
        );
    }
}
