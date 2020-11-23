<?php
// phpcs:ignoreFile

use Codeception\Example;

/**
 * Тестирование API
 */
class ApiCest
{
    /**
     * Выполняется перед запуском теста
     *
     * @param \ApiTester $I
     */
    public function _before(ApiTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
    }

    /**
     * Проверка сохранения токенов с правильными данными
     *
     * @dataProvider saveTokensDataProvider
     *
     * @param \ApiTester           $I
     * @param \Codeception\Example $example
     */
    public function saveTokenPositiveTest(ApiTester $I, Example $example): void
    {
        $I->sendPost('/save', $example['token']);
        $I->seeResponseCodeIsSuccessful();
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['status' => 'ok']);
    }

    /**
     * Проверка валидации токенов с неправильными данными при сохранении
     *
     * @dataProvider saveBadTokensDataProvider
     *
     * @param \ApiTester           $I
     * @param \Codeception\Example $example
     */
    public function saveTokenNegativeTest(ApiTester $I, Example $example): void
    {
        $I->sendPost('/save', $example['token']);
        $I->seeResponseCodeIsClientError();
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['status' => 'error', 'validation' => $example['validation']]);
        $I->seeResponseMatchesJsonType(['message' => 'string:!empty']);

        foreach ($example['token'] as $field => $value) {
            $I->dontSeeResponseContainsJson(['validation' => [$field => 'required']]);
        }
    }

    /**
     * Проверка получения токенов
     *
     * @dataProvider getTokensDataProvider
     *
     * @param \ApiTester           $I
     * @param \Codeception\Example $example
     */
    public function getTokensPositiveTest(ApiTester $I, Example $example): void
    {
        foreach ($example['tokens'] as $token) {
            $I->sendPost('/save', $token);
        }

        $I->sendPost('/get', $example['user']);
        $I->seeResponseCodeIsSuccessful();
        $I->seeResponseIsJson();

        foreach ($example['fetched'] as $token) {
            $I->seeResponseContainsJson(['token' => $token]);
        }

        foreach ($example['ignored'] as $token) {
            $I->dontSeeResponseContainsJson(['token' => $token]);
        }
    }

    /**
     * Проверят валидацию идентификаторов пользователя при получении токенов
     *
     * @param \ApiTester $I
     */
    public function getTokensNegativeTest(ApiTester $I): void
    {
        $I->sendPost('/get');
        $I->seeResponseCodeIsClientError();
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['user_id' => 'required', 'device_id' => 'required']);
        $I->seeResponseMatchesJsonType(['message' => 'string:!empty']);
    }

    /**
     * Проверка удаления токенов
     *
     * @dataProvider deleteTokenDataProvider
     *
     * @param \ApiTester           $I
     * @param \Codeception\Example $example
     */
    public function deleteTokenPositiveTest(ApiTester $I, Example $example): void
    {
        $token   = $example['token'];
        $tokenId = ['token' => $token['token']];

        $I->sendPost('/save', $token);

        $I->sendPost('/get', $example['user']);
        $I->seeResponseContainsJson($tokenId);

        $I->sendPost('/delete', $tokenId);
        $I->seeResponseCodeIsSuccessful();
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['status' => 'ok']);

        $I->sendPost('/get', $example['user']);
        $I->dontSeeResponseContainsJson($tokenId);
    }

    /**
     * Проверяет валидацию идентификатора токена при удалении
     *
     * @param \ApiTester $I
     */
    public function deleteTokenNegativeTest(ApiTester $I): void
    {
        $I->sendPost('/delete');
        $I->seeResponseCodeIsClientError();
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType(['message' => 'string:!empty']);
        $I->seeResponseContainsJson([
            'status'     => 'error',
            'validation' => [
                'token' => 'required',
            ],
        ]);
    }

    /**
     * Провайдер данных для проверки сохранения токенов
     *
     * @return array
     */
    protected function saveTokensDataProvider(): array
    {
        return [
            [
                'token' => [
                    'user_id'   => 1,
                    'device_id' => 'device-1',
                    'token'     => 'token-1',
                    'os'        => 'ios',
                    'version'   => '4.3.12',
                ],
            ],
            [
                'token' => [
                    'user_id'   => 2,
                    'device_id' => 'device-2',
                    'token'     => 'token-2',
                    'os'        => 'android',
                    'version'   => '4.4.4',
                ],
            ],
            [
                'token' => [
                    'device_id' => 'device-3',
                    'token'     => 'token-3',
                    'os'        => 'tizen',
                    'version'   => '3.0.1',
                ],
            ],
        ];
    }

    /**
     * Провайдер данных для проверки валидации при сохранении
     *
     * @return array
     */
    protected function saveBadTokensDataProvider(): array
    {
        return [
            [
                'token'      => [
                    'device_id' => 'device-1',
                    'os'        => 'ios',
                    'version'   => '4.3.12',
                ],
                'validation' => [
                    'token' => 'required',
                ],
            ],
            [
                'token'      => [
                    'user_id' => 2,
                    'token'   => 'token-2',
                    'os'      => 'android',
                    'version' => '4.4.4',
                ],
                'validation' => [
                    'device_id' => 'required',
                ],
            ],
            [
                'token'      => [],
                'validation' => [
                    'device_id' => 'required',
                    'token'     => 'required',
                    'os'        => 'required',
                    'version'   => 'required',
                ],
            ],
        ];
    }

    /**
     * Провайдер данных для проверки получения токенов
     *
     * @return array
     */
    protected function getTokensDataProvider(): array
    {
        return [
            [
                'user'    => [
                    'user_id' => 8888,
                ],
                'fetched' => ['token-8888'],
                'ignored' => ['token-anonymous-8888', 'token-9999'],
                'tokens'  => [
                    [
                        'device_id' => 'device-8888',
                        'token'     => 'token-anonymous-8888',
                        'os'        => 'ios',
                        'version'   => '4.3.12',
                    ],
                    [
                        'user_id'   => 8888,
                        'device_id' => 'device-8888',
                        'token'     => 'token-8888',
                        'os'        => 'ios',
                        'version'   => '4.3.12',
                    ],
                    [
                        'user_id'   => 9999,
                        'device_id' => 'device-9999',
                        'token'     => 'token-9999',
                        'os'        => 'android',
                        'version'   => '3.5.6',
                    ],
                ],
            ],
            [
                'user'    => [
                    'user_id' => 101010,
                ],
                'fetched' => ['token-01-101010', 'token-02-101010'],
                'ignored' => ['token-9999'],
                'tokens'  => [
                    [
                        'user_id'   => 101010,
                        'device_id' => 'device-01-101010',
                        'token'     => 'token-01-101010',
                        'os'        => 'android',
                        'version'   => '5.6.7',
                    ],
                    [
                        'user_id'   => 101010,
                        'device_id' => 'device-02-101010',
                        'token'     => 'token-02-101010',
                        'os'        => 'ios',
                        'version'   => '4.3.12',
                    ],
                    [
                        'user_id'   => 9999,
                        'device_id' => 'device-9999',
                        'token'     => 'token-9999',
                        'os'        => 'android',
                        'version'   => '3.5.6',
                    ],
                ],
            ],
            [
                'user'    => [
                    'device_id' => 'device-007',
                ],
                'fetched' => ['token-02-007'],
                'ignored' => ['token-01-007'],
                'tokens'  => [
                    [
                        'device_id' => 'device-007',
                        'token'     => 'token-01-007',
                        'os'        => 'android',
                        'version'   => '5.6.7',
                    ],
                    [
                        'device_id' => 'device-007',
                        'token'     => 'token-02-007',
                        'os'        => 'android',
                        'version'   => '5.6.7',
                    ],
                    [
                        'user_id'   => 9999,
                        'device_id' => 'device-9999',
                        'token'     => 'token-9999',
                        'os'        => 'android',
                        'version'   => '3.5.6',
                    ],
                ],
            ],
        ];
    }

    /**
     * Провайдер данных для проверки удаления токена
     *
     * @return array
     */
    public function deleteTokenDataProvider(): array
    {
        return [
            [
                'token' => [
                    'user_id'   => 12500,
                    'device_id' => 'device-12500',
                    'token'     => 'token-12500',
                    'os'        => 'ios',
                    'version'   => '4.3.12',
                ],
                'user'  => ['user_id' => 12500],
            ],
            [
                'token' => [
                    'user_id'   => 12800,
                    'device_id' => 'device-12800',
                    'token'     => 'token-12800',
                    'os'        => 'android',
                    'version'   => '4.4.4',
                ],
                'user'  => ['user_id' => 12800],
            ],
            [
                'token' => [
                    'device_id' => 'device-13100',
                    'token'     => 'token-13100',
                    'os'        => 'tizen',
                    'version'   => '3.0.1',
                ],
                'user'  => ['device_id' => 'device-13100'],
            ],
        ];
    }
}
