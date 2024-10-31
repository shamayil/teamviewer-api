<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        return parent::render($request, $exception);
    }

    /**
     * Convert a validation exception into a JSON response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Validation\ValidationException  $exception
     * @return \Illuminate\Http\JsonResponse
     */
    protected function invalidJson($request, ValidationException $exception)
    {
        return response()->json([
            'status' => 'ERROR',
            'message' => $exception->getMessage(),
            'errors' => $exception->errors(),
            'data' => [],
        ], $exception->status);
    }

    /**
     * Prepare a JSON response for the given exception.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception $e
     * @return \Illuminate\Http\JsonResponse
     */
    protected function prepareJsonResponse($request, Throwable $e)
    {
        return new JsonResponse(
            $this->convertExceptionToArray($e),
            $this->isHttpException($e) ? $e->getStatusCode() : 500,
            $this->isHttpException($e) ? $e->getHeaders() : [],
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
        );
    }

    /**
     * Convert the given exception to an array.
     *
     * @param  \Exception  $e
     * @return array
     */
    protected function convertExceptionToArray(Throwable $e)
    {
        if (config('app.debug')) {
            return [
                'message' => $e->getMessage(),
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => collect($e->getTrace())->map(function ($trace) {
                    return Arr::except($trace, ['args']);
                })->all(),
            ];
        }

        $ErrorMessage = strstr($e->getMessage(), '(SQL:', true);
        if(!$ErrorMessage)
            $ErrorMessage = $e->getMessage();

        $errors = [
            'data' => [],
            'status' => 'ERROR',
            'message' => method_exists($e, 'getStatusCode') && $e->getStatusCode() === 401
                ? 'Unauthorized'
                : 'Something went wrong!',
            'errors' => $ErrorMessage,
        ];

        if ($e instanceof AppException) {

            $errorCollection = [];
            $errorArray = $e->getErrors();

            if (is_array($errorArray)) {
                foreach ($errorArray as $key => $error){

                    $doesExist = Arr::has(
                        $error, 'message'
                    );

                    if($doesExist){
                        $ErrorMessage = strstr($error['message'], '(SQL:', true);
                        if(!$ErrorMessage)
                            $ErrorMessage = $error['message'];

                        $errorCollection[$key] = [
                            'time' =>  $error['time'],
                            'uuid' => $error['uuid'],
                            'message' => $ErrorMessage
                        ];
                    }
                }
            }

            $errors['message'] = $e->getTitle();
            if ($errorCollection == [])
                $errors['errors'] = $e->getErrors();
            else
                $errors['errors'] = $errorCollection;
        }

        return $errors;
    }
}
