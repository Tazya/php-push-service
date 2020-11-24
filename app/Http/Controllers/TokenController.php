<?php
namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;

/**
 * Класс обрабатывает запросы к сервису хранения push-токенов
 */
class TokenController extends Controller
{
    /**
     * Создает или обновляет токен
     *
     * @param  Request      $request
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        return response()
            ->json(['status' => 'OK', 'kolesa' => 'hello']);
    }

    /**
     * Запрашивает токены, используя user_id авторизованного пользователя
     * или device_id неавторизованного
     *
     * @param  Request      $request
     * @return JsonResponse
     */
    public function read(Request $request): JsonResponse
    {
        return response()
            ->json(['status' => 'OK', 'kolesa' => 'hello']);
    }

    /**
     * Удаляет токен
     *
     * @param  Request      $request
     * @return JsonResponse
     */
    public function delete(Request $request): JsonResponse
    {
        return response()
            ->json(['status' => 'OK', 'kolesa' => 'hello']);
    }
}
