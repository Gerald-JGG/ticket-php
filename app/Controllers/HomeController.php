<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Models\SolicitudRegistro;
use App\Models\Departamento;

class HomeController extends Controller
{
    public function index()
    {
        // Si está autenticado, redirigir al dashboard de tickets
        if (isset($_SESSION['user'])) {
            header('Location: /tickets');
            exit;
        }

        // Si no está autenticado, mostrar página de inicio
        return $this->view('home/index');
    }

    /**
     * Mostrar formulario de solicitud de registro
     */
    public function requestAccess()
    {
        if (isset($_SESSION['user'])) {
            header('Location: /tickets');
            exit;
        }

        $departamentos = Departamento::allActive();
        return $this->view('home/request_access', [
            'departamentos' => $departamentos
        ]);
    }

    /**
     * Procesar solicitud de registro
     */
    public function submitRequest()
    {
        if (isset($_SESSION['user'])) {
            header('Location: /tickets');
            exit;
        }

        $errors = [];

        // Validaciones
        if (empty($_POST['nombre_completo']) || strlen($_POST['nombre_completo']) < 3) {
            $errors[] = 'El nombre completo es obligatorio (mínimo 3 caracteres)';
        }

        if (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Debe proporcionar un email válido';
        }

        if (empty($_POST['username_solicitado']) || strlen($_POST['username_solicitado']) < 3) {
            $errors[] = 'El nombre de usuario es obligatorio (mínimo 3 caracteres)';
        }

        if (empty($_POST['motivo']) || strlen($_POST['motivo']) < 20) {
            $errors[] = 'El motivo debe tener al menos 20 caracteres';
        }

        // Verificar si el email ya tiene solicitud pendiente
        if (!empty($_POST['email']) && SolicitudRegistro::hasPendingRequest($_POST['email'])) {
            $errors[] = 'Ya existe una solicitud pendiente con este email';
        }

        // Verificar si el username ya existe
        if (!empty($_POST['username_solicitado']) && User::findByUsername($_POST['username_solicitado'])) {
            $errors[] = 'El nombre de usuario ya está en uso';
        }

        // Verificar si el username ya está solicitado
        if (!empty($_POST['username_solicitado']) && SolicitudRegistro::isUsernameTaken($_POST['username_solicitado'])) {
            $errors[] = 'Ya existe una solicitud pendiente con este nombre de usuario';
        }

        if (!empty($errors)) {
            $departamentos = Departamento::allActive();
            return $this->view('home/request_access', [
                'departamentos' => $departamentos,
                'errors' => $errors,
                'old' => $_POST
            ]);
        }

        // Crear solicitud
        $data = [
            'nombre_completo' => trim($_POST['nombre_completo']),
            'email' => trim($_POST['email']),
            'username_solicitado' => trim($_POST['username_solicitado']),
            'departamento_solicitado' => !empty($_POST['departamento_solicitado']) ? trim($_POST['departamento_solicitado']) : null,
            'telefono' => !empty($_POST['telefono']) ? trim($_POST['telefono']) : null,
            'motivo' => trim($_POST['motivo'])
        ];

        SolicitudRegistro::create($data);

        return $this->view('home/request_success');
    }
}