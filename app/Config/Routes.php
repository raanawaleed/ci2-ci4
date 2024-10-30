<?php
use CodeIgniter\Router\RouteCollection;
/**
* @var RouteCollection $routes
*/
$routes->get('/', 'HomeController::index');
// Set default controller and 404 override
$routes->setDefaultController('DashboardController');
$routes->set404Override('Error::error_404');
// Authentication routes
$routes->get('login', 'AuthController::login');
$routes->get('logout', 'AuthController::logout');
// Dashboard route
$routes->get('dashboard', 'DashboardController::index');
// Planification routes
$routes->get('planification', 'SaisietempsController::planification');
$routes->get('planification/(:num)/(:num)/(:num)', 'SaisietempsController::planification/$1/$2/$3');
// Saisie temps updates and deletions
$routes->post('saisietemps/miseAJourTemps', 'SaisietempsController::miseAJourTemps/1');
$routes->post('planification/miseAJourTemps', 'SaisietempsController::miseAJourTemps/0');
$routes->delete('saisietemps/deleteTempsTicket/(:num)/(:num)/(:num)', 'SaisietempsController::deleteTempsTicket/1/$1/$2/$3');
$routes->delete('planification/deleteTempsTicket/(:num)/(:num)/(:num)/(:num)', 'SaisietempsController::deleteTempsTicket/0/$1/$2/$3/$4');
// Validation routes
$routes->get('validation', 'SaisietempsController::validation');
$routes->get('validation/(:num)/(:num)', 'SaisietempsController::validation/$1/$2');
$routes->post('valider-saisie', 'SaisietempsController::validerSaisie');
// Settings routes for projects and tasks
$routes->get('projects-params', 'SettingsController::indexParamsProjets');
$routes->get('refvente', 'SettingsController::refvente');
$routes->get('projects-params/taches-par-defaut', 'SettingsController::indexTacheParDefaut');
$routes->get('projects-params/taches-par-defaut/view/(:num)', 'SettingsController::indexTacheParDefaut/$1');
