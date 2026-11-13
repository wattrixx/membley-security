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

    $routes->get('courses', 'Admin\Courses::index', [
        'as'     => 'admin.cms.courses',
        'filter' => 'permission:college.courses.view',
    ]);
    $routes->get('courses/new', 'Admin\Courses::createForm', [
        'as'     => 'admin.cms.courses.create',
        'filter' => 'permission:college.courses.manage',
    ]);
    $routes->post('courses', 'Admin\Courses::create', [
        'as'     => 'admin.cms.courses.store',
        'filter' => 'permission:college.courses.manage',
    ]);
    $routes->get('courses/(:num)/edit', 'Admin\Courses::edit/$1', [
        'as'     => 'admin.cms.courses.edit',
        'filter' => 'permission:college.courses.manage',
    ]);
    $routes->post('courses/(:num)', 'Admin\Courses::update/$1', [
        'as'     => 'admin.cms.courses.update',
        'filter' => 'permission:college.courses.manage',
    ]);
    $routes->post('courses/preview', 'Admin\Courses::preview', [
        'as'     => 'admin.cms.courses.preview',
        'filter' => 'permission:college.courses.manage',
    ]);

    $routes->get('news', 'Admin\News::index', [
        'as'     => 'admin.cms.news',
        'filter' => 'permission:content.news.view',
    ]);
    $routes->get('news/new', 'Admin\News::createForm', [
        'as'     => 'admin.cms.news.create',
        'filter' => 'permission:content.news.manage',
    ]);
    $routes->post('news', 'Admin\News::create', [
        'as'     => 'admin.cms.news.store',
        'filter' => 'permission:content.news.manage',
    ]);
    $routes->get('news/(:num)/edit', 'Admin\News::edit/$1', [
        'as'     => 'admin.cms.news.edit',
        'filter' => 'permission:content.news.manage',
    ]);
    $routes->post('news/(:num)', 'Admin\News::update/$1', [
        'as'     => 'admin.cms.news.update',
        'filter' => 'permission:content.news.manage',
    ]);
    $routes->post('news/preview', 'Admin\News::preview', [
        'as'     => 'admin.cms.news.preview',
        'filter' => 'permission:content.news.manage',
    ]);

    $routes->get('pages', 'Admin\Pages::index', [
        'as'     => 'admin.cms.pages',
        'filter' => 'permission:content.pages.view',
    ]);
    $routes->get('pages/(:segment)/edit', 'Admin\Pages::edit/$1', [
        'as'     => 'admin.cms.pages.edit',
        'filter' => 'permission:content.pages.manage',
    ]);
    $routes->post('pages/preview', 'Admin\Pages::preview', [
        'as'     => 'admin.cms.pages.preview',
        'filter' => 'permission:content.pages.manage',
    ]);
    $routes->post('pages/(:segment)', 'Admin\Pages::update/$1', [
        'as'     => 'admin.cms.pages.update',
        'filter' => 'permission:content.pages.manage',
    ]);

    $routes->get('announcements', 'Admin\Announcements::index', [
        'as'     => 'admin.cms.announcements',
        'filter' => 'permission:content.announcements.view',
    ]);
    $routes->get('announcements/new', 'Admin\Announcements::createForm', [
        'as'     => 'admin.cms.announcements.create',
        'filter' => 'permission:content.announcements.manage',
    ]);
    $routes->post('announcements', 'Admin\Announcements::create', [
        'as'     => 'admin.cms.announcements.store',
        'filter' => 'permission:content.announcements.manage',
    ]);
    $routes->get('announcements/(:num)/edit', 'Admin\Announcements::edit/$1', [
        'as'     => 'admin.cms.announcements.edit',
        'filter' => 'permission:content.announcements.manage',
    ]);
    $routes->post('announcements/(:num)', 'Admin\Announcements::update/$1', [
        'as'     => 'admin.cms.announcements.update',
        'filter' => 'permission:content.announcements.manage',
    ]);
    $routes->post('announcements/preview', 'Admin\Announcements::preview', [
        'as'     => 'admin.cms.announcements.preview',
        'filter' => 'permission:content.announcements.manage',
    ]);

    $routes->get('media', 'Admin\Media::index', [
        'as'     => 'admin.cms.media',
        'filter' => 'permission:content.media.view',
    ]);
    $routes->post('media', 'Admin\Media::upload', [
        'as'     => 'admin.cms.media.upload',
        'filter' => 'permission:content.media.manage',
    ]);
    $routes->post('media/(:num)/alt', 'Admin\Media::updateAltText/$1', [
        'as'     => 'admin.cms.media.alt',
        'filter' => 'permission:content.media.manage',
    ]);
    $routes->post('media/(:num)/delete', 'Admin\Media::delete/$1', [
        'as'     => 'admin.cms.media.delete',
        'filter' => 'permission:content.media.manage',
    ]);

    $routes->get('college/applications', 'Admin\FutureModules::applications', [
        'filter' => 'permission:college.applications.view',
    ]);
    $routes->get('security/enquiries', 'Admin\FutureModules::securityEnquiries', [
        'filter' => 'permission:security.enquiries.view',
    ]);
});
