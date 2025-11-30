<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
//$routes->get('/', 'Home::index');

$routes->group('(:segment)', ['namespace' => 'App\Controllers'], function ($routes) {
    $routes->group('auth', ['namespace' => 'App\Controllers'], function ($routes) {
        $routes->get('login', 'AuthController::loginView/$1', ['as' => 'login-view']);
        $routes->post('login', 'AuthController::login/$1', ['as' => 'login']);
        $routes->get('logout', 'AuthController::logout/$1', ['as' => 'logout']);
        $routes->get('forgot-password', 'AuthController::forgotPasswordView/$1', ['as' => 'forgot-password-view']);
        $routes->post('forgot-password', 'AuthController::forgotPassword/$1', ['as' => 'forgot-password']);
        $routes->get('reset-password', 'AuthController::resetPasswordView/$1', ['as' => 'reset-password-view']);
        $routes->post('reset-password', 'AuthController::resetPassword/$1', ['as' => 'reset-password']);
    });

    $routes->get('dashboard', 'DashboardController::index/$1', ['as' => 'dashboard']);

    $routes->group('users', ['namespace' => 'App\Controllers'], function ($routes) {
        $routes->get('create', 'UserController::createUserView/$1', ['as' => 'create-user-view']);
        $routes->post('create', 'UserController::createUser/$1', ['as' => 'create-user']);
        $routes->get('profile', 'UserController::profileView/$1', ['as' => 'user-profile-view']);
        $routes->post('profile', 'UserController::updateProfile/$1', ['as' => 'update-user-profile']);
        $routes->post('change-password', 'UserController::changePassword/$1', ['as' => 'change-user-password']);
    });

    $routes->group('system', ['namespace' => 'App\Controllers'], function ($routes) {
        $routes->group('package-settings', ['namespace' => 'App\Controllers'], function ($routes) {
            $routes->get('/', 'PackageSettingsController::index/$1', ['as' => 'package-settings']);
            $routes->post('update', 'PackageSettingsController::update/$1', ['as' => 'update-package-settings']);
        });
    });
});

$routes->view('/unauthorized', 'errors/main/unauthorized', ['as' => 'unauthorized']);
$routes->set404Override('App\Controllers\Errors::show404');
