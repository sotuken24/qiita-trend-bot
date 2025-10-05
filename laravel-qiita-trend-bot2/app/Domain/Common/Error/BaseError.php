<?php

namespace App\Http\Usecases\Common\Error;

use Exception;

class BaseError extends Exception
{
    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'message' => $this->message,
        ];
    }
}
