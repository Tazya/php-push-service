<?php
namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

/**
 * Класс обработки исключений
 */
class Handler extends ExceptionHandler
{
    /**
     * Список исключений, которые не будут обработаны
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
        MethodNotAllowedHttpException::class,
        NotFoundHttpException::class,
    ];

    /**
     * Рендер исключений и ошибок в HTTP ответ
     *
     * @param  Request      $request
     * @param  Throwable    $exception
     * @return JsonResponse
     *
     * @throws Throwable
     */
    public function render($request, Throwable $exception): JsonResponse
    {
        if ($exception instanceof NotFoundHttpException) {
            return response()
                ->json(['status' => 'error', 'message' => 'Маршрут не найден'], 404);
        }

        if ($exception instanceof MethodNotAllowedHttpException) {
            return response()
                ->json(['status' => 'error', 'message' => 'Неверный метод'], 405);
        }

        if ($exception instanceof ValidationException) {
            return $this->renderValidatorErrors($exception);
        }

        return response()
            ->json(['status' => 'error', 'message' => $exception->getMessage()], 500);
    }

    /**
     * Рендер ошибок валидации в формате json в HTTP ответ
     *
     * @param  Throwable    $exception
     * @return JsonResponse
     *
     * @throws Throwable
     */
    protected function renderValidatorErrors(Throwable $exception): JsonResponse
    {
        $errors            = collect($exception->errors());
        $validatorMessages = $errors
            ->map(static function ($inputErrors) {
                $firstInputError = $inputErrors[0];
                [$shortMessage]  = explode('|', $firstInputError);

                return $shortMessage;
            });

        $firstError         = $errors->first();
        $firstErrorMessages = explode('|', $firstError[0]);
        $fullErrorMessage   = $firstErrorMessages[1] ?? $firstErrorMessages[0];

        $preparedErrors = [
            'status'     => 'error',
            'validation' => $validatorMessages,
            'message'    => $fullErrorMessage,
        ];

        return response()
            ->json($preparedErrors, 400);
    }
}
