<?php

namespace App\Exceptions;

use Exception;
use Spatie\Permission\Exceptions\UnauthorizedException as SpatieUnauthorizedException;

class PermissionsException extends Exception
{
    /**
     * Create a new instance of the exception.
     *
     * @param string $message
     * @return void
     */
    public function __construct($message = "No tienes permiso para realizar esta acción.") //You do not have permission to perform this action.
    {
        // Call the parent constructor to ensure proper exception handling
        parent::__construct($message);
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function render($request)
    {
        // Check if the request expects a JSON response
        return response()->json([
            'error' => $this->getMessage()
        ], 403);
    }
    /*public function report()
    {
        // Log the exception or perform any other action
        \Log::error($this->getMessage());
    }*/
}
