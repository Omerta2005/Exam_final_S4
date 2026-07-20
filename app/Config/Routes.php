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
