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
     * Сохранить токен
     *
     * @param  array $data
     * @return Token
     */
    public function save(array $data): Token
    {
        // Здесь будет логика сохранения токена;
        return new $this->token;
    }
}
