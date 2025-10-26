<?php
namespace App;

class TicketManager
{
    private $tickets = [];

    public function __construct()
    {
        // In a real app, this would connect to a database
        // For demo purposes, we'll use an in-memory array
        $this->tickets = [
            [
                'id' => '1',
                'user_id' => '1',
                'title' => 'Sample Ticket',
                'desc' => 'This is a sample ticket description.',
                'status' => 'open',
                'priority' => 'medium',
                'created_at' => date('Y-m-d H:i:s'),
            ]
        ];
    }

    public function getTicketsByUser($userId)
    {
        return array_filter($this->tickets, fn($ticket) => $ticket['user_id'] === $userId);
    }

    public function getTicketById($id)
    {
        foreach ($this->tickets as $ticket) {
            if ($ticket['id'] === $id) {
                return $ticket;
            }
        }
        return null;
    }

    public function createTicket($userId, $title, $desc, $status, $priority)
    {
        $ticket = [
            'id' => uniqid(),
            'user_id' => $userId,
            'title' => $title,
            'desc' => $desc,
            'status' => $status,
            'priority' => $priority,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $this->tickets[] = $ticket;
        return $ticket;
    }

    public function updateTicket($id, $title, $desc, $status, $priority)
    {
        foreach ($this->tickets as &$ticket) {
            if ($ticket['id'] === $id) {
                $ticket['title'] = $title;
                $ticket['desc'] = $desc;
                $ticket['status'] = $status;
                $ticket['priority'] = $priority;
                return $ticket;
            }
        }
        return null;
    }

    public function deleteTicket($id)
    {
        $this->tickets = array_filter($this->tickets, fn($ticket) => $ticket['id'] !== $id);
    }
}
