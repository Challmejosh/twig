<?php
namespace App;

class Auth
{
    private $users = [];

    public function __construct()
    {
        // In a real app, this would connect to a database
        // For demo purposes, we'll use an in-memory array
        $this->users = [
            [
                'id' => '1',
                'name' => 'Demo User',
                'email' => 'demo@example.com',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
            ]
        ];
    }

    public function register($name, $email, $password)
    {
        // Check if user already exists
        foreach ($this->users as $user) {
            if ($user['email'] === $email) {
                return false;
            }
        }

        $newUser = [
            'id' => uniqid(),
            'name' => $name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
        ];

        $this->users[] = $newUser;
        return true;
    }

    public function login($email, $password)
    {
        foreach ($this->users as $user) {
            if ($user['email'] === $email && password_verify($password, $user['password'])) {
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email'],
                ];
                return true;
            }
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
