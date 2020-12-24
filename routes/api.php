<?php

use Laravel\Lumen\Routing\Router;

/** @var Router $router */
$router->post('save', 'TokenController@create');
$router->post('get', 'TokenController@read');
$router->post('delete', 'TokenController@delete');
