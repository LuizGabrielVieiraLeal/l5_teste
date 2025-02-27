<?php

use App\Filters\AdminFilter;
use App\Filters\AuthFilter;
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->group('api', function ($routes) {
    $routes->post('auth/login', 'AuthController::login');
    $routes->post('auth/register', 'AuthController::register');
    $routes->get('products/', 'ProductsController::index');
    $routes->get('products/(:num)', 'ProductsController::show/$1');
});

$routes->group('api', ['filter' => AuthFilter::class], function ($routes) {
    $routes->put('users/(:num)', 'UsersController::update/$1');
    $routes->delete('users/(:num)', 'UsersController::delete/$1');
    $routes->get('orders/', 'OrdersController::index');
    $routes->get('orders/(:num)', 'OrdersController::show/$1');
    $routes->post('orders/', 'OrdersController::create');
    $routes->put('orders/(:num)', 'OrdersController::update/$1');
});

$routes->group('api', ['filter' => [AuthFilter::class, AdminFilter::class]], function ($routes) {
    $routes->post('products/', 'ProductsController::create');
    $routes->put('products/(:num)', 'ProductsController::update/$1');
    $routes->delete('products/(:num)', 'ProductsController::delete/$1');
});