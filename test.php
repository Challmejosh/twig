<?php
require_once 'vendor/autoload.php';

use App\Auth;
use App\Router;
use App\TicketManager;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Symfony\Component\HttpFoundation\Request;

echo "Testing classes...\n";

try {
    $loader = new FilesystemLoader(__DIR__ . '/templates');
    $twig = new Environment($loader, ['cache' => false]);
    echo "Twig loaded\n";

    $request = Request::createFromGlobals();
    echo "Request created\n";

    $auth = new Auth();
    echo "Auth class instantiated\n";

    $ticketManager = new TicketManager();
    echo "TicketManager class instantiated\n";

    $router = new Router($twig, $auth, $ticketManager);
    echo "Router class instantiated\n";

    echo "All classes loaded successfully\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
