<?php
require_once 'vendor/autoload.php';

use App\Auth;
use App\Router;
use App\TicketManager;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Symfony\Component\HttpFoundation\Request;

echo "Testing signin route...\n";

try {
    $loader = new FilesystemLoader(__DIR__ . '/templates');
    $twig = new Environment($loader, ['cache' => false]);

    $request = Request::create('/signin', 'GET');
    $twig->addGlobal('app', ['request' => $request]);

    $auth = new Auth();
    $ticketManager = new TicketManager();
    $router = new Router($twig, $auth, $ticketManager);

    echo "Calling handleRequest for /signin\n";
    $router->handleRequest($request);

    echo "Signin route handled successfully\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
