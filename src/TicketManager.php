<?php
namespace App;

class TicketManager
{
    private $apiBaseUrl = 'https://ticket-backend-zeta.vercel.app/';

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

    public function createTicket($userId, $title, $desc, $status, $priority)
    {
        return $this->makeApiCall('POST', "tickets/{$userId}", [
            'title' => $title,
            'desc' => $desc,
            'status' => $status,
            'priority' => $priority,
        ]);
    }

    public function getTicketsByUser($userId)
    {
        $result = $this->makeApiCall('GET', "tickets/{$userId}");
        return isset($result['error']) ? [] : $result;
    }

    public function getTicketById($ticketId)
    {
        $result = $this->makeApiCall('GET', "tickets/{$ticketId}");
        return isset($result['error']) ? null : $result;
    }

    public function updateTicket($ticketId, $title, $desc, $status, $priority)
    {
        return $this->makeApiCall('PUT', "tickets/{$ticketId}", [
            'title' => $title,
            'desc' => $desc,
            'status' => $status,
            'priority' => $priority,
            'ticketId' => $ticketId,
        ]);
    }

    public function deleteTicket($userId, $ticketId)
    {
        return $this->makeApiCall('DELETE', "tickets/{$userId}", [
            'ticketId' => $ticketId,
        ]);
    }
}
