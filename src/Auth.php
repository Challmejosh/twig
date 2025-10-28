<?php
namespace App;

class Auth
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = new \PDO('sqlite:' . __DIR__ . '/../database.sqlite');
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS users (
            id TEXT PRIMARY KEY,
            name TEXT NOT NULL,
            email TEXT UNIQUE NOT NULL,
            password TEXT NOT NULL
        )");
    }

    public function register($name, $email, $password, $passwordConfirm)
    {
        // Advanced validation
        if (empty($name) || empty($email) || empty($password) || empty($passwordConfirm)) {
            return ['success' => false, 'error' => 'All fields are required'];
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'error' => 'Invalid email format'];
        }
        if (strlen($password) < 8) {
            return ['success' => false, 'error' => 'Password must be at least 8 characters'];
        }
        if (!preg_match('/[A-Z]/', $password) || !preg_match('/\d/', $password) || !preg_match('/[!@#$%^&*()_+\-=[\]{};\':"\\|,.<>\/?]/', $password)) {
            return ['success' => false, 'error' => 'Password must contain uppercase, number, and symbol'];
        }
        if (strpos($password, $name) !== false || strpos($password, $email) !== false) {
            return ['success' => false, 'error' => 'Password cannot contain name or email'];
        }
        if ($password !== $passwordConfirm) {
            return ['success' => false, 'error' => 'Passwords do not match'];
        }

        // Check if user already exists
        $stmt = $this->pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            return ['success' => false, 'error' => 'User already exists'];
        }

        $id = uniqid();
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->pdo->prepare("INSERT INTO users (id, name, email, password) VALUES (?, ?, ?, ?)");
        $stmt->execute([$id, $name, $email, $hashedPassword]);

        $newUser = [
            'id' => $id,
            'name' => $name,
            'email' => $email,
        ];

        return ['success' => true, 'user' => $newUser];
    }

    public function login($email, $password)
    {
        if (empty($email) || empty($password)) {
            return false;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        if (strlen($password) < 8) {
            return false;
        }

        $stmt = $this->pdo->prepare("SELECT id, name, email, password FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
            ];
            return true;
        }
        return false;
    }

    public function logout()
    {
        unset($_SESSION['user']);
    }

    public function isLoggedIn()
    {
        return isset($_SESSION['user']);
    }

    public function getCurrentUser()
    {
        return $_SESSION['user'] ?? null;
    }
}
