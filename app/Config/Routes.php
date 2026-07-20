<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'BaremeFraisController::index');

$routes->group('operateur/prefixe', function ($routes) {
    $routes->get('/', 'PrefixeController::index');
    $routes->get('form', 'PrefixeController::form');
    $routes->post('save', 'PrefixeController::save');
});

$routes->group('operateur/baremeFrais', function ($routes) {
    $routes->get('/', 'BaremeFraisController::index');
    $routes->get('formMultiple', 'BaremeFraisController::formMultiple');
    $routes->post('saveMultiple', 'BaremeFraisController::saveMultiple');
});