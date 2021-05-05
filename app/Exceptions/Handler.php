<?php

namespace App\Exceptions;

use App\Traits\ApiResponser;
use Asm89\Stack\CorsService;
use Dotenv\Exception\ValidationException;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Throwable;

class Handler extends ExceptionHandler
{

    use ApiResponser;
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
       $response = $this->handleException($request, $exception);

       app(CorsService::class)->addActualRequestHeader($response, $request);

       return $response;
    }

    public function handleException($request, Exception $exception)
    {
        if ($exception instanceof  ModelNotFoundException) {
            $modelName = strtolower(class_basename($exception->getModel()));
            return $this->errorResponse("No existe ningun {$modelName} con este identificador", 404);
         }

        if ($exception instanceof AuthenticationException) {
            return $this->unauthenticated($request,$exception);
        }

        if ($exception instanceof AuthorizationException) {
            return $this->errorResponse($exception->getMessage(), 403);
        }

        if ($exception instanceof MethodNotAllowedHttpException) {
             return $this->errorResponse('Este metodo para los request es invalido', 405);
        }

        if ($exception instanceof HttpException) {
             return $this->errorResponse($exception->getMessage(), $exception->getMessage());
        }

        if ($exception instanceof NotFoundHttpException) {
             return $this->errorResponse('Este url no puede ser encontrado', 404);
        }

        if ($exception instanceof  QueryException) {
            $errorCode = $exception->errorInfo[1];

            if ($errorCode == 1451) {
                return $this->errorResponse('No se puede remover este recurso permanentemente. Esta asociado con otro recurso', 409);
            }
        }

        if ($exception instanceof TokenMismatchException) {
             return redirect()->back()->withInput($request->input());
        }

        if (config('app.debug')) {
             return parent::render($request, $exception);
        }

        return $this->errorResponse('Unexpected exception. Try Later', 500);
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($this->isFrontend($request)) {
            return redirect()->guest('login');
        }

        return $this->errorResponse('Unauthenticated.', 401);
    }



    private function isFrontend($request)
    {
        return $request->acceptsHtml() && collect($request->route()->middleware())->contains('web');
    }
}
