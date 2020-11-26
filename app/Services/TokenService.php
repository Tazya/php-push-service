<?php
namespace App\Services;

use App\Repositories\TokenRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

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

    /**
     * Проверяет данные на валидность и сохраняет push-токен.
     *
     * @param  array               $data
     * @throws ValidationException
     * @return object
     */
    public function saveTokenData(array $data)
    {
        $messages = [
            'required' => 'required|Переданы не все данные',
            'string'   => 'string|Параметр :attribute должен быть строкой',
            'integer'  => 'integer|Параметр :attribute должен быть целым числом',
        ];

        $validator = Validator::make($data, [
            'user_id'   => 'nullable|integer',
            'device_id' => 'required|string',
            'token'     => 'required|string',
            'os'        => 'required|string',
            'version'   => 'required|string',
        ], $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $this->tokenRepository->save($data);
    }

    /**
     * Проверяет данные на валидность и возвращает токены по репозиторию.
     *
     * @param  array               $data
     * @throws ValidationException
     * @return array
     */
    public function getTokens(array $data)
    {
        $messages = [
            'user_id.required_without'   => 'required|Не передан идентификатор пользователя',
            'user_id.integer'            => 'integer|user_id должен быть целым числом',
            'device_id.required_without' => 'required|Не передан идентификатор устройства',
            'device_id.string'           => 'string|device_id должен быть строкой',
        ];

        $validator = Validator::make($data, [
            'user_id'   => 'required_without:device_id|integer',
            'device_id' => 'required_without:user_id|string',
        ], $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $this->tokenRepository->get($data);
    }

    /**
     * Проверяет данные на валидность и удаляет токен.
     *
     * @param  mixed               $token
     * @throws ValidationException
     * @return void
     */
    public function delete($token): void
    {
        $messages = [
            'token.required' => 'required|Не указан token для удаления',
            'token.string'   => 'string|Не указан token для удаления',
            'token.exists'   => 'exists|Токен не найден',
        ];

        $validator = Validator::make(
            ['token' => $token],
            ['token' => 'required|string|exists:tokens,token'],
            $messages
        );

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $this->tokenRepository->delete($token);
    }
}
