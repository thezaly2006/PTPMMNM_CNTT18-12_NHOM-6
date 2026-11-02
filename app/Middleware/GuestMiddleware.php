<?php

declare(strict_types=1);

namespace App\Middleware;

class GuestMiddleware
{
    public function handle(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!empty($_SESSION['user'])) {
            redirect('/dashboard');
        }
    }
}
