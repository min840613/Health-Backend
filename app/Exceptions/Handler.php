<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Http\Response;
use Log;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Session\TokenMismatchException;
use App\Exceptions\MemberErrorException;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

     /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        AuthenticationException::class,
        NotFoundHttpException::class,
        ValidationException::class,
        MethodNotAllowedHttpException::class,
        TokenMismatchException::class,
        \JsonException::class,
        \Tvbs\Member\Exception\MemberException::class,
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * @param Throwable $exception
     */
    public function report(Throwable $exception)
    {
        if ($this->shouldntReport($exception)) {
            Log::warning($exception);
            return;
        }

        Log::debug('request', ['request' => request()]);
        Log::alert($exception);
    }

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, $exception)
    {
        // 檢查是否為特定例外
        if ($exception instanceof \Tvbs\Member\Exception\MemberException) {
            return MemberErrorException::renderMemberException($exception);
        }

        if ($exception instanceof \JsonException) {
            return response()->json([
                'status' => '99999',
                'message' => $exception->getMessage(),
                'data' => [],
            ], Response::HTTP_BAD_REQUEST);
        }

        if (!$request->is('api/*')) {
            return parent::render($request, $exception);
        }

        if ($exception instanceof NotFoundHttpException || $exception instanceof MethodNotAllowedHttpException) {
            return response()->json([
                'status' => '99999',
                'message' => 'route or method error.',
                'data' => [],
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'status' => '99999',
            'message' => 'server error.',
            'data' => [],
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

}
