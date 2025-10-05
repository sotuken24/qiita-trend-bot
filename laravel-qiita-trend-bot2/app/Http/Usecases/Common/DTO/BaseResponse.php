<?php

namespace App\Http\Usecases\Common\DTO;

use App\Http\Usecases\Common\Error\BaseError;

readonly class BaseResponse
{

    /**
     * BaseResponse contructor.
     * @param array $data
     * @param BaseError|null $error
     * @param bool $systemErrorFlag
     */
    public function __construct(
        protected array $data,
        protected ?BaseError $error,
        protected bool $systemErrorFlag
    ) {
    }

    /**
     * @return bool
     */
    public function hasError(): bool
    {
        return !is_null($this->error);
    }

    /**
     * @return string
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function getErrorMessage(): ?string
    {
        return $this->error->getMessage();
    }

    /**
     * @return array|null
     */
    public function getError(): ?string
    {
        return $this->error;
    }

    /**
     * @return bool
     */
    public function isSystemError(): bool
    {
        return $this->systemErrorFlag;
    }
}
