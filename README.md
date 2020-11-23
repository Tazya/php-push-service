# Сервис хранения push токенов
Необходимо реализовать сервис, который может собирать токены устройств для push уведомлений. Один токен соответствует только одному устройству, он может меняться со временем. Также устройство имеет уникальный идентификатор, и принято считать, что он не меняется. У одного пользователя может быть несколько устройств. Токен, привязанный к устройству, должен иметь признак отличия для авторизованного и анонимного пользователя

## Требования к технологиям:
 - Docker
 - Nginx
 - Phalcon, Yii, Laravel, Plain PHP
 - MySQL, MongoDB
 - Redis
 - RabbitMQ

## Endpoints/Handlers
Сервис общается с внешним миром в формате json посредством http протокола. Все конечные точки запрашиваются методом POST
### Create/Update - “/save”
Устройство будет запрашивать данную конечную точку при создании и обновлении токена

#### Запрос от авторизованного пользователя
```json
{“user_id”: 1, “device_id”: “f472787a84477aefc2be5617876ab1b2”, “token”: “d5b119095f2c027176c17124e1437029”, “os”: “ios”, “version”: “4.3.12”}
```

#### Запрос от анонимного пользователя
```json
{“device_id”: “f472787a84477aefc2be5617876ab1b2”, “token”: “d5b119095f2c027176c17124e1437029”, “os”: “ios”, “version”: “4.3.12”}
```

#### Успешный ответ
HTTP 200 OK
```json
{“status”: “ok”}
```

#### Ответ с ошибкой из-за неправильного запроса
HTTP 400 Bad Request
```json
{“status”: “error”, “validation”: {“device_id”: “required”}, “message”: “Переданы не все данные”}
```

#### Ответ с ошибкой из-за неполадок сервиса
HTTP 500 Internal Server Error
```json
{“status”: “error”, “message”: “Ошибка при обращении к базе данных”}
```

### Read - “/get”
Сторонние сервисы будут запрашивать данную конечную точку для получения токенов пользователя, которому нужно отправить push уведомления

#### Запрос для авторизованного пользователя
```json
{“user_id”: 1}
```

#### Запрос для неавторизованного пользователя
```json
{“device_id”: “f472787a84477aefc2be5617876ab1b2”}
```

#### Успешный ответ
HTTP 200 OK
```json
{“status”: “ok”, “tokens”: [{“user_id”: 1, “device_id”: “f472787a84477aefc2be5617876ab1b2”, “token”: “d5b119095f2c027176c17124e1437029”, “os”: “ios”, “version”: “4.3.12”}]}
```

#### Ответ с ошибкой из-за неправильного запроса
HTTP 400 Bad Request
```json
{“status”: “error”, “validation”: {“user_id”: “required”, “device_id”: “required”}, “message”: “Не передан идентификатор пользователя”}
```

#### Ответ с ошибкой из-за неполадок сервиса
HTTP 500 Internal Server Error
```json
{“status”: “error”, “message”: “Ошибка при обращении к базе данных”}
```

### Delete - “/delete”
Сторонний сервис будет запрашивать данную конечную точку для удаления токенов при истечении их срока жизни
#### Запрос на удаление
```json
{“token”: “d5b119095f2c027176c17124e1437029”}
```

#### Успешный ответ
HTTP 200 OK
```json
{“status”: “ok”}
```

#### Ответ с ошибкой из-за неправильного запроса
HTTP 400 Bad Request
```json
{“status”: “error”, “validation”: {“token”: “required”}, “message”: “Не указан token для удаления”}
```
#### Ответ с ошибкой из-за неполадок сервиса
HTTP 500 Internal Server Error
```json
{“status”: “error”, “message”: “Ошибка при обращении к базе данных”}
```

### Дополнительное задание
 - Необходимо подключить in-memory базу данных “Redis” для хранения токенов, чтобы повысить скорость ответа
 - Необходимо внедрить другой вид транспорта для общения с сервисом, который будет работать параллельно с HTTP - очереди “RabbitMQ”. Формат сообщений в очереди такой же, как и у запросов по HTTP. Обработка ошибок будет отличаться. В случае ошибок валидации, необходимо переправлять сообщения в очередь с ошибками валидации для ручного анализа и обработки. В случае ошибок, которые могут исчезнуть через время, необходимо делать повторную обработку сообщения - requeue
 
### Полезные команды
 - docker-compose up web - запуск сервера
 - docker-compose up tests - запуск тестов. Все тесты будут пройдены после правильной реализации внешних интерфейсов сервиса. Для тестов необходимо настроить поднятие выбранной вами БД в docker-compose
 - docker-compose run terminal - запуск терминала, где можно установить зависимости и сделать другие необходимые операции на сервере
 
### Полезные ссылки
 - [Composer](https://getcomposer.org/doc/00-intro.md)
 - [Phalcon](https://phalcon.io/en-us), [Yii](https://www.yiiframework.com/), [Laravel](https://laravel.com/)
 - [Настройка среды разработки на примере Docker, nginx, MySQL, Laravel](https://www.digitalocean.com/community/tutorials/how-to-set-up-laravel-nginx-and-mysql-with-docker-compose-ru)
 - [Awesome PHP](https://github.com/ziadoz/awesome-php)
 - Router
   - [Rareloop/router](https://github.com/Rareloop/router)
   - [dannyvankooten/PHP-Router](https://github.com/dannyvankooten/PHP-Router)
   - [miladrahimi/phprouter](https://github.com/miladrahimi/phprouter)
   - etc…
 - Validation
   - [Respect/Validation](https://github.com/Respect/Validation)
   - [vlucas/valitron](https://github.com/vlucas/valitron)
   - [rakit/validation](https://github.com/rakit/validation)
   - etc...
 - Database
   - [SergeyTsalkov/meekrodb](http://SergeyTsalkov/meekrodb)
   - [kodols/php-mysql-library](https://github.com/kodols/php-mysql-library)
   - [ORM](https://ru.wikipedia.org/wiki/%D0%A1%D0%BF%D0%B8%D1%81%D0%BE%D0%BA_ORM-%D0%B1%D0%B8%D0%B1%D0%BB%D0%B8%D0%BE%D1%82%D0%B5%D0%BA#PHP)
   - etc...
 - [Redis & PHP](https://redislabs.com/lp/php-redis/)
 - [RabbitMQ & PHP](https://www.rabbitmq.com/tutorials/tutorial-one-php.html)

P.S: 

 - Используйте только нужное. Ссылки приведены в качестве рекомендаций, а не инструкций к действию

 - Учтите, что тесты покрывают не все кейсы, и если они проходят - не факт, что у вас все реализовано правильно
