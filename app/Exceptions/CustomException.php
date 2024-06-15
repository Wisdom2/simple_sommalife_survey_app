<?php
    namespace App\Exceptions;

    use Exception;

class CustomException extends Exception
{
    public function __construct($message, $data = null, $status = null)
    {
        $this->message = $message;
        $this->data = $data;
        $this->status = $status;
    }

    public function render()
    {
        $statusCode = $this->status ??  400;

        return response()->json([
            'status' => $this->status ? $this->status : 400,
            'data' => $this->data,
            'message' => $this->message,
        ], $statusCode);
    }
}
