<?php
namespace App\Http\Controllers;

use App\Services\TokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;

/**
 * Класс обрабатывает запросы к сервису хранения push-токенов
 */
class TokenController extends Controller
{
    /**
     * Сервис-контейнер push-токена
     *
     * @var TokenService
     */
    protected $tokenService;

    /**
     * PostController Constructor
     *
     * @param TokenService $tokenService
     *
     */
    public function __construct(TokenService $tokenService)
    {
        $this->tokenService = $tokenService;
    }

    /**
     * Создает или обновляет токен
     *
     * @param  Request      $request
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        $data = $request->only([
            'user_id',
            'device_id',
            'token',
            'os',
            'version',
        ]);

        $this->tokenService->saveTokenData($data);

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
        $data = $request->only([
            'user_id',
            'device_id',
        ]);

        $tokens = $this->tokenService->getTokens($data);

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
        $tokenId = $request->input('token');

        $this->tokenService->delete($tokenId);

        return response()
            ->json(['status' => 'ok']);
    }
}
