<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class HomeController extends Controller
{
    public function index()
    {
        // Si no está autenticado, redirigir al login
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }

        // Redirigir al dashboard de tickets según el rol
        header('Location: /tickets');
        exit;
    }
}