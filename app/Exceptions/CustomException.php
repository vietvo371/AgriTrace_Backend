<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class CustomException extends Exception
{
    /**
     * Custom Error
     *
     * @var array
     */
    protected $errors = [];

    /**
     * CustomException constructor.
     *
     * @param string         $message  Message
     * @param array          $errors   Error Data
     * @param int            $code     Code
     * @param Throwable|null $previous previous
     *
     * @return void
     */
    public function __construct(string $message = '', $errors = [], int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->errors = $errors;
    }

    /**
     * Get Errors
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    public function render($request)
    {
        return response()->json([
            'errors' => [
                'message' => [$this->getMessage()],
            ],
        ], 422);
    }
}
