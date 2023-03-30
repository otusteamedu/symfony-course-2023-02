<?php

namespace App\Controller\Common;

class ErrorResponse
{
    public bool $success = false;

    /**
     * @param Error[] $errors
     */
    public function __construct(
        public array $errors,
    ) {
    }
}
