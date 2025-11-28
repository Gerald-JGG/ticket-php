<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Ticket;
use App\Models\Entrada;
use App\Models\User;
use App\Models\Categoria;
use App\Models\Prioridad;

class TicketController extends Controller
{
    public function __construct()
    {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
    }

    // Vista para Usuarios
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

    private function userDashboard()
    {
        $estado = $_GET['estado'] ?? 'todos';
        $tickets = Ticket::getByUser($_SESSION['user']['id'], $estado);
        return $this->view('tickets/user_dashboard', [
            'tickets' => $tickets,
            'estado' => $estado
        ]);
    }

    private function operatorDashboard()
    {
        $ticketsNoAsignados = Ticket::getUnassigned();
        $estado = $_GET['estado'] ?? 'todos';
        $misTickets = Ticket::getByOperator($_SESSION['user']['id'], $estado);
        
        return $this->view('tickets/operator_dashboard', [
            'ticketsNoAsignados' => $ticketsNoAsignados,
            'misTickets' => $misTickets,
            'estado' => $estado
        ]);
    }

    private function adminDashboard()
    {
        $estado = $_GET['estado'] ?? 'todos';
        $tipo = $_GET['tipo'] ?? 'todos';
        $operador = $_GET['operador'] ?? 'todos';
        $busqueda = $_GET['busqueda'] ?? '';
        
        $tickets = Ticket::getAllFiltered($estado, $tipo, $operador, $busqueda);
        $operadores = User::getOperators();
        
        return $this->view('tickets/admin_dashboard', [
            'tickets' => $tickets,
            'operadores' => $operadores,
            'estado' => $estado,
            'tipo' => $tipo,
            'operador' => $operador,
            'busqueda' => $busqueda
        ]);
    }

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

    public function store()
    {
        $user = User::find($_SESSION['user']['id']);
        
        if ($user->rol !== 'Usuario') {
            header('Location: /tickets');
            exit;
        }

        $data = [
            'titulo' => $_POST['titulo'],
            'tipo' => $_POST['tipo'],
            'usuario_creador_id' => $_SESSION['user']['id'],
            'categoria_id' => $_POST['categoria_id'] ?? null,
            'prioridad_id' => $_POST['prioridad_id'] ?? null
        ];

        $descripcion = $_POST['descripcion'];

        $ticketId = Ticket::create($data);
        
        // Crear primera entrada con la descripción
        Entrada::create([
            'ticket_id' => $ticketId,
            'autor_id' => $_SESSION['user']['id'],
            'texto' => $descripcion,
            'es_interno' => false
        ]);

        header('Location: /tickets/' . $ticketId);
    }

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

        $entradas = Entrada::getByTicket($id);
        
        return $this->view('tickets/show', [
            'ticket' => $ticket,
            'entradas' => $entradas,
            'userRole' => $user->rol
        ]);
    }

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
            'texto' => 'Ticket asignado',
            'es_interno' => true,
            'estado_anterior' => 'No Asignado',
            'estado_nuevo' => 'Asignado'
        ]);

        header('Location: /tickets/' . $id);
    }

    public function updateStatus($id)
    {
        $user = User::find($_SESSION['user']['id']);
        $ticket = Ticket::find($id);
        
        if ($user->rol !== 'Operador' || $ticket->operador_asignado_id != $user->id) {
            header('Location: /tickets');
            exit;
        }

        $nuevoEstado = $_POST['estado'];
        $comentario = $_POST['comentario'] ?? '';

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
            'texto' => $comentario ?: 'Cambio de estado',
            'es_interno' => false,
            'estado_anterior' => $ticket->estado,
            'estado_nuevo' => $nuevoEstado
        ]);

        header('Location: /tickets/' . $id);
    }

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

        $texto = $_POST['texto'];
        $esInterno = isset($_POST['es_interno']) && $user->rol !== 'Usuario' ? 1 : 0;

        Entrada::create([
            'ticket_id' => $id,
            'autor_id' => $_SESSION['user']['id'],
            'texto' => $texto,
            'es_interno' => $esInterno
        ]);

        header('Location: /tickets/' . $id);
    }

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
            'texto' => 'Solución aceptada',
            'es_interno' => false,
            'estado_anterior' => 'Solucionado',
            'estado_nuevo' => 'Cerrado'
        ]);

        header('Location: /tickets/' . $id);
    }

    public function rejectSolution($id)
    {
        $user = User::find($_SESSION['user']['id']);
        $ticket = Ticket::find($id);
        
        if ($user->rol !== 'Usuario' || $ticket->usuario_creador_id != $user->id || $ticket->estado !== 'Solucionado') {
            header('Location: /tickets');
            exit;
        }

        $motivo = $_POST['motivo'] ?? 'Solución rechazada';

        Ticket::updateStatus($id, 'Asignado');
        
        Entrada::create([
            'ticket_id' => $id,
            'autor_id' => $_SESSION['user']['id'],
            'texto' => $motivo,
            'es_interno' => false,
            'estado_anterior' => 'Solucionado',
            'estado_nuevo' => 'Asignado'
        ]);

        header('Location: /tickets/' . $id);
    }
}