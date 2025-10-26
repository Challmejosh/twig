# TicketDesk - PHP/Twig Version

This is a PHP-based ticket management application using Twig templating, converted from a React SPA.

## Features

- User authentication (signin/signup)
- Dashboard with ticket statistics
- Create, view, edit, and delete tickets
- Responsive design with Tailwind CSS

## Requirements

- PHP 7.4 or higher
- Composer

## Installation

1. Install Composer dependencies:
   ```bash
   composer install
   ```

2. Start the PHP development server:
   ```bash
   php -S localhost:8000 -t public
   ```

3. Open your browser and navigate to `http://localhost:8000`

## Demo Credentials

- Email: demo@example.com
- Password: password123

## Project Structure

```
ticket-react/ (converted to PHP/Twig)
├── composer.json
├── public/
│   └── index.php
├── src/
│   ├── Router.php
│   ├── Auth.php
│   └── TicketManager.php
├── templates/
│   ├── base.twig
│   ├── landing.twig
│   ├── auth_layout.twig
│   ├── signin.twig
│   ├── signup.twig
│   ├── dashboard_layout.twig
│   ├── dashboard.twig
│   ├── tickets.twig
│   ├── create_ticket.twig
│   ├── ticket_view.twig
│   └── 404.twig
└── README.md
```

## Notes

- This is a demo implementation using in-memory storage
- In a production environment, you would connect to a real database
- Authentication uses PHP sessions
- Forms are handled server-side with POST requests
