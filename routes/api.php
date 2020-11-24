<?php

/** @var \Laravel\Lumen\Routing\Router $router */
$router->get('/', function () use ($router) {
    return response()
        ->json(['status' => 'OK', 'kolesa' => 'hello']);
});
