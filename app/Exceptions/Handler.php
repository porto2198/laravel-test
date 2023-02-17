<?php

namespace App\Exceptions;

use App\Traits\ApiResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    use ApiResponse;

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
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

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Throwable $e
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $e)
    {
        if ($e instanceof ValidationException) {
            return $this->convertValidationExceptionToResponse($e, $request);
        }

        if ($e instanceof ModelNotFoundException) {
            $model = strtolower(class_basename($e->getModel()));

            return $this->errorResponse("No existe ninguna instancia de {$model} con el id específico.", 404);
        }

        if ($e instanceof AuthenticationException) {
            return $this->unauthenticated($request, $e);
        }

        if ($e instanceof AuthorizationException) {
            return $this->errorResponse('No posee permisos para ejecutar esta acción.', 403);
        }

        if ($e instanceof NotFoundHttpException) {
            return $this->errorResponse('No se encontró la URL específica.', 404);
        }

        if ($e instanceof MethodNotAllowedHttpException) {
            return $this->errorResponse('El método especificado en la petición no es válido.', 405);
        }

        if ($e instanceof HttpException) {
            return $this->errorResponse($e->getMessage(), $e->getStatusCode());
        }

        if ($e instanceof QueryException) {
            $code = $e->errorInfo[1];

            if ($code == 1451) {
                return $this->errorResponse('No se puede eliminar de forma permanente el recurso porque está relacionado con algún otro.', 409);
            }
        }

        if ($e instanceof TokenMismatchException) {
            return redirect()->back()->withInput($request->input());
        }

        if (config('app.debug')) {
            return parent::render($request, $e);
        }

        return $this->errorResponse('Falla inesperada. Intente luego.', 500);
    }

    /**
     * Convert an authentication exception into a response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Auth\AuthenticationException $exception
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return $this->errorResponse('No autenticado.', 401);
    }
}
