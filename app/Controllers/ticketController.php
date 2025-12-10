<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Ticket;
use App\Models\Entrada;
use App\Models\User;
use App\Models\Categoria;
use App\Models\Prioridad;
use App\Models\Estado;

class TicketController extends Controller
{
    public function __construct()
    {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
    }

    /**
     * Vista principal - redirige según el rol
     */
    public function index()
    {
        $user = User::find($_SESSION['user']['id']);

        if ($user->rol === 'Usuario') {
            return $this->userDashboard();
        } elseif ($user->rol === 'Operador') {
            return $this->operatorDashboard();
        } else {
            return $this->adminDashboard();
        }
    }

    /**
     * Dashboard para usuarios regulares
     */
    private function userDashboard()
    {
        $estado = $_GET['estado'] ?? 'todos';
        $tickets = Ticket::getByUser($_SESSION['user']['id'], $estado);
        $estados = Estado::allActive();

        return $this->view('tickets/user_dashboard', [
            'tickets' => $tickets,
            'estado' => $estado,
            'estados' => $estados
        ]);
    }

    /**
     * Dashboard para operadores
     */
    private function operatorDashboard()
    {
        $ticketsNoAsignados = Ticket::getUnassigned();
        $estado = $_GET['estado'] ?? 'todos';
        $misTickets = Ticket::getByOperator($_SESSION['user']['id'], $estado);
        $estados = Estado::allActive();

        return $this->view('tickets/operator_dashboard', [
            'ticketsNoAsignados' => $ticketsNoAsignados,
            'misTickets' => $misTickets,
            'estado' => $estado,
            'estados' => $estados
        ]);
    }

    /**
     * Dashboard para superadministradores
     */
    private function adminDashboard()
    {
        $estado = $_GET['estado'] ?? 'todos';
        $tipo = $_GET['tipo'] ?? 'todos';
        $operador = $_GET['operador'] ?? 'todos';
        $busqueda = $_GET['busqueda'] ?? '';

        $tickets = Ticket::getAllFiltered($estado, $tipo, $operador, $busqueda);
        $operadores = User::getOperators();
        $estados = Estado::allActive();

        return $this->view('tickets/admin_dashboard', [
            'tickets' => $tickets,
            'operadores' => $operadores,
            'estados' => $estados,
            'estado' => $estado,
            'tipo' => $tipo,
            'operador' => $operador,
            'busqueda' => $busqueda
        ]);
    }

    /**
     * Formulario de creación de ticket
     */
    public function create()
    {
        $user = User::find($_SESSION['user']['id']);

        if ($user->rol !== 'Usuario') {
            header('Location: /tickets');
            exit;
        }

        $categorias = Categoria::allActive();
        $prioridades = Prioridad::allActive();

        return $this->view('tickets/create', [
            'categorias' => $categorias,
            'prioridades' => $prioridades
        ]);
    }

    /**
     * Guardar nuevo ticket
     */
    public function store()
    {
        $user = User::find($_SESSION['user']['id']);

        if ($user->rol !== 'Usuario') {
            header('Location: /tickets');
            exit;
        }

        // Validar datos
        $errors = [];

        if (empty($_POST['titulo']) || strlen($_POST['titulo']) > 200) {
            $errors[] = 'El título es obligatorio y debe tener máximo 200 caracteres';
        }

        if (empty($_POST['tipo']) || !in_array($_POST['tipo'], ['Petición', 'Incidente'])) {
            $errors[] = 'El tipo de solicitud es inválido';
        }

        if (empty($_POST['descripcion'])) {
            $errors[] = 'La descripción es obligatoria';
        }

        if (!empty($errors)) {
            $categorias = Categoria::allActive();
            $prioridades = Prioridad::allActive();
            return $this->view('tickets/create', [
                'categorias' => $categorias,
                'prioridades' => $prioridades,
                'errors' => $errors,
                'old' => $_POST
            ]);
        }

        $data = [
            'titulo' => trim($_POST['titulo']),
            'tipo' => $_POST['tipo'],
            'usuario_creador_id' => $_SESSION['user']['id'],
            'categoria_id' => !empty($_POST['categoria_id']) ? $_POST['categoria_id'] : null,
            'prioridad_id' => !empty($_POST['prioridad_id']) ? $_POST['prioridad_id'] : null
        ];

        $descripcion = trim($_POST['descripcion']);

        $ticketId = Ticket::create($data);

        // Crear primera entrada con la descripción
        Entrada::create([
            'ticket_id' => $ticketId,
            'autor_id' => $_SESSION['user']['id'],
            'texto' => $descripcion
        ]);

        header('Location: /tickets/' . $ticketId);
    }

    /**
     * Ver detalle de ticket
     */
    public function show($id)
    {
        $user = User::find($_SESSION['user']['id']);
        $ticket = Ticket::find($id);

        if (!$ticket) {
            header('Location: /tickets');
            exit;
        }

        // Verificar permisos
        if ($user->rol === 'Usuario' && $ticket->usuario_creador_id != $user->id) {
            header('Location: /tickets');
            exit;
        }

        if ($user->rol === 'Operador' && $ticket->operador_asignado_id != $user->id && $ticket->estado !== 'No Asignado') {
            header('Location: /tickets');
            exit;
        }

        // Obtener entradas según el rol
        $isOperator = in_array($user->rol, ['Operador', 'Superadministrador']);
        $entradas = Entrada::getVisibleByTicket($id, $isOperator);

        // Obtener estados disponibles para transición
        $estadosDisponibles = Estado::getAvailableTransitions($ticket->estado);

        return $this->view('tickets/show', [
            'ticket' => $ticket,
            'entradas' => $entradas,
            'userRole' => $user->rol,
            'estadosDisponibles' => $estadosDisponibles
        ]);
    }

    /**
     * Asignar ticket a operador
     */
    public function assign($id)
    {
        $user = User::find($_SESSION['user']['id']);

        if ($user->rol !== 'Operador') {
            header('Location: /tickets');
            exit;
        }

        $ticket = Ticket::find($id);

        if ($ticket->estado !== 'No Asignado') {
            header('Location: /tickets');
            exit;
        }

        Ticket::assignToOperator($id, $_SESSION['user']['id']);

        // Crear entrada de asignación
        Entrada::create([
            'ticket_id' => $id,
            'autor_id' => $_SESSION['user']['id'],
            'texto' => 'Ticket asignado al operador ' . $_SESSION['user']['username'],
            'estado_anterior' => 'No Asignado',
            'estado_nuevo' => 'Asignado'
        ]);

        header('Location: /tickets/' . $id);
    }

    /**
     * Actualizar estado del ticket
     */
    public function updateStatus($id)
    {
        $user = User::find($_SESSION['user']['id']);
        $ticket = Ticket::find($id);

        if ($user->rol !== 'Operador' || $ticket->operador_asignado_id != $user->id) {
            header('Location: /tickets');
            exit;
        }

        $nuevoEstado = $_POST['estado'];
        $comentario = trim($_POST['comentario'] ?? '');

        // Validar transición de estado
        if (!Ticket::isValidTransition($ticket->estado, $nuevoEstado)) {
            header('Location: /tickets/' . $id . '?error=transicion_invalida');
            exit;
        }

        Ticket::updateStatus($id, $nuevoEstado);

        // Crear entrada con cambio de estado
        Entrada::create([
            'ticket_id' => $id,
            'autor_id' => $_SESSION['user']['id'],
            'texto' => !empty($comentario) ? $comentario : 'Cambio de estado a: ' . $nuevoEstado,
            'estado_anterior' => $ticket->estado,
            'estado_nuevo' => $nuevoEstado
        ]);

        header('Location: /tickets/' . $id);
    }

    /**
     * Agregar entrada/comentario al ticket
     */
    public function addEntry($id)
    {
        $user = User::find($_SESSION['user']['id']);
        $ticket = Ticket::find($id);

        // Verificar permisos
        if ($user->rol === 'Usuario' && $ticket->usuario_creador_id != $user->id) {
            header('Location: /tickets');
            exit;
        }

        if ($user->rol === 'Operador' && $ticket->operador_asignado_id != $user->id) {
            header('Location: /tickets');
            exit;
        }

        $texto = trim($_POST['texto']);

        if (empty($texto)) {
            header('Location: /tickets/' . $id . '?error=texto_vacio');
            exit;
        }

        Entrada::create([
            'ticket_id' => $id,
            'autor_id' => $_SESSION['user']['id'],
            'texto' => $texto
        ]);

        header('Location: /tickets/' . $id);
    }

    /**
     * Aceptar solución propuesta
     */
    public function acceptSolution($id)
    {
        $user = User::find($_SESSION['user']['id']);
        $ticket = Ticket::find($id);

        if ($user->rol !== 'Usuario' || $ticket->usuario_creador_id != $user->id || $ticket->estado !== 'Solucionado') {
            header('Location: /tickets');
            exit;
        }

        Ticket::updateStatus($id, 'Cerrado');

        Entrada::create([
            'ticket_id' => $id,
            'autor_id' => $_SESSION['user']['id'],
            'texto' => 'El usuario ha aceptado la solución propuesta',
            'estado_anterior' => 'Solucionado',
            'estado_nuevo' => 'Cerrado'
        ]);

        header('Location: /tickets/' . $id);
    }

    /**
     * Rechazar solución propuesta
     */
    public function rejectSolution($id)
    {
        $user = User::find($_SESSION['user']['id']);
        $ticket = Ticket::find($id);

        if ($user->rol !== 'Usuario' || $ticket->usuario_creador_id != $user->id || $ticket->estado !== 'Solucionado') {
            header('Location: /tickets');
            exit;
        }

        $motivo = trim($_POST['motivo'] ?? 'El usuario ha rechazado la solución propuesta');

        Ticket::updateStatus($id, 'Asignado');

        Entrada::create([
            'ticket_id' => $id,
            'autor_id' => $_SESSION['user']['id'],
            'texto' => $motivo,
            'estado_anterior' => 'Solucionado',
            'estado_nuevo' => 'Asignado'
        ]);

        header('Location: /tickets/' . $id);
    }
}