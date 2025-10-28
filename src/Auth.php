<?php
namespace App;

class Auth
{
    private $apiBaseUrl = 'https://ticket-backend-zeta.vercel.app/';
    // private $apiBaseUrl = 'http://localhost:3000';

    private function makeApiCall($method, $url, $data = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->apiBaseUrl . $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);

        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false) {
            return ['error' => 'API call failed'];
        }

        $decoded = json_decode($response, true);
        if ($httpCode >= 200 && $httpCode < 300) {
            return $decoded;
        } else {
            return ['error' => $decoded['message'] ?? 'Something went wrong'];
        }
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

        $result = $this->makeApiCall('POST', 'api/auth/register', [
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ]);

        if (isset($result['error'])) {
            return ['success' => false, 'error' => $result['error']];
        }

        return ['success' => true, 'message' => $result['message'] ?? 'Registration successful'];
    }

    public function login($email, $password)
    {
        if (empty($email) || empty($password)) {
            return ['success' => false, 'error' => 'Email and password are required'];
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'error' => 'Invalid email format'];
        }
        if (strlen($password) < 8) {
            return ['success' => false, 'error' => 'Password must be at least 8 characters'];
        }

        $result = $this->makeApiCall('POST', 'api/auth/signin', [
            'email' => $email,
            'password' => $password,
        ]);
        if (isset($result['error'])) {
            return ['success' => false, 'error' => $result['error']];
        }

        if (isset($result['ticketapp_session'])) {
            $_SESSION['ticketapp_session'] = $result['ticketapp_session'];
            return ['success' => true];
        }

        return ['success' => false, 'error' => 'Login failed'];
    }

    public function logout()
    {
        unset($_SESSION['ticketapp_session']);
    }

    public function isLoggedIn()
    {
        return isset($_SESSION['ticketapp_session']);
    }

    public function getCurrentUser()
    {
        return $_SESSION['ticketapp_session'] ?? null;
    }
}
