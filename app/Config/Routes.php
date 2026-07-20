<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'ClientController::index');
$routes->group('client', function($routes) {
    $routes->post('login', 'ClientController::login');
    $routes->get('solde', 'ClientController::solde');
    $routes->get('operations', 'ClientController::operations');
    $routes->post('retrait', 'ClientController::retrait');
    $routes->post('depot', 'ClientController::depot');
    $routes->post('transfert', 'ClientController::transfert');
    $routes->get('historique', 'ClientController::historique');
});
$routes->get('/bareme-frais', 'BaremeFraisController::index');
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
    $routes->get('form', 'BaremeFraisController::form');
    $routes->post('save', 'BaremeFraisController::save');
});

$routes->get('operateur/gains', 'GainController::index');
