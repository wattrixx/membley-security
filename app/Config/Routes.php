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
