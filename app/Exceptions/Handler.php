<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        $debug = env('APP_DEBUG', true);

        if (!$debug) {
            switch ($e) {
                case ($e instanceof ModelNotFoundException):
                    return response()->json(['error' => 'Object not found.'], 404);

                case ($e instanceof ExamAlreadySubmittedException):
                    return response()->json(['error' => 'ExamAlreadySubmittedException'], 403);

                case ($e instanceof ExamAttemptsReachedException):
                    return response()->json(['error' => 'ExamAttemptsReachedException'], 403);

                case ($e instanceof ExamAuthorSpoofingException):
                    return response()->json(['error' => 'ExamAuthorSpoofingException'], 403);

                case ($e instanceof ExamNotSubmittedException):
                    return response()->json(['error' => 'ExamNotSubmittedException'], 403);

                case ($e instanceof ExamOutOfPeriodOfAvailabilityException):
                    return response()->json(['error' => 'ExamOutOfPeriodOfAvailabilityException'], 403);

                case ($e instanceof QueryException):
                    return response()->json(['error' => 'An unexpected database error occurred.'], 500);

                default:
                    return parent::render($request, $e);
            }
        }

        return parent::render($request, $e);
    }
}
