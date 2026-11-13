<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');
$routes->get('about', 'PublicSite::about');
$routes->get('college', 'PublicSite::college');
$routes->get('college/contact', 'PublicSite::collegeContact');
$routes->get('college/courses', 'PublicSite::courses');
$routes->get('college/courses/(:segment)', 'PublicSite::course/$1');
$routes->get('contact', 'PublicSite::contact');
$routes->get('news', 'PublicSite::news');
$routes->get('news/(:segment)', 'PublicSite::article/$1');
$routes->get('security-services', 'PublicSite::securityServices');
$routes->get('security-services/contact', 'PublicSite::securityContact');

$routes->get('admin/login', 'Admin\AuthController::login', [
    'as'     => 'login',
    'filter' => 'auth-rates',
]);
$routes->post('admin/login', 'Admin\AuthController::attempt', [
    'filter' => 'auth-rates',
]);
$routes->post('admin/logout', 'Admin\AuthController::logout', [
    'as'     => 'admin.logout',
    'filter' => 'session',
]);
$routes->get('admin/forbidden', 'Admin\AuthController::forbidden', [
    'as'     => 'admin.forbidden',
    'filter' => 'session',
]);

$adminFilters = ['session', 'permission:admin.access'];

$routes->get('admin', 'Admin\Dashboard::index', [
    'as'     => 'admin.dashboard',
    'filter' => $adminFilters,
]);

$routes->group('admin', ['filter' => $adminFilters], static function ($routes): void {
    $routes->get('users', 'Admin\Users::index', [
        'as'     => 'admin.users',
        'filter' => 'permission:admin.users.view',
    ]);
    $routes->get('users/new', 'Admin\Users::createForm', [
        'as'     => 'admin.users.create',
        'filter' => 'permission:admin.users.manage',
    ]);
    $routes->post('users', 'Admin\Users::create', [
        'as'     => 'admin.users.store',
        'filter' => 'permission:admin.users.manage',
    ]);
    $routes->post('users/(:num)/role', 'Admin\Users::updateRole/$1', [
        'as'     => 'admin.users.role',
        'filter' => 'permission:admin.users.manage',
    ]);
    $routes->post('users/(:num)/status', 'Admin\Users::updateStatus/$1', [
        'as'     => 'admin.users.status',
        'filter' => 'permission:admin.users.manage',
    ]);

    $routes->get('content/pages', 'Admin\FutureModules::pages', [
        'filter' => 'permission:content.pages.view',
    ]);
    $routes->get('college/applications', 'Admin\FutureModules::applications', [
        'filter' => 'permission:college.applications.view',
    ]);
    $routes->get('security/enquiries', 'Admin\FutureModules::securityEnquiries', [
        'filter' => 'permission:security.enquiries.view',
    ]);
});
