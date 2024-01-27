<?php

namespace App\Exceptions;

use App\Services\LogErroService;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */

    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Illuminate\Validation\ValidationException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
        NotFoundHttpException::class
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

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

    public function render($request, Throwable $exception)
    {
        try {
            if (
                env('APP_ENV') == 'local'
                || in_array(get_class($exception), $this->dontReport)
            ) {
                return parent::render($request, $exception);
            }

            LogErroService::registrar([
                'message'    => $exception->getMessage(),
                'line'       => $exception->getLine(),
                'file'       => $exception->getFile(),
                'metodo'     => $request->method(),
                'tipo_erro'  => get_class($exception),
                'IP'         => $request->ip(),
                'uri'        => $request->fullUrl(),
		        'body'	     => var_dump(json_encode($request->all())),
                'trace'      => $exception->getTraceAsString()
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
        }

        return parent::render($request, $exception);
    }
}
