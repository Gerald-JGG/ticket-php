<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Models\SolicitudRegistro;
use App\Models\Rol;
use App\Models\Departamento;

class SolicitudController extends Controller
{
    public function __construct()
    {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }

        // Solo Superadministrador puede acceder
        $user = User::find($_SESSION['user']['id']);
        if (!$user || $user->rol !== 'Superadministrador') {
            header('Location: /');
            exit;
        }
    }

    /**
     * Listar todas las solicitudes
     */
    public function index()
    {
        $solicitudes = SolicitudRegistro::all();
        $pendientes = SolicitudRegistro::countByStatus('Pendiente');
        
        return $this->view('solicitudes/index', [
            'solicitudes' => $solicitudes,
            'pendientes' => $pendientes
        ]);
    }

    /**
     * Ver detalle de solicitud
     */
    public function show($id)
    {
        $solicitud = SolicitudRegistro::find($id);
        
        if (!$solicitud) {
            header('Location: /solicitudes');
            exit;
        }
        
        $roles = Rol::allActive();
        $departamentos = Departamento::allActive();
        
        return $this->view('solicitudes/show', [
            'solicitud' => $solicitud,
            'roles' => $roles,
            'departamentos' => $departamentos
        ]);
    }

    /**
     * Aprobar solicitud y crear usuario
     */
    public function aprobar($id)
    {
        $solicitud = SolicitudRegistro::find($id);
        
        if (!$solicitud || $solicitud->estado !== 'Pendiente') {
            header('Location: /solicitudes');
            exit;
        }

        // Validar datos
        $errors = [];
        
        if (empty($_POST['rol'])) {
            $errors[] = 'Debe seleccionar un rol';
        }
        
        if (empty($_POST['password']) || strlen($_POST['password']) < 6) {
            $errors[] = 'Debe proporcionar una contraseña (mínimo 6 caracteres)';
        }

        // Verificar si el username ya existe
        if (User::findByUsername($solicitud->username_solicitado)) {
            $errors[] = 'El nombre de usuario ya está en uso';
        }

        if (!empty($errors)) {
            $roles = Rol::allActive();
            $departamentos = Departamento::allActive();
            return $this->view('solicitudes/show', [
                'solicitud' => $solicitud,
                'roles' => $roles,
                'departamentos' => $departamentos,
                'errors' => $errors
            ]);
        }

        // Buscar departamento por nombre
        $departamentoId = null;
        if ($solicitud->departamento_solicitado) {
            $depto = Departamento::findByName($solicitud->departamento_solicitado);
            if ($depto) {
                $departamentoId = $depto->id;
            }
        }
        
        // Permitir override del departamento
        if (!empty($_POST['departamento'])) {
            $departamentoId = $_POST['departamento'];
        }

        // Crear usuario
        $userData = [
            'username' => $solicitud->username_solicitado,
            'password' => $_POST['password'],
            'rol_id' => $_POST['rol'],
            'email' => $solicitud->email,
            'departamento_id' => $departamentoId
        ];

        User::create($userData);

        // Marcar solicitud como aprobada
        $comentario = !empty($_POST['comentario']) ? trim($_POST['comentario']) : 'Solicitud aprobada. Usuario creado exitosamente.';
        SolicitudRegistro::aprobar($id, $_SESSION['user']['id'], $comentario);

        header('Location: /solicitudes?success=aprobada');
        exit;
    }

    /**
     * Rechazar solicitud
     */
    public function rechazar($id)
    {
        $solicitud = SolicitudRegistro::find($id);
        
        if (!$solicitud || $solicitud->estado !== 'Pendiente') {
            header('Location: /solicitudes');
            exit;
        }

        $comentario = !empty($_POST['comentario']) ? trim($_POST['comentario']) : 'Solicitud rechazada';

        if (empty($comentario)) {
            $roles = Rol::allActive();
            $departamentos = Departamento::allActive();
            return $this->view('solicitudes/show', [
                'solicitud' => $solicitud,
                'roles' => $roles,
                'departamentos' => $departamentos,
                'errors' => ['Debe proporcionar un motivo para rechazar la solicitud']
            ]);
        }

        SolicitudRegistro::rechazar($id, $_SESSION['user']['id'], $comentario);

        header('Location: /solicitudes?success=rechazada');
        exit;
    }
}