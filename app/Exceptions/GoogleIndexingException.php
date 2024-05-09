<?php

namespace App\Exceptions;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class GoogleIndexingException extends \Exception
{
    /**
     * @param Request $request
     * @return Response
     */
    public function render(Request $request): Response
    {
        return response()->json([
            'status' => '99999',
            'message' => 'google index exception',
            'data' => [],
        ]);
    }
}
