<?php

use Laravel\Lumen\Routing\Router;

/** @var Router $router */
$router->get('save', 'TokenController@create');
$router->get('get', 'TokenController@read');
$router->get('delete', 'TokenController@delete');
