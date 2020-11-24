<?php
namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            'user_id'   => 'integer',
            'device_id' => 'required',
            'token'     => 'required',
            'os'        => 'required',
            'version'   => 'required',
        ], $messages);

        $isAuthorized   = array_key_exists('user_id', $data);
        $preparedValues = array_merge($data, ['authorized' => $isAuthorized]);

        $token = DB::table('tokens')
            ->where('device_id', $data['device_id'])->first();

        if ($token) {
            DB::table('tokens')
                ->where('device_id', $data['device_id'])
                ->update($preparedValues);
        } else {
            DB::table('tokens')->insert($preparedValues);
        }

        return response()
            ->json(['status' => 'ok']);
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

        if (array_key_exists('user_id', $data)) {
            $tokens = DB::table('tokens')
                ->where('user_id', $data['user_id'])
                ->select('user_id', 'device_id', 'token', 'os', 'version')
                ->get();
        } else {
            $tokens = DB::table('tokens')
                ->where('device_id', $data['device_id'])
                ->select('device_id', 'token', 'os', 'version')
                ->get();
        }

        return response()
            ->json(['status' => 'ok', 'tokens' => $tokens]);
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

        DB::table('tokens')
            ->where('token', $data['token'])
            ->delete();

        return response()
            ->json(['status' => 'ok']);
    }
}
