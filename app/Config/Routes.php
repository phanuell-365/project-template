<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
//$routes->get('/', 'Home::index');

$routes->group('(:segment)', ['namespace' => 'App\Controllers'], function ($routes) {
    $routes->group('auth', ['namespace' => 'App\Controllers'], function ($routes) {
        $routes->get('login', 'AuthController::loginView', ['as' => 'login-view']);
        $routes->post('login', 'AuthController::login', ['as' => 'login']);
        $routes->get('logout', 'AuthController::logout', ['as' => 'logout']);
        $routes->get('forgot-password', 'AuthController::forgotPasswordView', ['as' => 'forgot-password-view']);
        $routes->post('forgot-password', 'AuthController::forgotPassword', ['as' => 'forgot-password']);
        $routes->get('reset-password', 'AuthController::resetPasswordView', ['as' => 'reset-password-view']);
        $routes->post('reset-password', 'AuthController::resetPassword', ['as' => 'reset-password']);
    });

    $routes->get('dashboard', 'DashboardController::index', ['as' => 'dashboard']);

    $routes->group('users', ['namespace' => 'App\Controllers'], function ($routes) {
        $routes->get('create', 'UserController::createUserView', ['as' => 'create-user-view']);
        $routes->post('create', 'UserController::createUser', ['as' => 'create-user']);
        $routes->get('profile', 'UserController::profileView', ['as' => 'user-profile-view']);
        $routes->post('profile', 'UserController::updateProfile', ['as' => 'update-user-profile']);
        $routes->post('change-password', 'UserController::changePassword', ['as' => 'change-user-password']);
    });

    $routes->group('system', ['namespace' => 'App\Controllers'], function ($routes) {
        $routes->group('package-settings', ['namespace' => 'App\Controllers'], function ($routes) {
            $routes->get('/', 'PackageSettingsController::index', ['as' => 'package-settings']);
            $routes->post('create', 'PackageSettingsController::create', ['as' => 'create-package-settings']);
            $routes->put('edit', 'PackageSettingsController::edit', ['as' => 'edit-package-settings']);
        });
    });
});

$routes->view('/unauthorized', 'errors/main/unauthorized', ['as' => 'unauthorized']);
$routes->set404Override('App\Controllers\Errors::show404');
