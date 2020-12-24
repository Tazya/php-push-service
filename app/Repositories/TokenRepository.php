<?php
namespace App\Repositories;

use App\Models\Token;

/**
 * Класс репозитория push-токена
 */
class TokenRepository
{
    /**
     * Модель токена
     *
     * @var Token
     */
    protected $token;

    /**
     * Конструктор токен-репозитория
     *
     * @param Token $token
     */
    public function __construct(Token $token)
    {
        $this->token = $token;
    }

    /**
     * Сохраняет токен
     *
     * @param  array $data
     * @return Token
     */
    public function save(array $data): Token
    {
        $token = $this->token->where('device_id', $data['device_id'])->first() ?? new $this->token;

        $token->user_id   = $data['user_id'] ?? null;
        $token->device_id = $data['device_id'];
        $token->token     = $data['token'];
        $token->os        = $data['os'];
        $token->version   = $data['version'];

        $token->save();

        return $token->fresh();
    }

    /**
     * Получает токены по признаку пользователя или устройства
     *
     * @param  array $data
     * @return array
     */
    public function get(array $data): array
    {
        if (array_key_exists('user_id', $data)) {
            return $this
                ->token
                ->where('user_id', $data['user_id'])
                ->get()
                ->toArray();
        }

        return $this
            ->token
            ->where('device_id', $data['device_id'])
            ->select('device_id', 'token', 'os', 'version')
            ->get()
            ->toArray();
    }

    /**
     * Удаляет токен
     *
     * @param  string $tokenId
     * @return array
     */
    public function delete(string $tokenId): array
    {
        $token     = $this->token->where('token', $tokenId)->get()->first();
        $tokenData = $token->toArray();

        $token->delete();

        return $tokenData;
    }
}
