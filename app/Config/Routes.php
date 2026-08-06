<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');

$routes->group('admin', static function ($routes): void {
    $routes->get('login', 'Admin\\AuthController::index', ['as' => 'admin.login']);
    $routes->post('login', 'Admin\\AuthController::login', ['filter' => 'csrf']);
    $routes->post('logout', 'Admin\\AuthController::logout', ['filter' => ['adminAuth', 'csrf']]);
    $routes->get('dashboard', 'Admin\\DashboardController::index', ['filter' => 'adminAuth', 'as' => 'admin.dashboard']);

    $routes->get('materials', 'Admin\\MaterialController::index', ['filter' => ['adminAuth', 'adminRole'], 'as' => 'admin.materials']);
    $routes->get('materials/create', 'Admin\\MaterialController::create', ['filter' => ['adminAuth', 'adminRole']]);
    $routes->post('materials', 'Admin\\MaterialController::store', ['filter' => ['adminAuth', 'adminRole', 'csrf']]);
    $routes->get('materials/(:num)/edit', 'Admin\\MaterialController::edit/$1', ['filter' => ['adminAuth', 'adminRole']]);
    $routes->post('materials/(:num)', 'Admin\\MaterialController::update/$1', ['filter' => ['adminAuth', 'adminRole', 'csrf']]);
    $routes->post('materials/(:num)/toggle', 'Admin\\MaterialController::toggle/$1', ['filter' => ['adminAuth', 'adminRole', 'csrf']]);
    $routes->post('materials/(:num)/delete', 'Admin\\MaterialController::delete/$1', ['filter' => ['adminAuth', 'adminRole', 'csrf']]);

    $routes->get('questions', 'Admin\\QuestionController::index', ['filter' => ['adminAuth', 'adminRole'], 'as' => 'admin.questions']);
    $routes->get('questions/create', 'Admin\\QuestionController::create', ['filter' => ['adminAuth', 'adminRole']]);
    $routes->post('questions', 'Admin\\QuestionController::store', ['filter' => ['adminAuth', 'adminRole', 'csrf']]);
    $routes->get('questions/(:num)/edit', 'Admin\\QuestionController::edit/$1', ['filter' => ['adminAuth', 'adminRole']]);
    $routes->post('questions/(:num)', 'Admin\\QuestionController::update/$1', ['filter' => ['adminAuth', 'adminRole', 'csrf']]);
    $routes->post('questions/(:num)/toggle', 'Admin\\QuestionController::toggle/$1', ['filter' => ['adminAuth', 'adminRole', 'csrf']]);
    $routes->post('questions/(:num)/delete', 'Admin\\QuestionController::delete/$1', ['filter' => ['adminAuth', 'adminRole', 'csrf']]);

    $routes->get('quizzes', 'Admin\\QuizController::index', ['filter' => 'adminAuth', 'as' => 'admin.quizzes']);
    $routes->get('quizzes/(:num)', 'Admin\\QuizController::show/$1', ['filter' => 'adminAuth', 'as' => 'admin.quiz.detail']);

    $routes->get('sessions', 'Admin\\QuizSessionController::index', ['filter' => 'adminAuth', 'as' => 'admin.sessions']);
    $routes->get('sessions/(:num)', 'Admin\\QuizSessionController::show/$1', ['filter' => 'adminAuth', 'as' => 'admin.session.detail']);

    $routes->get('participants', 'Admin\\ParticipantController::index', ['filter' => 'adminAuth', 'as' => 'admin.participants']);
    $routes->get('participants/(:num)', 'Admin\\ParticipantController::show/$1', ['filter' => 'adminAuth', 'as' => 'admin.participant.detail']);
});
