<?php
namespace App\Services;

use App\Repositories\TokenRepository;

/**
 * Класс сервиса push-токена
 */
class TokenService
{
    /**
     * Токен-репозиторий.
     *
     * @var $tokenRepository
     */
    protected $tokenRepository;

    /**
     * Констурктор токен-сервиса.
     *
     * @param TokenRepository $tokenRepository
     */
    public function __construct(TokenRepository $tokenRepository)
    {
        $this->tokenRepository = $tokenRepository;
    }
}
