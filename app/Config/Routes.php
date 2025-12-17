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
            $routes->post('permissions', 'PackageSettingsController::updatePermissions', ['as' => 'update-package-permissions']);
            $routes->get('permissions', 'PackageSettingsController::getPermissions', ['as' => 'get-package-permissions']);
            $routes->delete('delete', 'PackageSettingsController::delete', ['as' => 'delete-package-settings']);
            $routes->group('group-templates', ['namespace' => 'App\Controllers'], function ($routes) {
                $routes->get('create', 'PackageSettingsController::getPackageGroupTemplates', ['as' => 'get-package-group-templates']);
                $routes->post('create', 'PackageSettingsController::savePackageGroupTemplate', ['as' => 'create-package-group-template']);
                $routes->put('edit', 'PackageSettingsController::editPackageGroupTemplate', ['as' => 'edit-package-group-template']);
                $routes->delete('delete', 'PackageSettingsController::deletePackageGroupTemplate', ['as' => 'delete-package-group-template']);
            });
        });

        $routes->group('groups-settings', ['namespace' => 'App\Controllers'], function ($routes) {
            $routes->get('/', 'GroupsSettingsController::index', ['as' => 'groups-settings']);
            $routes->post('create', 'GroupsSettingsController::create', ['as' => 'create-group-settings']);
            $routes->put('edit', 'GroupsSettingsController::edit', ['as' => 'edit-group-settings']);
            $routes->post('permissions', 'GroupsSettingsController::updatePermissions', ['as' => 'update-group-permissions']);
            $routes->get('permissions', 'GroupsSettingsController::getPermissions', ['as' => 'get-group-permissions']);
        });
    });

    $routes->group('organisation', ['namespace' => 'App\Controllers'], function ($routes) {
        $routes->get('create', 'OrganisationController::createOrganizationView', ['as' => 'create-organisation-view']);
        $routes->post('create', 'OrganisationController::createOrganization', ['as' => 'create-organisation']);
        $routes->get('profile', 'OrganisationController::profileView', ['as' => 'organisation-profile-view']);
        $routes->post('profile', 'OrganisationController::updateProfile', ['as' => 'update-organisation-profile']);
        $routes->get('settings', 'SettingsController::generalSettingsView', ['as' => 'general-settings-view']);
        $routes->post('settings', 'SettingsController::saveGeneralSettings', ['as' => 'save-general-settings']);
    });
});

$routes->view('/unauthorized', 'errors/main/unauthorized', ['as' => 'unauthorized']);
$routes->set404Override('App\Controllers\Errors::show404');
