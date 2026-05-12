<?php

namespace App\Services;

class RunnerException extends \Exception
{
    public array $details;

    public function __construct(string $message, int $code, array $details)
    {
        parent::__construct($message, $code);
        $this->details = $details;
    }
}
