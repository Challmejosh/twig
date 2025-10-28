<?php
namespace App;

use Twig\Environment;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class Router
{
    private $twig;
    private $auth;
    private $ticketManager;

    public function __construct(Environment $twig, Auth $auth, TicketManager $ticketManager)
    {
        $this->twig = $twig;
        $this->auth = $auth;
        $this->ticketManager = $ticketManager;
    }

    public function handleRequest(Request $request)
    {
        $path = $request->getPathInfo();
        $method = $request->getMethod();

        // Check authentication for protected routes
        $protectedRoutes = ['/dashboard', '/tickets', '/create-ticket', '/tickets/'];
        $isProtected = false;
        foreach ($protectedRoutes as $route) {
            if (strpos($path, $route) === 0) {
                $isProtected = true;
                break;
            }
        }

        if ($isProtected && !$this->auth->isLoggedIn()) {
            $response = new RedirectResponse('/signin');
            $response->send();
            return;
        }

        // Non-auth routes redirect to dashboard if logged in
        $nonAuthRoutes = ['/signin', '/signup'];
        if (in_array($path, $nonAuthRoutes) && $this->auth->isLoggedIn()) {
            $response = new RedirectResponse('/dashboard');
            $response->send();
            return;
        }

        switch ($path) {
            case '/':
                $this->renderLanding($request);
                break;
            case '/signin':
                $this->handleSignin($request);
                break;
            case '/signup':
                $this->handleSignup($request);
                break;
            case '/logout':
                $this->handleLogout();
                break;
            case '/dashboard':
                $this->renderDashboard($request);
                break;
            case '/tickets':
                $this->renderTickets($request);
                break;
            case '/create-ticket':
                $this->handleCreateTicket($request);
                break;
            default:
                if (preg_match('/^\/tickets\/(.+)$/', $path, $matches)) {
                    $this->handleTicketView($request, $matches[1]);
                } else {
                    $this->render404();
                }
                break;
        }
    }

    private function renderLanding($request)
    {
        echo $this->twig->render('landing.twig');
    }

    private function handleSignin($request)
    {
        $success = $request->query->get('success');

        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $password = $request->request->get('password');

            $url = "https://ticket-backend-zeta.vercel.app/api/auth/signin";
            $data = [
                "email" => $email,
                "password" => $password
            ];

            $options = [
                "http" => [
                    "header"  => "Content-Type: application/json\r\n",
                    "method"  => "POST",
                    "content" => json_encode($data),
                ],
            ];

            $context  = stream_context_create($options);
            $response = file_get_contents($url, false, $context);

            if ($response === FALSE) {
                $error = "Something went wrong!";
            } else {
                $resData = json_decode($response, true);
                if (isset($resData["user"])) {
                    session_start();
                    $_SESSION["user"] = $resData["user"];
                    $response = new RedirectResponse('/dashboard');
                    $response->send();
                    return;
                } else {
                    $error = $resData["message"] ?? "Invalid login details.";
                }
            }

            echo $this->twig->render('signin.twig', ['error' => $error, 'email' => $email, 'success' => $success]);
            return;
        }

        echo $this->twig->render('signin.twig', ['success' => $success]);
    }

    private function handleSignup($request)
    {
        if ($request->isMethod('POST')) {
            $name = $request->request->get('name');
            $email = $request->request->get('email');
            $password = $request->request->get('password');

            $url = "https://ticket-backend-zeta.vercel.app/api/auth/register";
            $data = [
                "name" => $name,
                "email" => $email,
                "password" => $password
            ];

            $options = [
                "http" => [
                    "header"  => "Content-Type: application/json\r\n",
                    "method"  => "POST",
                    "content" => json_encode($data),
                ],
            ];

            $context  = stream_context_create($options);
            $response = file_get_contents($url, false, $context);

            if ($response === FALSE) {
                $error = "Something went wrong!";
            } else {
                $resData = json_decode($response, true);
                if (isset($resData["message"])) {
                    // Show success message
                    $success = $resData["message"];
                    // Redirect to login page
                    $response = new RedirectResponse('/signin?success=' . urlencode($success));
                    $response->send();
                    return;
                } else {
                    $error = "Unexpected server response.";
                }
            }

            echo $this->twig->render('signup.twig', ['error' => $error, 'name' => $name, 'email' => $email]);
            return;
        }

        echo $this->twig->render('signup.twig');
    }

    private function renderDashboard($request)
    {
        $user = $this->auth->getCurrentUser();
        $tickets = $this->ticketManager->getTicketsByUser($user['id']);
        $stats = [
            'total' => count($tickets),
            'open' => count(array_filter($tickets, fn($t) => $t['status'] === 'open')),
            'resolved' => count(array_filter($tickets, fn($t) => $t['status'] === 'closed')),
        ];

        echo $this->twig->render('dashboard.twig', [
            'user' => $user,
            'stats' => $stats,
        ]);
    }

    private function renderTickets($request)
    {
        $user = $this->auth->getCurrentUser();
        $tickets = $this->ticketManager->getTicketsByUser($user['id']);

        echo $this->twig->render('tickets.twig', [
            'user' => $user,
            'tickets' => $tickets,
        ]);
    }

    private function handleCreateTicket($request)
    {
        $user = $this->auth->getCurrentUser();

        if ($request->isMethod('POST')) {
            $title = $request->request->get('title');
            $desc = $request->request->get('desc');
            $status = $request->request->get('status');
            $priority = $request->request->get('priority');

            $this->ticketManager->createTicket($user['id'], $title, $desc, $status, $priority);
            $response = new RedirectResponse('/tickets');
            $response->send();
            return;
        }

        echo $this->twig->render('create_ticket.twig', ['user' => $user]);
    }

    private function handleTicketView($request, $ticketId)
    {
        $user = $this->auth->getCurrentUser();
        $ticket = $this->ticketManager->getTicketById($ticketId);

        if (!$ticket || $ticket['user_id'] !== $user['id']) {
            $this->render404();
            return;
        }

        if ($request->isMethod('POST')) {
            if ($request->request->has('delete')) {
                $this->ticketManager->deleteTicket($ticketId);
                $response = new RedirectResponse('/tickets');
                $response->send();
                return;
            } elseif ($request->request->has('update')) {
                $title = $request->request->get('title');
                $desc = $request->request->get('desc');
                $status = $request->request->get('status');
                $priority = $request->request->get('priority');

                $this->ticketManager->updateTicket($ticketId, $title, $desc, $status, $priority);
                $response = new RedirectResponse("/tickets/{$ticketId}?updated=1");
                $response->send();
                return;
            }
        }

        $isEditing = $request->query->get('edit') === 'true';

        echo $this->twig->render('ticket_view.twig', [
            'user' => $user,
            'ticket' => $ticket,
            'isEditing' => $isEditing,
        ]);
    }

    private function handleLogout()
    {
        $this->auth->logout();
        $response = new RedirectResponse('/');
        $response->send();
        return;
    }

    private function render404()
    {
        http_response_code(404);
        echo $this->twig->render('404.twig');
    }
}
