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
        $messages = [
            'required' => 'required|Переданы не все данные',
        ];

        $data = $this->validate($request, [
            'device_id' => 'required',
            'token'     => 'required',
            'os'        => 'required',
            'version'   => 'required',
        ], $messages);

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
        $messages = [
            'user_id.required_without'   => 'required|Не передан идентификатор пользователя',
            'device_id.required_without' => 'required|Не передан идентификатор устройства',
        ];

        $data = $this->validate($request, [
            'user_id'   => 'required_without:device_id',
            'device_id' => 'required_without:user_id',
        ], $messages);

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
        $messages = [
            'token.required'   => 'required|Не указан token для удаления',
        ];

        $data = $this->validate($request, [
            'token'   => 'required',
        ], $messages);

        return response()
            ->json(['status' => 'OK', 'kolesa' => 'hello']);
    }
}
