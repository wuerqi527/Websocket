<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        UserException::class,
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
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        // Api 异常响应
        $isApiRequest = $request->is(['api/*']) || $request->expectsJson();

        if ($isApiRequest && $request->header('x-debug-mode') != 2) {

            $resp = $this->handleApiException($request, $exception);

            return response()->output($resp['message'], $resp['code']);
        }

        return parent::render($request, $exception);
    }

    protected function handleApiException($request, Exception $exception)
    {
        $code = $exception->getCode() ?: -1;
        $message = $exception->getMessage() ?: 'Oops';

        if ($exception instanceof UserException) {
            return compact('code', 'message');
        }

        if ($exception instanceof ModelNotFoundException) {
            $code = -404;
            $className = $exception->getModel();
            $message = property_exists($className, 'modelName') ? $className::$modelName : class_basename($className);
            $message = '操作失败，' . $message . '不存在';
            return compact('code', 'message');
        }

        if ($exception instanceof AuthenticationException) {
            return [
                'code'    => -1000,
                'message' => '令牌不正确，请重新登录',
            ];
        }

        if ($exception instanceof AccessDeniedHttpException) {
            return [
                'code'    => -403,
                'message' => $message,
            ];
        }

        if ($exception instanceof ValidationException) {
            return [
                'code'    => -1,
                'message' => current(array_flatten($exception->errors())),
            ];
        }

        if ($request->header('x-debug-mode') == 1) {
            $message .= ' - ' . $exception->__toString();
        }
        else {
            $exceptionClass = get_class($exception);
            if ($exceptionClass != 'Exception') {
                $message .= ' - ' . $exceptionClass;
            }
        }

        return compact('code', 'message');
    }
}
