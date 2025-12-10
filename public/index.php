<?php

use App\Core\Router;

session_start();

require __DIR__ . '/../app/Core/Router.php';
require __DIR__ . '/../app/Core/Controller.php';
require __DIR__ . '/../app/Core/Model.php';
require __DIR__ . '/../app/Core/View.php';

// Load environment variables
$env = parse_ini_file(__DIR__ . '/../.env');

// Debug mode configuration
if (isset($env['DEBUG']) && filter_var($env['DEBUG'], FILTER_VALIDATE_BOOLEAN)) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(0);
}

// Database connection
require_once __DIR__ . '/../config/database.php';

// Simple Autoloader
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../app/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

$router = new Router();

// Home Route
$router->add('GET', '/', 'HomeController@index');

// Auth Routes
$router->add('GET', '/login', 'AuthController@login');
$router->add('POST', '/login', 'AuthController@authenticate');
$router->add('GET', '/logout', 'AuthController@logout');

// User Routes (Solo Superadministrador)
$router->add('GET', '/users', 'UsuarioController@index');
$router->add('GET', '/users/create', 'UsuarioController@create');
$router->add('POST', '/users/store', 'UsuarioController@store');
$router->add('GET', '/users/edit/{id}', 'UsuarioController@edit');
$router->add('POST', '/users/update/{id}', 'UsuarioController@update');
$router->add('GET', '/users/deactivate/{id}', 'UsuarioController@deactivate');
$router->add('GET', '/users/activate/{id}', 'UsuarioController@activate');

// Ticket Routes
$router->add('GET', '/tickets', 'TicketController@index');
$router->add('GET', '/tickets/create', 'TicketController@create');
$router->add('POST', '/tickets/store', 'TicketController@store');
$router->add('GET', '/tickets/{id}', 'TicketController@show');
$router->add('POST', '/tickets/{id}/assign', 'TicketController@assign');
$router->add('POST', '/tickets/{id}/update-status', 'TicketController@updateStatus');
$router->add('POST', '/tickets/{id}/add-entry', 'TicketController@addEntry');
$router->add('POST', '/tickets/{id}/accept-solution', 'TicketController@acceptSolution');
$router->add('POST', '/tickets/{id}/reject-solution', 'TicketController@rejectSolution');

try {
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $method = $_SERVER['REQUEST_METHOD'];
    $router->dispatch($uri, $method);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}