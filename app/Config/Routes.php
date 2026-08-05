<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');

$routes->group('admin', static function ($routes): void {
    $routes->get('login', 'Admin\\AuthController::index', ['as' => 'admin.login']);
    $routes->post('login', 'Admin\\AuthController::login', ['filter' => 'csrf']);
    $routes->post('logout', 'Admin\\AuthController::logout', ['filter' => ['adminAuth', 'csrf']]);
    $routes->get('dashboard', 'Admin\\DashboardController::index', ['filter' => 'adminAuth', 'as' => 'admin.dashboard']);

    $routes->get('materials', 'Admin\\MaterialController::index', ['filter' => 'adminAuth', 'as' => 'admin.materials']);
    $routes->get('materials/create', 'Admin\\MaterialController::create', ['filter' => 'adminAuth']);
    $routes->post('materials', 'Admin\\MaterialController::store', ['filter' => ['adminAuth', 'csrf']]);
    $routes->get('materials/(:num)/edit', 'Admin\\MaterialController::edit/$1', ['filter' => 'adminAuth']);
    $routes->post('materials/(:num)', 'Admin\\MaterialController::update/$1', ['filter' => ['adminAuth', 'csrf']]);
    $routes->post('materials/(:num)/toggle', 'Admin\\MaterialController::toggle/$1', ['filter' => ['adminAuth', 'csrf']]);
    $routes->post('materials/(:num)/delete', 'Admin\\MaterialController::delete/$1', ['filter' => ['adminAuth', 'csrf']]);
});
