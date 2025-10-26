<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Router;
use App\Auth;
use App\TicketManager;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

// Initialize Twig
$loader = new FilesystemLoader(__DIR__ . '/../templates');
$twig = new Environment($loader, [
    'cache' => false, // Disable cache for development
]);

// Initialize classes
$auth = new Auth();
$ticketManager = new TicketManager();

// Start session
session_start();

// Create router and handle request
$router = new Router($twig, $auth, $ticketManager);
$router->handleRequest();
