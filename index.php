<?php
session_start();
date_default_timezone_set('Europe/Madrid');

// Habilitar visualización de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$jsonFile = 'coches.json';
$historyFile = 'historial.json';
$adminFile = 'admin.json';
$blockedFile = 'blocked.json';
$profilesFile = 'profiles.json';
$matriculasFile = 'matriculas.json';

// Crear archivos si no existen
if (!file_exists($jsonFile)) {
    file_put_contents($jsonFile, json_encode([]));
}
if (!file_exists($historyFile)) {
    file_put_contents($historyFile, json_encode([]));
}
if (!file_exists($adminFile)) {
    file_put_contents($adminFile, json_encode([
        'moovecars' => [
            'password' => password_hash('DNI/NIE_CON_LETRA_MAYUSCULA_SIN_ESPACIOS', PASSWORD_DEFAULT),
            'role' => 'master',
            'active' => true,
            'created_at' => time(),
            'last_login' => null,
            'reset_password' => false
        ]
    ], JSON_PRETTY_PRINT));
}
if (!file_exists($blockedFile)) {
    file_put_contents($blockedFile, json_encode([]));
}
if (!file_exists($profilesFile)) {
    file_put_contents($profilesFile, json_encode([]));
}
if (!file_exists($matriculasFile)) {
    file_put_contents($matriculasFile, json_encode([]));
}

// Manejo de solicitudes AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    // Login de usuarios
    if (isset($_POST['action']) && $_POST['action'] === 'login') {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        $admins = json_decode(file_get_contents($adminFile), true);

        if (isset($admins[$username]) && password_verify($password, $admins[$username]['password']) && ($admins[$username]['active'] ?? true)) {
            $_SESSION['admin'] = $username;
            $_SESSION['role'] = $admins[$username]['role'] ?? 'moderador';
            $_SESSION['reset_password'] = $admins[$username]['reset_password'] ?? false;

            // Actualizar último login
            $admins[$username]['last_login'] = time();
            file_put_contents($adminFile, json_encode($admins, JSON_PRETTY_PRINT));

            echo json_encode(['status' => 'success', 'role' => $_SESSION['role'], 'reset_password' => $_SESSION['reset_password']]);
            exit;
        }

        echo json_encode(['status' => 'error', 'message' => 'Credenciales incorrectas o cuenta desactivada']);
        exit;
    }

    // Logout
    if (isset($_POST['action']) && $_POST['action'] === 'logout') {
        unset($_SESSION['admin']);
        unset($_SESSION['role']);
        unset($_SESSION['reset_password']);
        session_destroy();
        echo json_encode(['status' => 'success']);
        exit;
    }

    // Bloquear/desbloquear plazas
    if (isset($_POST['action']) && $_POST['action'] === 'toggle_block' && isset($_SESSION['admin'])) {
        $plaza = intval($_POST['plaza'] ?? 0);

        if ($plaza > 0 && $plaza <= 900) {
            $blocked = json_decode(file_get_contents($blockedFile), true) ?: [];

            if (isset($blocked[$plaza])) {
                unset($blocked[$plaza]);
            } else {
                $blocked[$plaza] = true;
            }

            file_put_contents($blockedFile, json_encode($blocked, JSON_PRETTY_PRINT));
            echo json_encode(['status' => 'success', 'blocked' => isset($blocked[$plaza])]);
            exit;
        }

        echo json_encode(['status' => 'error', 'message' => 'Plaza inválida']);
        exit;
    }

    // Guardar nuevas matrículas
    if (isset($_POST['action']) && $_POST['action'] === 'guardar') {
        $matricula = strtoupper(trim(str_replace('-', '', $_POST['matricula'] ?? '')));
        $plaza = intval($_POST['plaza'] ?? 0);

        // Validación
        if (preg_match('/^[0-9BCDFGHJKLMNPRSTVWXYZ]{4}[0-9BCDFGHJKLMNPRSTVWXYZ]{3}$/', $matricula) && $plaza > 0 && $plaza <= 900) {
            $blocked = json_decode(file_get_contents($blockedFile), true) ?: [];

            // Verificar si la plaza está bloqueada
            if (isset($blocked[$plaza]) && !isset($_SESSION['admin'])) {
                echo json_encode(['status' => 'error', 'message' => 'Plaza en mantenimiento']);
                exit;
            }

            $coches = json_decode(file_get_contents($jsonFile), true) ?: [];
            $historial = json_decode(file_get_contents($historyFile), true) ?: [];
            $matriculas = json_decode(file_get_contents($matriculasFile), true) ?: [];

            // Registrar entrada en historial
            $historial[] = [
                'matricula' => $matricula,
                'plaza' => $plaza,
                'accion' => 'entrada',
                'timestamp' => time(),
                'admin' => $_SESSION['admin'] ?? null
            ];

            // Actualizar plaza
            $coches[$matricula] = ['plaza' => $plaza, 'timestamp' => time()];

            // Guardar matrícula en archivo de matrículas si no existe
            if (!in_array($matricula, $matriculas)) {
                $matriculas[] = $matricula;
                file_put_contents($matriculasFile, json_encode($matriculas, JSON_PRETTY_PRINT));
            }

            file_put_contents($jsonFile, json_encode($coches, JSON_PRETTY_PRINT));
            file_put_contents($historyFile, json_encode($historial, JSON_PRETTY_PRINT));

            echo json_encode(['status' => 'success']);
            exit;
        }

        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Datos inválidos']);
        exit;
    }

    // Eliminar matrículas
    if (isset($_POST['action']) && $_POST['action'] === 'eliminar') {
        $matricula = strtoupper(trim(str_replace('-', '', $_POST['matricula'] ?? '')));

        if (preg_match('/^[0-9BCDFGHJKLMNPRSTVWXYZ]{4}[0-9BCDFGHJKLMNPRSTVWXYZ]{3}$/', $matricula)) {
            $coches = json_decode(file_get_contents($jsonFile), true) ?: [];
            $historial = json_decode(file_get_contents($historyFile), true) ?: [];

            if (isset($coches[$matricula])) {
                // Registrar salida en historial
                $historial[] = [
                    'matricula' => $matricula,
                    'plaza' => $coches[$matricula]['plaza'],
                    'accion' => 'salida',
                    'timestamp' => time(),
                    'admin' => $_SESSION['admin'] ?? null
                ];

                unset($coches[$matricula]);
                file_put_contents($jsonFile, json_encode($coches, JSON_PRETTY_PRINT));
                file_put_contents($historyFile, json_encode($historial, JSON_PRETTY_PRINT));

                echo json_encode(['status' => 'success']);
                exit;
            }

            http_response_code(404);
            echo json_encode(['status' => 'error', 'message' => 'Matrícula no encontrada']);
            exit;
        }

        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Matrícula inválida']);
        exit;
    }

    // Vaciar todas las plazas (solo master)
    if (isset($_POST['action']) && $_POST['action'] === 'vaciar_plazas' && isset($_SESSION['admin']) && $_SESSION['role'] === 'master') {
        $coches = json_decode(file_get_contents($jsonFile), true) ?: [];
        $historial = json_decode(file_get_contents($historyFile), true) ?: [];

        // Registrar salida en historial para todos los coches
        foreach ($coches as $matricula => $datos) {
            $historial[] = [
                'matricula' => $matricula,
                'plaza' => $datos['plaza'],
                'accion' => 'salida',
                'timestamp' => time(),
                'admin' => $_SESSION['admin']
            ];
        }

        file_put_contents($jsonFile, json_encode([], JSON_PRETTY_PRINT));
        file_put_contents($historyFile, json_encode($historial, JSON_PRETTY_PRINT));

        echo json_encode(['status' => 'success', 'count' => count($coches)]);
        exit;
    }

    // Obtener sugerencias de matrículas para autocompletado
    if (isset($_POST['action']) && $_POST['action'] === 'autocompletar_matriculas') {
        $query = strtoupper(trim($_POST['query'] ?? ''));
        $matriculas = json_decode(file_get_contents($matriculasFile), true) ?: [];

        $sugerencias = array_filter($matriculas, function($matricula) use ($query) {
            return strpos($matricula, $query) === 0;
        });

        echo json_encode(['status' => 'success', 'sugerencias' => array_values($sugerencias)]);
        exit;
    }

    // Obtener historial filtrado
    if (isset($_POST['action']) && $_POST['action'] === 'obtener_historial' && isset($_SESSION['admin'])) {
        $historial = json_decode(file_get_contents($historyFile), true) ?: [];
        $matriculaFiltro = isset($_POST['matricula']) ? strtoupper(trim(str_replace('-', '', $_POST['matricula']))) : '';
        $fechaInicio = isset($_POST['fechaInicio']) ? strtotime($_POST['fechaInicio']) : 0;
        $fechaFin = isset($_POST['fechaFin']) ? strtotime($_POST['fechaFin'] . ' 23:59:59') : time();

        if ($matriculaFiltro) {
            $historial = array_filter($historial, function($item) use ($matriculaFiltro) {
                return strpos($item['matricula'], $matriculaFiltro) !== false;
            });
        }

        $historial = array_filter($historial, function($item) use ($fechaInicio, $fechaFin) {
            return $item['timestamp'] >= $fechaInicio && $item['timestamp'] <= $fechaFin;
        });

        echo json_encode(['status' => 'success', 'data' => array_values($historial)]);
        exit;
    }

    // Guardar perfil de administrador
    if (isset($_POST['action']) && $_POST['action'] === 'guardar_perfil' && isset($_SESSION['admin'])) {
        $profiles = json_decode(file_get_contents($profilesFile), true) ?: [];

        $profiles[$_SESSION['admin']] = [
            'nombre' => $_POST['nombre'] ?? '',
            'apellidos' => $_POST['apellidos'] ?? '',
            'email' => $_POST['email'] ?? '',
            'telefono' => $_POST['telefono'] ?? '',
            'actualizado' => time()
        ];

        file_put_contents($profilesFile, json_encode($profiles, JSON_PRETTY_PRINT));
        echo json_encode(['status' => 'success']);
        exit;
    }

    // Obtener perfil de administrador
    if (isset($_POST['action']) && $_POST['action'] === 'obtener_perfil' && isset($_SESSION['admin'])) {
        $profiles = json_decode(file_get_contents($profilesFile), true) ?: [];

        if (isset($profiles[$_SESSION['admin']])) {
            echo json_encode(['status' => 'success', 'data' => $profiles[$_SESSION['admin']]]);
        } else {
            echo json_encode(['status' => 'success', 'data' => []]);
        }
        exit;
    }

    // Obtener lista de moderadores (solo master)
    if (isset($_POST['action']) && $_POST['action'] === 'obtener_moderadores' && isset($_SESSION['admin']) && $_SESSION['role'] === 'master') {
        $admins = json_decode(file_get_contents($adminFile), true);
        $profiles = json_decode(file_get_contents($profilesFile), true) ?: [];

        // Filtrar para no mostrar el master actual ni las contraseñas
        $moderadores = [];
        foreach ($admins as $username => $data) {
            if ($username !== $_SESSION['admin']) {
                $modData = [
                    'username' => $username,
                    'role' => $data['role'],
                    'active' => $data['active'] ?? true,
                    'created_at' => $data['created_at'] ?? null,
                    'last_login' => $data['last_login'] ?? null,
                    'reset_password' => $data['reset_password'] ?? false
                ];

                // Añadir datos del perfil si existen
                if (isset($profiles[$username])) {
                    $modData['profile'] = $profiles[$username];
                }

                $moderadores[] = $modData;
            }
        }

        echo json_encode(['status' => 'success', 'data' => $moderadores]);
        exit;
    }

    // Añadir nuevo moderador (solo master)
    if (isset($_POST['action']) && $_POST['action'] === 'agregar_moderador' && isset($_SESSION['admin']) && $_SESSION['role'] === 'master') {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'moderador';

        if (empty($username) || empty($password)) {
            echo json_encode(['status' => 'error', 'message' => 'Usuario y contraseña son obligatorios']);
            exit;
        }

        $admins = json_decode(file_get_contents($adminFile), true);

        if (isset($admins[$username])) {
            echo json_encode(['status' => 'error', 'message' => 'El usuario ya existe']);
            exit;
        }

        $admins[$username] = [
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role' => $role,
            'active' => true,
            'created_at' => time(),
            'last_login' => null,
            'reset_password' => true
        ];

        file_put_contents($adminFile, json_encode($admins, JSON_PRETTY_PRINT));
        echo json_encode(['status' => 'success']);
        exit;
    }

    // Actualizar moderador (solo master)
    if (isset($_POST['action']) && $_POST['action'] === 'actualizar_moderador' && isset($_SESSION['admin']) && $_SESSION['role'] === 'master') {
        $username = trim($_POST['username'] ?? '');
        $role = $_POST['role'] ?? 'moderador';
        $active = isset($_POST['active']) ? (bool)$_POST['active'] : true;

        $admins = json_decode(file_get_contents($adminFile), true);

        if (!isset($admins[$username]) || $username === $_SESSION['admin']) {
            echo json_encode(['status' => 'error', 'message' => 'Usuario no válido']);
            exit;
        }

        $admins[$username]['role'] = $role;
        $admins[$username]['active'] = $active;

        file_put_contents($adminFile, json_encode($admins, JSON_PRETTY_PRINT));
        echo json_encode(['status' => 'success']);
        exit;
    }

    // Actualizar contraseña de moderador (solo master)
    if (isset($_POST['action']) && $_POST['action'] === 'actualizar_password' && isset($_SESSION['admin']) && $_SESSION['role'] === 'master') {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($password)) {
            echo json_encode(['status' => 'error', 'message' => 'La contraseña no puede estar vacía']);
            exit;
        }

        $admins = json_decode(file_get_contents($adminFile), true);

        if (!isset($admins[$username]) || $username === $_SESSION['admin']) {
            echo json_encode(['status' => 'error', 'message' => 'Usuario no válido']);
            exit;
        }

        $admins[$username]['password'] = password_hash($password, PASSWORD_DEFAULT);
        $admins[$username]['reset_password'] = false;

        file_put_contents($adminFile, json_encode($admins, JSON_PRETTY_PRINT));
        echo json_encode(['status' => 'success']);
        exit;
    }

    // Eliminar moderador (solo master)
    if (isset($_POST['action']) && $_POST['action'] === 'eliminar_moderador' && isset($_SESSION['admin']) && $_SESSION['role'] === 'master') {
        $username = trim($_POST['username'] ?? '');

        $admins = json_decode(file_get_contents($adminFile), true);
        $profiles = json_decode(file_get_contents($profilesFile), true) ?: [];

        if (!isset($admins[$username]) || $username === $_SESSION['admin']) {
            echo json_encode(['status' => 'error', 'message' => 'Usuario no válido']);
            exit;
        }

        unset($admins[$username]);
        if (isset($profiles[$username])) {
            unset($profiles[$username]);
        }

        file_put_contents($adminFile, json_encode($admins, JSON_PRETTY_PRINT));
        file_put_contents($profilesFile, json_encode($profiles, JSON_PRETTY_PRINT));

        echo json_encode(['status' => 'success']);
        exit;
    }

    // Restablecer contraseña (para usuarios con reset_password)
    if (isset($_POST['action']) && $_POST['action'] === 'restablecer_password' && isset($_SESSION['admin']) && ($_SESSION['reset_password'] ?? false)) {
        $username = $_SESSION['admin'];
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        $admins = json_decode(file_get_contents($adminFile), true);

        if (!isset($admins[$username])) {
            echo json_encode(['status' => 'error', 'message' => 'Usuario no encontrado']);
            exit;
        }

        if (!password_verify($current_password, $admins[$username]['password'])) {
            echo json_encode(['status' => 'error', 'message' => 'Contraseña actual incorrecta']);
            exit;
        }

        if ($new_password !== $confirm_password) {
            echo json_encode(['status' => 'error', 'message' => 'Las contraseñas no coinciden']);
            exit;
        }

        if (empty($new_password)) {
            echo json_encode(['status' => 'error', 'message' => 'La nueva contraseña no puede estar vacía']);
            exit;
        }

        $admins[$username]['password'] = password_hash($new_password, PASSWORD_DEFAULT);
        $admins[$username]['reset_password'] = false;
        $_SESSION['reset_password'] = false;

        file_put_contents($adminFile, json_encode($admins, JSON_PRETTY_PRINT));
        echo json_encode(['status' => 'success']);
        exit;
    }
}

// Cargar datos para JS
$coches = json_decode(file_get_contents($jsonFile), true) ?: [];
$blocked = json_decode(file_get_contents($blockedFile), true) ?: [];
$isAdmin = isset($_SESSION['admin']);
$role = $_SESSION['role'] ?? '';
$reset_password = $_SESSION['reset_password'] ?? false;

// Obtener nombre del admin para la bienvenida
$adminName = '';
if ($isAdmin) {
    $profiles = json_decode(file_get_contents($profilesFile), true) ?: [];
    if (isset($profiles[$_SESSION['admin']])) {
        $adminName = $profiles[$_SESSION['admin']]['nombre'] ?? $_SESSION['admin'];
    } else {
        $adminName = $_SESSION['admin'];
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <title>Plazas de aparcamiento - MooveCars Barcelona</title>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary-color: #2c3e50;
      --secondary-color: #3498db;
      --accent-color: #e74c3c;
      --light-color: #ecf0f1;
      --dark-color: #2c3e50;
      --success-color: #27ae60;
      --warning-color: #f39c12;
      --delete-color: #e74c3c;
      --recent-color: #2ecc71;
      --medium-color: #f1c40f;
      --old-color: #e74c3c;
      --blocked-color: #e74c3c;
      --clock-color: #2c3e50;
      --master-color: #9b59b6;
      --moderator-color: #3498db;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Roboto', sans-serif;
      line-height: 1.6;
      color: var(--dark-color);
      background-color: #f5f5f5;
      padding: 10px;
      min-height: 100vh;
      -webkit-text-size-adjust: 100%;
    }

    .container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 15px;
      background-color: white;
      border-radius: 8px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    h1 {
      color: var(--primary-color);
      text-align: center;
      margin-bottom: 15px;
      font-size: 1.5rem;
    }

    .clock {
      text-align: center;
      font-size: 1.2rem;
      color: var(--clock-color);
      margin-bottom: 15px;
      font-weight: bold;
    }

    .controls {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      margin-bottom: 15px;
    }

    .search-container {
      display: flex;
      gap: 8px;
      flex: 1;
      min-width: 100%;
      flex-wrap: wrap;
      position: relative;
    }

    .filter-container {
      display: flex;
      gap: 8px;
      align-items: center;
      width: 100%;
    }

    input[type="text"], select, input[type="date"] {
      flex: 1;
      min-width: 120px;
      padding: 10px 12px;
      border: 1px solid #ddd;
      border-radius: 5px;
      font-size: 14px;
      min-height: 44px;
    }

    button {
      padding: 10px 15px;
      background-color: var(--secondary-color);
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-size: 14px;
      transition: background-color 0.3s;
      white-space: nowrap;
      min-height: 44px;
      flex: 1;
    }

    button:hover {
      background-color: #2980b9;
    }

    button.delete {
      background-color: var(--delete-color);
    }

    button.delete:hover {
      background-color: #c0392b;
    }

    button.history {
      background-color: var(--primary-color);
    }

    button.history:hover {
      background-color: #1a252f;
    }

    button.login {
      background-color: var(--success-color);
    }

    button.login:hover {
      background-color: #219653;
    }

    button.profile {
      background-color: #9b59b6;
    }

    button.profile:hover {
      background-color: #8e44ad;
    }

    button.master {
      background-color: var(--master-color);
    }

    button.master:hover {
      background-color: #8e44ad;
    }

    .legend {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      margin-bottom: 15px;
      justify-content: center;
    }

    .legend-item {
      display: flex;
      align-items: center;
      font-size: 12px;
    }

    .legend-color {
      width: 15px;
      height: 15px;
      border-radius: 3px;
      margin-right: 5px;
      position: relative;
    }

    .legend-color.blocked {
      background-color: var(--blocked-color);
    }

    .legend-color.blocked::before {
      content: "";
      position: absolute;
      left: 0;
      top: 50%;
      width: 100%;
      height: 2px;
      background-color: white;
      transform: rotate(45deg);
    }

    #mapa {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(25px, 1fr));
      gap: 2px;
    }

    .plaza {
      width: 100%;
      aspect-ratio: 1;
      background-color: var(--light-color);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 7px;
      border-radius: 2px;
      cursor: pointer;
      transition: all 0.2s;
      position: relative;
      touch-action: manipulation;
    }

    .plaza:hover {
      transform: scale(1.1);
      z-index: 1;
      box-shadow: 0 0 3px rgba(0, 0, 0, 0.2);
    }

    .plaza.ocupada.reciente {
      background-color: var(--recent-color);
      color: white;
      font-weight: bold;
    }

    .plaza.ocupada.medio {
      background-color: var(--medium-color);
      color: var(--dark-color);
      font-weight: bold;
    }

    .plaza.ocupada.antiguo {
      background-color: var(--old-color);
      color: white;
      font-weight: bold;
    }

    .plaza.bloqueada {
      background-color: var(--blocked-color);
      color: white;
      font-weight: bold;
      position: relative;
      overflow: hidden;
    }

    .plaza.bloqueada::before {
      content: "";
      position: absolute;
      left: 0;
      top: 50%;
      width: 100%;
      height: 2px;
      background-color: white;
      box-shadow: 0 0 2px rgba(0,0,0,0.5);
      transform: rotate(45deg);
    }

    .plaza.resaltada {
      animation: pulse 1.5s infinite;
      box-shadow: 0 0 0 2px var(--secondary-color);
    }

    @keyframes pulse {
      0% { transform: scale(1); }
      50% { transform: scale(1.05); }
      100% { transform: scale(1); }
    }

    .popup {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.6);
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 100;
      opacity: 0;
      visibility: hidden;
      transition: all 0.3s;
    }

    .popup.active {
      opacity: 1;
      visibility: visible;
    }

    .popup-content {
      background-color: white;
      padding: 20px;
      border-radius: 8px;
      width: 95%;
      max-width: 500px;
      max-height: 90vh;
      overflow-y: auto;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
      position: relative;
    }

    .popup-close {
      position: absolute;
      top: 10px;
      right: 10px;
      background: none;
      border: none;
      font-size: 20px;
      cursor: pointer;
      color: #7f8c8d;
    }

    .popup-content h2 {
      margin-bottom: 12px;
      color: var(--primary-color);
      font-size: 1.3rem;
    }

    .popup-content p {
      margin-bottom: 12px;
      font-size: 0.9rem;
    }

    .popup-content label {
      display: block;
      margin-bottom: 5px;
      font-weight: 500;
      font-size: 0.9rem;
    }

    .popup-content input {
      width: 100%;
      padding: 10px;
      border: 1px solid #ddd;
      border-radius: 5px;
      margin-bottom: 12px;
      font-size: 14px;
      min-height: 44px;
    }

    .popup-actions {
      display: flex;
      gap: 8px;
      justify-content: flex-end;
    }

    .popup-actions button {
      padding: 8px 12px;
      min-height: 40px;
    }

    .popup-actions button.cancel {
      background-color: #95a5a6;
    }

    .popup-actions-column {
      flex-direction: column;
      gap: 8px;
    }

    .popup-actions-column button {
      width: 100%;
    }

    .plaza-info {
      position: absolute;
      bottom: 100%;
      left: 50%;
      transform: translateX(-50%);
      background-color: var(--dark-color);
      color: white;
      padding: 4px 8px;
      border-radius: 4px;
      font-size: 10px;
      white-space: nowrap;
      display: none;
      z-index: 2;
    }

    .plaza:hover .plaza-info {
      display: block;
    }

    .stats {
      display: flex;
      justify-content: space-between;
      margin-bottom: 15px;
      flex-wrap: wrap;
      gap: 8px;
    }

    .stat-card {
      background-color: white;
      padding: 12px;
      border-radius: 6px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      flex: 1;
      min-width: 100%;
    }

    .stat-card h3 {
      font-size: 12px;
      color: #7f8c8d;
      margin-bottom: 4px;
    }

    .stat-card p {
      font-size: 18px;
      font-weight: bold;
      color: var(--primary-color);
    }

    .history-item {
      padding: 8px 0;
      border-bottom: 1px solid #eee;
      display: flex;
      flex-direction: column;
      font-size: 12px;
    }

    .history-item:last-child {
      border-bottom: none;
    }

    .history-time {
      color: #7f8c8d;
      font-size: 0.8em;
    }

    .history-plate {
      font-weight: bold;
    }

    .history-action.entrada {
      color: var(--recent-color);
    }

    .history-action.salida {
      color: var(--old-color);
    }

    .login-container {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0,0,0,0.8);
      display: flex;
      justify-content: center;
      align-items: center;
      z-index: 1000;
    }

    .login-box {
      background: white;
      padding: 20px;
      border-radius: 8px;
      width: 90%;
      max-width: 400px;
    }

    .login-box h2 {
      margin-bottom: 20px;
      color: var(--primary-color);
    }

    .login-box input {
      width: 100%;
      padding: 10px;
      margin-bottom: 15px;
      border: 1px solid #ddd;
      border-radius: 4px;
    }

    .login-box button {
      width: 100%;
      padding: 10px;
      background-color: var(--secondary-color);
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }

    .admin-bar {
      background-color: var(--primary-color);
      color: white;
      padding: 10px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 15px;
      border-radius: 8px;
    }

    .admin-bar .role-badge {
      background-color: <?php echo ($role === 'master') ? 'var(--master-color)' : 'var(--moderator-color)'; ?>;
      padding: 3px 8px;
      border-radius: 4px;
      font-size: 0.8em;
      margin-left: 8px;
    }

    .admin-bar button {
      background-color: var(--delete-color);
      border: none;
      color: white;
      padding: 5px 10px;
      border-radius: 4px;
      cursor: pointer;
      margin-left: 5px;
    }

    .admin-bar button.profile {
      background-color: #9b59b6;
    }

    .login-button {
      position: fixed;
      bottom: 20px;
      right: 20px;
      background-color: var(--primary-color);
      color: white;
      border: none;
      border-radius: 50%;
      width: 50px;
      height: 50px;
      font-size: 24px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      box-shadow: 0 2px 5px rgba(0,0,0,0.2);
      z-index: 10;
    }

    .profile-info {
      margin-bottom: 15px;
      padding: 10px;
      background-color: #f8f9fa;
      border-radius: 5px;
    }

    .profile-info p {
      margin-bottom: 5px;
    }

    .profile-info strong {
      color: var(--primary-color);
    }

    .autocomplete-suggestions {
      position: absolute;
      top: 100%;
      left: 0;
      right: 0;
      background: white;
      border: 1px solid #ddd;
      border-top: none;
      border-radius: 0 0 5px 5px;
      z-index: 1000;
      max-height: 200px;
      overflow-y: auto;
      display: none;
    }

    .autocomplete-suggestion {
      padding: 8px 12px;
      cursor: pointer;
    }

    .autocomplete-suggestion:hover {
      background-color: #f5f5f5;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 15px;
    }

    th, td {
      padding: 8px;
      text-align: left;
      border-bottom: 1px solid #ddd;
    }

    th {
      background-color: #f2f2f2;
      font-weight: 500;
    }

    tr:hover {
      background-color: #f5f5f5;
    }

    .badge {
      display: inline-block;
      padding: 2px 6px;
      border-radius: 4px;
      font-size: 0.8em;
      font-weight: bold;
      color: white;
    }

    .badge-master {
      background-color: var(--master-color);
    }

    .badge-moderator {
      background-color: var(--moderator-color);
    }

    .badge-active {
      background-color: var(--success-color);
    }

    .badge-inactive {
      background-color: var(--delete-color);
    }

    .reset-password-popup {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0,0,0,0.8);
      display: flex;
      justify-content: center;
      align-items: center;
      z-index: 1001;
    }

    .reset-password-box {
      background: white;
      padding: 20px;
      border-radius: 8px;
      width: 90%;
      max-width: 400px;
    }

    .reset-password-box h2 {
      margin-bottom: 20px;
      color: var(--primary-color);
    }

    .reset-password-box input {
      width: 100%;
      padding: 10px;
      margin-bottom: 15px;
      border: 1px solid #ddd;
      border-radius: 4px;
    }

    .reset-password-box button {
      width: 100%;
      padding: 10px;
      background-color: var(--secondary-color);
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }

    @media (min-width: 481px) {
      h1 {
        font-size: 2rem;
      }

      .clock {
        font-size: 1.5rem;
      }

      #mapa {
        grid-template-columns: repeat(auto-fill, minmax(30px, 1fr));
        gap: 3px;
      }

      .plaza {
        font-size: 8px;
      }

      .search-container, .filter-container {
        flex-wrap: nowrap;
      }

      button {
        flex: initial;
      }

      .stat-card {
        min-width: 150px;
      }

      .stat-card h3 {
        font-size: 14px;
      }

      .stat-card p {
        font-size: 24px;
      }

      .history-item {
        flex-direction: row;
        justify-content: space-between;
        font-size: inherit;
      }

      .popup-actions-column {
        flex-direction: row;
        flex-wrap: wrap;
        justify-content: flex-end;
      }

      .popup-actions-column button {
        width: auto;
        flex: 1;
        min-width: 120px;
      }

      .legend {
        font-size: 14px;
      }

      .legend-color {
        width: 20px;
        height: 20px;
      }
    }

    @media (min-width: 769px) {
      body {
        padding: 20px;
      }

      .container {
        padding: 20px;
      }

      #mapa {
        grid-template-columns: repeat(auto-fill, minmax(40px, 1fr));
        gap: 4px;
      }

      .plaza {
        font-size: 10px;
      }

      .controls {
        gap: 10px;
      }

      .search-container, .filter-container {
        gap: 10px;
      }

      input[type="text"], select, input[type="date"] {
        padding: 10px 15px;
        font-size: 16px;
      }

      button {
        padding: 10px 20px;
        font-size: 16px;
      }

      .popup-content {
        padding: 25px;
        width: 90%;
      }
    }
  </style>
</head>
<body>
  <?php if ($isAdmin && $reset_password): ?>
  <div class="reset-password-popup">
    <div class="reset-password-box">
      <h2>Restablecer contraseña</h2>
      <p>Es necesario que cambies tu contraseña antes de continuar.</p>
      <input type="password" id="currentPassword" placeholder="Contraseña actual" autocomplete="current-password">
      <input type="password" id="newPassword" placeholder="Nueva contraseña" autocomplete="new-password">
      <input type="password" id="confirmPassword" placeholder="Confirmar nueva contraseña" autocomplete="new-password">
      <button onclick="restablecerPassword()">Cambiar contraseña</button>
    </div>
  </div>
  <?php endif; ?>

  <?php if ($isAdmin && !$reset_password): ?>
  <div class="admin-bar">
    <span>Modo Admin: Bienvenido <?php echo htmlspecialchars($adminName); ?> <span class="role-badge"><?php echo strtoupper($role); ?></span></span>
    <div>
      <button class="profile" onclick="mostrarPerfil()">Perfil</button>
      <?php if ($role === 'master'): ?>
        <button onclick="mostrarModeradores()">Moderadores</button>
        <button class="master" onclick="mostrarVaciarPlazas()">Vaciar Plazas</button>
      <?php endif; ?>
      <button onclick="logout()">Cerrar sesión</button>
    </div>
  </div>
  <?php endif; ?>

  <div class="container">
    <h1>Plazas de aparcamiento - MooveCars Barcelona</h1>
    <div class="clock" id="reloj"></div>

    <div class="stats">
      <div class="stat-card">
        <h3>Plazas totales</h3>
        <p>900</p>
      </div>
      <div class="stat-card">
        <h3>Plazas ocupadas</h3>
        <p id="plazas-ocupadas"><?php echo count($coches); ?></p>
      </div>
      <div class="stat-card">
        <h3>Plazas libres</h3>
        <p id="plazas-libres"><?php echo 900 - count($coches) - count($blocked); ?></p>
      </div>
      <div class="stat-card">
        <h3>Plazas bloqueadas</h3>
        <p id="plazas-bloqueadas"><?php echo count($blocked); ?></p>
      </div>
    </div>

    <div class="legend">
      <div class="legend-item">
        <div class="legend-color" style="background-color: var(--recent-color);"></div>
        <span>Ocupado (menos de 4h)</span>
      </div>
      <div class="legend-item">
        <div class="legend-color" style="background-color: var(--medium-color);"></div>
        <span>Ocupado (4-8h)</span>
      </div>
      <div class="legend-item">
        <div class="legend-color" style="background-color: var(--old-color);"></div>
        <span>Ocupado (más de 8h)</span>
      </div>
      <div class="legend-item">
        <div class="legend-color" style="background-color: var(--light-color);"></div>
        <span>Libre</span>
      </div>
      <div class="legend-item">
        <div class="legend-color blocked"></div>
        <span>Bloqueada</span>
      </div>
    </div>

    <div class="controls">
      <div class="search-container">
        <input type="text" id="matriculaInput" placeholder="Introduce la matrícula (ej: 1234ABC)" autocomplete="off" autofocus>
        <div id="autocompleteSuggestions" class="autocomplete-suggestions"></div>
        <button onclick="buscarMatricula()">Buscar</button>
        <?php if (!$isAdmin): ?>
        <button class="login" onclick="mostrarPopup('loginContainer')">Login</button>
        <?php else: ?>
        <button class="history" onclick="mostrarHistorial()">Historial</button>
        <?php endif; ?>
      </div>

      <div class="filter-container">
        <select id="filtroEstado" onchange="filtrarPlazas()">
          <option value="todas">Todas las plazas</option>
          <option value="ocupadas">Plazas ocupadas</option>
          <option value="libres">Plazas libres</option>
          <?php if ($isAdmin): ?>
          <option value="bloqueadas">Plazas bloqueadas</option>
          <?php endif; ?>
        </select>
      </div>
    </div>

    <div id="mapa"></div>
  </div>

  <!-- Popups -->
  <div id="popupCocheEncontrado" class="popup">
    <div class="popup-content">
      <button class="popup-close" onclick="cerrarPopup('popupCocheEncontrado')">×</button>
      <h2>Vehículo encontrado</h2>
      <p>El vehículo con matrícula <strong id="matriculaEncontrada"></strong> está estacionado en la plaza <strong id="plazaEncontrada"></strong>.</p>
      <div class="popup-actions">
        <button class="delete" onclick="confirmarSacarCoche()">Sacar del parking</button>
      </div>
    </div>
  </div>

  <div id="popupCocheNoEncontrado" class="popup">
    <div class="popup-content">
      <button class="popup-close" onclick="cerrarPopup('popupCocheNoEncontrado')">×</button>
      <h2>Vehículo no encontrado</h2>
      <p>No se encontró el vehículo con matrícula <strong id="matriculaNoEncontrada"></strong> en el sistema.</p>
      <p>Los vehículos estacionados en las plazas bloqueadas no están registrados en esta web, ya que son vehículos en reparación o mantenimiento, aunque podrían estar ya listos para circular.</p>
      <p>Verifica que has escrito la matrícula correctamente o acércate a la zona de vehículos bloqueados con la aplicación GeoTab abierta.</p>
      <p>Si lo que deseas es aparcar tu vehículo, solo pulsa "Aparcar vehículo".</p>
      <div class="popup-actions popup-actions-column">
        <button onclick="volverAIntroducirMatricula()">Matrícula incorrecta</button>
        <button onclick="mostrarPopupAparcar()">Aparcar vehículo</button>
      </div>
    </div>
  </div>

  <div id="popupAparcar" class="popup">
    <div class="popup-content">
      <button class="popup-close" onclick="cerrarPopup('popupAparcar')">×</button>
      <h2>Aparcar vehículo</h2>
      <p>Introduce el número de plaza para la matrícula <strong id="matriculaAparcar"></strong>:</p>
      <input type="number" id="plazaAparcar" min="1" max="900" placeholder="Número de plaza (1-900)">
      <div class="popup-actions">
        <button onclick="guardarNuevaPlaza()">Confirmar</button>
      </div>
    </div>
  </div>

  <div id="popupEliminar" class="popup">
    <div class="popup-content">
      <button class="popup-close" onclick="cerrarPopup('popupEliminar')">×</button>
      <h2>Sacar coche del parking</h2>
      <p>Introduce la matrícula del coche que vas a sacar:</p>
      <input type="text" id="matriculaEliminar" placeholder="Matrícula (ej: 1234ABC)">
      <div class="popup-actions">
        <button class="delete" onclick="eliminarCoche()">Confirmar</button>
      </div>
    </div>
  </div>

  <div id="popupHistorial" class="popup">
    <div class="popup-content">
      <button class="popup-close" onclick="cerrarPopup('popupHistorial')">×</button>
      <h2>Historial de movimientos</h2>
      <div class="filter-container" style="margin-bottom: 15px;">
        <input type="text" id="historialMatricula" placeholder="Filtrar por matrícula">
        <input type="date" id="historialFechaInicio">
        <input type="date" id="historialFechaFin">
        <button onclick="filtrarHistorial()">BUSCAR</button>
      </div>
      <div id="historialContenido"></div>
    </div>
  </div>

  <div id="popupPerfil" class="popup">
    <div class="popup-content">
      <button class="popup-close" onclick="cerrarPopup('popupPerfil')">×</button>
      <h2>Perfil de Administrador</h2>
      <div id="perfilInfo" class="profile-info"></div>
      <form id="formPerfil">
        <label for="perfilNombre">Nombre:</label>
        <input type="text" id="perfilNombre" placeholder="Nombre">

        <label for="perfilApellidos">Apellidos:</label>
        <input type="text" id="perfilApellidos" placeholder="Apellidos">

        <label for="perfilEmail">Correo electrónico:</label>
        <input type="email" id="perfilEmail" placeholder="Correo electrónico">

        <label for="perfilTelefono">Teléfono móvil:</label>
        <input type="tel" id="perfilTelefono" placeholder="Teléfono móvil">

        <div class="popup-actions">
          <button type="button" class="cancel" onclick="cerrarPopup('popupPerfil')">Cancelar</button>
          <button type="button" onclick="guardarPerfil()">Guardar</button>
        </div>
      </form>
    </div>
  </div>

  <div id="popupVaciarPlazas" class="popup">
    <div class="popup-content">
      <button class="popup-close" onclick="cerrarPopup('popupVaciarPlazas')">×</button>
      <h2>Vaciar todas las plazas</h2>
      <p>¿Estás seguro de que deseas vaciar todas las plazas del parking?</p>
      <p>Esta acción registrará la salida de todos los vehículos en el historial pero mantendrá las matrículas en el sistema.</p>
      <div class="popup-actions">
        <button class="cancel" onclick="cerrarPopup('popupVaciarPlazas')">Cancelar</button>
        <button class="master" onclick="vaciarTodasLasPlazas()">Confirmar</button>
      </div>
    </div>
  </div>

  <div id="popupModeradores" class="popup">
    <div class="popup-content">
      <button class="popup-close" onclick="cerrarPopup('popupModeradores')">×</button>
      <h2>Gestión de Moderadores</h2>

      <div class="controls" style="margin-bottom: 15px;">
        <button onclick="mostrarAgregarModerador()">➕ Añadir Moderador</button>
      </div>

      <div id="listaModeradores"></div>
    </div>
  </div>

  <div id="popupAgregarModerador" class="popup">
    <div class="popup-content">
      <button class="popup-close" onclick="cerrarPopup('popupAgregarModerador')">×</button>
      <h2>Añadir Nuevo Moderador</h2>

      <label for="nuevoModeradorUsuario">Usuario:</label>
      <input type="text" id="nuevoModeradorUsuario" placeholder="Nombre de usuario">

      <label for="nuevoModeradorPassword">Contraseña:</label>
      <input type="password" id="nuevoModeradorPassword" placeholder="Contraseña">

      <label for="nuevoModeradorRol">Rol:</label>
      <select id="nuevoModeradorRol">
        <option value="moderador">Moderador</option>
        <option value="master">Master</option>
      </select>

      <div class="popup-actions">
        <button class="cancel" onclick="cerrarPopup('popupAgregarModerador')">Cancelar</button>
        <button onclick="agregarModerador()">Guardar</button>
      </div>
    </div>
  </div>

  <div id="popupEditarModerador" class="popup">
    <div class="popup-content">
      <button class="popup-close" onclick="cerrarPopup('popupEditarModerador')">×</button>
      <h2>Editar Moderador</h2>
      <input type="hidden" id="editarModeradorUsuario">

      <div id="moderadorInfo" class="profile-info"></div>

      <label for="editarModeradorRol">Rol:</label>
      <select id="editarModeradorRol">
        <option value="moderador">Moderador</option>
        <option value="master">Master</option>
      </select>

      <label for="editarModeradorActivo">Estado:</label>
      <select id="editarModeradorActivo">
        <option value="1">Activo</option>
        <option value="0">Inactivo</option>
      </select>

      <div class="popup-actions">
        <button onclick="mostrarCambiarPassword()">Cambiar Contraseña</button>
        <button class="delete" onclick="eliminarModerador()">Eliminar Moderador</button>
        <button onclick="actualizarModerador()">Guardar Cambios</button>
      </div>
    </div>
  </div>

  <div id="popupCambiarPassword" class="popup">
    <div class="popup-content">
      <button class="popup-close" onclick="cerrarPopup('popupCambiarPassword')">×</button>
      <h2>Cambiar Contraseña</h2>
      <input type="hidden" id="cambiarPasswordUsuario">

      <label for="nuevaPassword">Nueva Contraseña:</label>
      <input type="password" id="nuevaPassword" placeholder="Nueva contraseña">

      <label for="confirmarPassword">Confirmar Contraseña:</label>
      <input type="password" id="confirmarPassword" placeholder="Confirmar contraseña">

      <div class="popup-actions">
        <button class="cancel" onclick="cerrarPopup('popupCambiarPassword')">Cancelar</button>
        <button onclick="cambiarPasswordModerador()">Guardar</button>
      </div>
    </div>
  </div>

  <div id="popupEliminarModerador" class="popup">
    <div class="popup-content">
      <button class="popup-close" onclick="cerrarPopup('popupEliminarModerador')">×</button>
      <h2>Eliminar Moderador</h2>
      <p>¿Estás seguro de que deseas eliminar al moderador <strong id="moderadorAEliminar"></strong>?</p>
      <p>Esta acción no se puede deshacer.</p>
      <input type="hidden" id="eliminarModeradorUsuario">
      <div class="popup-actions">
        <button class="cancel" onclick="cerrarPopup('popupEliminarModerador')">Cancelar</button>
        <button class="delete" onclick="confirmarEliminarModerador()">Eliminar</button>
      </div>
    </div>
  </div>

  <div id="loginContainer" class="popup">
    <div class="popup-content">
      <button class="popup-close" onclick="cerrarPopup('loginContainer')">×</button>
      <h2>Acceso Administrador</h2>
      <input type="text" id="loginUser" placeholder="Usuario" autocomplete="username">
      <input type="password" id="loginPass" placeholder="Contraseña" autocomplete="current-password">
      <div class="popup-actions">
        <button onclick="login()">Entrar</button>
      </div>
    </div>
  </div>

  <?php if (!$isAdmin): ?>
  <button class="login-button" onclick="mostrarPopup('loginContainer')">🔑</button>
  <?php endif; ?>

  <script>
    const coches = <?php echo json_encode($coches); ?>;
    const blockedPlazas = <?php echo json_encode($blocked); ?>;
    const isAdmin = <?php echo $isAdmin ? 'true' : 'false'; ?>;
    const role = '<?php echo $role; ?>';
    const reset_password = <?php echo $reset_password ? 'true' : 'false'; ?>;
    let matriculaPendiente = '';
    let filtroActual = 'todas';
    let autocompleteTimeout = null;

    // Función para asignar eventos de forma segura
    const asignarEvento = (id, evento, callback) => {
      const elemento = document.getElementById(id);
      if (elemento) {
        elemento.addEventListener(evento, callback);
      }
    };

    function actualizarReloj() {
      const ahora = new Date();
      const opciones = {
        timeZone: 'Europe/Madrid',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: false
      };
      const horaEspaña = ahora.toLocaleTimeString('es-ES', opciones);
      const fechaEspaña = ahora.toLocaleDateString('es-ES', {
        timeZone: 'Europe/Madrid',
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      });
      document.getElementById('reloj').innerHTML = `${fechaEspaña} - ${horaEspaña}`;
    }

    function formatTime(timestamp) {
      const date = new Date(timestamp * 1000);
      const opciones = {
        timeZone: 'Europe/Madrid',
        hour: '2-digit',
        minute:'2-digit',
        hour12: false
      };
      return date.toLocaleTimeString('es-ES', opciones);
    }

    function formatDate(timestamp) {
      const date = new Date(timestamp * 1000);
      const opciones = {
        timeZone: 'Europe/Madrid',
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        hour12: false
      };
      return date.toLocaleString('es-ES', opciones);
    }

    function formatDuration(timestamp) {
      const ahora = Math.floor(Date.now() / 1000);
      const segundos = ahora - timestamp;

      if (segundos < 60) {
        return `${segundos} segundos`;
      }

      const minutos = Math.floor(segundos / 60);
      if (minutos < 60) {
        return `${minutos} minutos`;
      }

      const horas = Math.floor(minutos / 60);
      const minutosRestantes = minutos % 60;

      if (horas < 24) {
        return `${horas}h ${minutosRestantes}m`;
      }

      const dias = Math.floor(horas / 24);
      const horasRestantes = horas % 24;
      return `${dias}d ${horasRestantes}h`;
    }

    function actualizarEstadisticas() {
      const ocupadas = Object.keys(coches).length;
      const bloqueadas = Object.keys(blockedPlazas).length;
      document.getElementById('plazas-ocupadas').textContent = ocupadas;
      document.getElementById('plazas-libres').textContent = 900 - ocupadas - bloqueadas;
      document.getElementById('plazas-bloqueadas').textContent = bloqueadas;
    }

    function login() {
      const user = document.getElementById('loginUser').value;
      const pass = document.getElementById('loginPass').value;

      fetch('index.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `action=login&username=${encodeURIComponent(user)}&password=${encodeURIComponent(pass)}`
      })
      .then(res => res.json())
      .then(data => {
        if (data.status === 'success') {
          location.reload();
        } else {
          alert('Error: ' + (data.message || 'Credenciales incorrectas'));
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Error de conexión');
      });
    }

    function restablecerPassword() {
      const currentPassword = document.getElementById('currentPassword').value;
      const newPassword = document.getElementById('newPassword').value;
      const confirmPassword = document.getElementById('confirmPassword').value;

      if (!currentPassword || !newPassword || !confirmPassword) {
        alert('Todos los campos son obligatorios');
        return;
      }

      if (newPassword !== confirmPassword) {
        alert('Las contraseñas no coinciden');
        return;
      }

      fetch('index.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `action=restablecer_password&current_password=${encodeURIComponent(currentPassword)}&new_password=${encodeURIComponent(newPassword)}&confirm_password=${encodeURIComponent(confirmPassword)}`
      })
      .then(res => res.json())
      .then(data => {
        if (data.status === 'success') {
          location.reload();
        } else {
          alert('Error: ' + (data.message || 'Error al cambiar la contraseña'));
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Error de conexión');
      });
    }

    function logout() {
      fetch('index.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'action=logout'
      })
      .then(() => location.reload());
    }

    function toggleBlockPlaza(plaza) {
      if (!isAdmin) return;

      fetch('index.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `action=toggle_block&plaza=${plaza}`
      })
      .then(res => res.json())
      .then(data => {
        if (data.status === 'success') {
          if (data.blocked) {
            blockedPlazas[plaza] = true;
          } else {
            delete blockedPlazas[plaza];
          }
          crearMapa();
          actualizarEstadisticas();
        }
      });
    }

    function crearMapa() {
      const contenedor = document.getElementById('mapa');
      contenedor.innerHTML = '';

      for (let i = 1; i <= 900; i++) {
        const div = document.createElement('div');
        div.className = 'plaza';
        div.textContent = i.toString().padStart(3, '0');
        div.id = 'plaza-' + i;
        div.dataset.plaza = i;

        // Verificar si la plaza está bloqueada
        if (blockedPlazas[i]) {
          div.classList.add('bloqueada');
          if (isAdmin) {
            div.onclick = () => toggleBlockPlaza(i);
          }
        } else {
          // Verificar si la plaza está ocupada
          const matriculaEnPlaza = Object.entries(coches).find(([_, datos]) => datos.plaza === i);

          if (matriculaEnPlaza) {
            const ahora = Math.floor(Date.now() / 1000);
            const horasOcupacion = (ahora - matriculaEnPlaza[1].timestamp) / 3600;

            if (horasOcupacion < 4) {
              div.classList.add('ocupada', 'reciente');
            } else if (horasOcupacion < 8) {
              div.classList.add('ocupada', 'medio');
            } else {
              div.classList.add('ocupada', 'antiguo');
            }

            if (window.innerWidth > 480) {
              const info = document.createElement('div');
              info.className = 'plaza-info';
              info.innerHTML = `
                <div>${matriculaEnPlaza[0]}</div>
                <div>${formatDate(matriculaEnPlaza[1].timestamp)}</div>
                <div>${formatDuration(matriculaEnPlaza[1].timestamp)}</div>
              `;
              div.appendChild(info);
            }
          } else if (isAdmin) {
            div.onclick = () => toggleBlockPlaza(i);
          }
        }

        // Aplicar filtro
        if (filtroActual === 'ocupadas') {
          if (!div.classList.contains('ocupada')) {
            div.style.display = 'none';
          } else {
            div.style.display = 'flex';
          }
        } else if (filtroActual === 'libres') {
          if (div.classList.contains('ocupada') || div.classList.contains('bloqueada')) {
            div.style.display = 'none';
          } else {
            div.style.display = 'flex';
          }
        } else if (filtroActual === 'bloqueadas') {
          if (!div.classList.contains('bloqueada')) {
            div.style.display = 'none';
          } else {
            div.style.display = 'flex';
          }
        } else {
          div.style.display = 'flex';
        }

        contenedor.appendChild(div);
      }

      actualizarEstadisticas();
    }

    function filtrarPlazas() {
      filtroActual = document.getElementById('filtroEstado').value;
      crearMapa();
    }

    function validarMatricula(matricula) {
      const matriculaLimpia = matricula.toUpperCase().replace('-', '');
      return /^[0-9BCDFGHJKLMNPRSTVWXYZ]{4}[0-9BCDFGHJKLMNPRSTVWXYZ]{3}$/.test(matriculaLimpia);
    }

    function buscarMatricula() {
      const input = document.getElementById('matriculaInput').value.toUpperCase().trim();
      if (!input) {
        alert('Por favor, introduce una matrícula');
        return;
      }

      if (!validarMatricula(input)) {
        alert('Formato de matrícula inválido. Debe ser 1234ABC o 1234-ABC (sin vocales ni Q/Ñ)');
        return;
      }

      const matriculaLimpia = input.replace('-', '');

      document.querySelectorAll('.plaza').forEach(p => p.classList.remove('resaltada'));

      if (coches[matriculaLimpia]) {
        matriculaPendiente = matriculaLimpia;
        document.getElementById('matriculaEncontrada').textContent = matriculaLimpia;
        document.getElementById('plazaEncontrada').textContent = coches[matriculaLimpia].plaza.toString().padStart(3, '0');
        mostrarPopup('popupCocheEncontrado');

        const plaza = coches[matriculaLimpia].plaza;
        const div = document.getElementById('plaza-' + plaza);
        if (div) {
          div.classList.add('resaltada');
          div.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
      } else {
        matriculaPendiente = matriculaLimpia;
        document.getElementById('matriculaNoEncontrada').textContent = input;
        mostrarPopup('popupCocheNoEncontrado');
      }
    }

    function volverAIntroducirMatricula() {
      cerrarPopup('popupCocheNoEncontrado');
      document.getElementById('matriculaInput').value = '';
      document.getElementById('matriculaInput').focus();
    }

    function confirmarSacarCoche() {
      eliminarCoche(matriculaPendiente);
      cerrarPopup('popupCocheEncontrado');
    }

    function mostrarPopupAparcar() {
      document.getElementById('matriculaAparcar').textContent = matriculaPendiente;
      cerrarPopup('popupCocheNoEncontrado');
      mostrarPopup('popupAparcar');
      document.getElementById('plazaAparcar').focus();
    }

    function mostrarPopup(id) {
      document.getElementById(id).classList.add('active');
    }

    function cerrarPopup(id) {
      document.getElementById(id).classList.remove('active');
      document.getElementById('matriculaInput').focus();
    }

    function filtrarHistorial() {
      const matricula = document.getElementById('historialMatricula').value.toUpperCase().replace('-', '');
      const fechaInicio = document.getElementById('historialFechaInicio').value;
      const fechaFin = document.getElementById('historialFechaFin').value;

      fetch('index.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `action=obtener_historial&matricula=${encodeURIComponent(matricula)}&fechaInicio=${fechaInicio}&fechaFin=${fechaFin}`
      })
      .then(res => res.json())
      .then(data => {
        if (data.status === 'success') {
          mostrarHistorialContenido(data.data);
        } else {
          document.getElementById('historialContenido').innerHTML = '<p>Error al cargar el historial</p>';
        }
      })
      .catch(error => {
        console.error('Error:', error);
        document.getElementById('historialContenido').innerHTML = '<p>Error al cargar el historial</p>';
      });
    }

    function mostrarHistorialContenido(historial) {
      const contenido = document.getElementById('historialContenido');
      contenido.innerHTML = '';

      if (historial.length === 0) {
        contenido.innerHTML = '<p>No hay registros en el historial</p>';
        return;
      }

      historial.forEach(item => {
        const div = document.createElement('div');
        div.className = 'history-item';
        div.innerHTML = `
          <div>
            <span class="history-time">${formatDate(item.timestamp)}</span>
            <span class="history-plate">${item.matricula}</span>
          </div>
          <div>
            <span class="history-action ${item.accion}">${item.accion === 'entrada' ? 'Entrada' : 'Salida'}</span>
            <span>Plaza ${item.plaza.toString().padStart(3, '0')}</span>
          </div>
        `;
        contenido.appendChild(div);
      });
    }

    function mostrarHistorial() {
      mostrarPopup('popupHistorial');
      document.getElementById('historialMatricula').value = '';
      document.getElementById('historialFechaInicio').value = '';
      document.getElementById('historialFechaFin').value = '';
      filtrarHistorial();
    }

    function guardarNuevaPlaza() {
      const plazaInput = document.getElementById('plazaAparcar');
      let plaza = plazaInput.value;

      plaza = parseInt(plaza);
      if (!plaza || plaza < 1 || plaza > 900) {
        alert('Por favor, introduce un número de plaza válido (1-900)');
        return;
      }

      if (blockedPlazas[plaza] && !isAdmin) {
        alert('Esta plaza está bloqueada para mantenimiento');
        return;
      }

      const plazaOcupada = Object.values(coches).some(coche => coche.plaza === plaza);
      if (plazaOcupada) {
        if (!confirm(`La plaza ${plaza.toString().padStart(3, '0')} ya está ocupada. ¿Deseas sobrescribirla?`)) {
          return;
        }
      }

      fetch('index.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `action=guardar&matricula=${encodeURIComponent(matriculaPendiente)}&plaza=${plaza}`
      })
      .then(res => res.json())
      .then(data => {
        if (data.status === 'success') {
          coches[matriculaPendiente] = { plaza, timestamp: Math.floor(Date.now() / 1000) };
          cerrarPopup('popupAparcar');
          plazaInput.value = '';
          crearMapa();

          const div = document.getElementById('plaza-' + plaza);
          if (div) {
            div.classList.add('resaltada');
            div.scrollIntoView({ behavior: 'smooth', block: 'center' });
          }
        } else {
          alert('Error al guardar: ' + (data.message || 'Error desconocido'));
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Error de conexión');
      });
    }

    function eliminarCoche(matricula = null) {
      const matriculaInput = matricula || document.getElementById('matriculaEliminar').value.toUpperCase().replace('-', '');

      if (!matriculaInput) {
        alert('Por favor, introduce una matrícula');
        return;
      }

      if (!validarMatricula(matriculaInput)) {
        alert('Formato de matrícula inválido. Debe ser 1234ABC o 1234-ABC (sin vocales ni Q/Ñ)');
        return;
      }

      if (!confirm(`¿Estás seguro de que quieres sacar el coche con matrícula ${matriculaInput} del parking?`)) {
        return;
      }

      fetch('index.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `action=eliminar&matricula=${encodeURIComponent(matriculaInput)}`
      })
      .then(res => res.json())
      .then(data => {
        if (data.status === 'success') {
          delete coches[matriculaInput];
          if (!matricula) {
            cerrarPopup('popupEliminar');
            document.getElementById('matriculaEliminar').value = '';
          }
          crearMapa();
          document.querySelectorAll('.plaza').forEach(p => p.classList.remove('resaltada'));
        } else {
          alert('Error al eliminar: ' + (data.message || 'Matrícula no encontrada'));
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Error de conexión');
      });
    }

    function mostrarVaciarPlazas() {
      if (role !== 'master') return;
      mostrarPopup('popupVaciarPlazas');
    }

    function vaciarTodasLasPlazas() {
      if (role !== 'master') return;

      if (!confirm('¿Estás seguro de que deseas vaciar TODAS las plazas del parking?')) {
        return;
      }

      fetch('index.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'action=vaciar_plazas'
      })
      .then(res => res.json())
      .then(data => {
        if (data.status === 'success') {
          alert(`Se han vaciado ${data.count} plazas correctamente`);
          for (const matricula in coches) {
            delete coches[matricula];
          }
          crearMapa();
          cerrarPopup('popupVaciarPlazas');
        } else {
          alert('Error al vaciar las plazas');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Error de conexión');
      });
    }

    function mostrarPerfil() {
      fetch('index.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'action=obtener_perfil'
      })
      .then(res => res.json())
      .then(data => {
        if (data.status === 'success') {
          const perfil = data.data;
          const perfilInfo = document.getElementById('perfilInfo');

          if (Object.keys(perfil).length > 0) {
            perfilInfo.innerHTML = `
              <p><strong>Nombre:</strong> ${perfil.nombre || 'No especificado'}</p>
              <p><strong>Apellidos:</strong> ${perfil.apellidos || 'No especificados'}</p>
              <p><strong>Email:</strong> ${perfil.email || 'No especificado'}</p>
              <p><strong>Teléfono:</strong> ${perfil.telefono || 'No especificado'}</p>
              <p><strong>Última actualización:</strong> ${formatDate(perfil.actualizado)}</p>
            `;
          } else {
            perfilInfo.innerHTML = '<p>No hay información de perfil guardada.</p>';
          }

          // Rellenar los campos del formulario
          document.getElementById('perfilNombre').value = perfil.nombre || '';
          document.getElementById('perfilApellidos').value = perfil.apellidos || '';
          document.getElementById('perfilEmail').value = perfil.email || '';
          document.getElementById('perfilTelefono').value = perfil.telefono || '';

          mostrarPopup('popupPerfil');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Error al cargar el perfil');
      });
    }

    function guardarPerfil() {
      const perfil = {
        nombre: document.getElementById('perfilNombre').value.trim(),
        apellidos: document.getElementById('perfilApellidos').value.trim(),
        email: document.getElementById('perfilEmail').value.trim(),
        telefono: document.getElementById('perfilTelefono').value.trim()
      };

      fetch('index.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `action=guardar_perfil&nombre=${encodeURIComponent(perfil.nombre)}&apellidos=${encodeURIComponent(perfil.apellidos)}&email=${encodeURIComponent(perfil.email)}&telefono=${encodeURIComponent(perfil.telefono)}`
      })
      .then(res => res.json())
      .then(data => {
        if (data.status === 'success') {
          alert('Perfil guardado correctamente');
          mostrarPerfil(); // Actualizar la vista
        } else {
          alert('Error al guardar el perfil');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Error de conexión');
      });
    }

    function buscarAutocompletado(query) {
      if (query.length < 2) {
        document.getElementById('autocompleteSuggestions').style.display = 'none';
        return;
      }

      fetch('index.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `action=autocompletar_matriculas&query=${encodeURIComponent(query)}`
      })
      .then(res => res.json())
      .then(data => {
        const suggestionsContainer = document.getElementById('autocompleteSuggestions');
        suggestionsContainer.innerHTML = '';

        if (data.status === 'success' && data.sugerencias.length > 0) {
          data.sugerencias.forEach(matricula => {
            const div = document.createElement('div');
            div.className = 'autocomplete-suggestion';
            div.textContent = matricula;
            div.onclick = () => {
              document.getElementById('matriculaInput').value = matricula;
              suggestionsContainer.style.display = 'none';
              buscarMatricula();
            };
            suggestionsContainer.appendChild(div);
          });
          suggestionsContainer.style.display = 'block';
        } else {
          suggestionsContainer.style.display = 'none';
        }
      });
    }

    function mostrarModeradores() {
      if (role !== 'master') return;

      fetch('index.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'action=obtener_moderadores'
      })
      .then(res => res.json())
      .then(data => {
        if (data.status === 'success') {
          mostrarListaModeradores(data.data);
          mostrarPopup('popupModeradores');
        } else {
          alert('Error al cargar moderadores');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Error de conexión');
      });
    }

    function mostrarListaModeradores(moderadores) {
      const lista = document.getElementById('listaModeradores');
      lista.innerHTML = '';

      if (moderadores.length === 0) {
        lista.innerHTML = '<p>No hay moderadores registrados</p>';
        return;
      }

      const table = document.createElement('table');
      table.style.width = '100%';
      table.style.borderCollapse = 'collapse';

      // Cabecera
      const thead = document.createElement('thead');
      thead.innerHTML = `
        <tr>
          <th style="text-align: left; padding: 8px; border-bottom: 1px solid #ddd;">Usuario</th>
          <th style="text-align: left; padding: 8px; border-bottom: 1px solid #ddd;">Rol</th>
          <th style="text-align: left; padding: 8px; border-bottom: 1px solid #ddd;">Estado</th>
          <th style="text-align: left; padding: 8px; border-bottom: 1px solid #ddd;">Último login</th>
          <th style="text-align: left; padding: 8px; border-bottom: 1px solid #ddd;">Acciones</th>
        </tr>
      `;
      table.appendChild(thead);

      // Cuerpo
      const tbody = document.createElement('tbody');
      moderadores.forEach(mod => {
        const tr = document.createElement('tr');
        tr.style.borderBottom = '1px solid #eee';

        // Estado como badge
        const estado = mod.active ?
          '<span class="badge badge-active">✔ Activo</span>' :
          '<span class="badge badge-inactive">✖ Inactivo</span>';

        // Rol como badge
        const rolClass = mod.role === 'master' ? 'badge-master' : 'badge-moderator';
        const rol = `<span class="badge ${rolClass}">${mod.role.toUpperCase()}</span>`;

        // Último login formateado
        const lastLogin = mod.last_login ? formatDate(mod.last_login) : 'Nunca';

        tr.innerHTML = `
          <td style="padding: 8px;">${mod.username}</td>
          <td style="padding: 8px;">${rol}</td>
          <td style="padding: 8px;">${estado}</td>
          <td style="padding: 8px;">${lastLogin}</td>
          <td style="padding: 8px;">
            <button style="padding: 4px 8px; font-size: 12px;" onclick="editarModerador('${mod.username}')">Editar</button>
          </td>
        `;
        tbody.appendChild(tr);
      });
      table.appendChild(tbody);
      lista.appendChild(table);
    }

    function mostrarAgregarModerador() {
      document.getElementById('nuevoModeradorUsuario').value = '';
      document.getElementById('nuevoModeradorPassword').value = '';
      document.getElementById('nuevoModeradorRol').value = 'moderador';
      mostrarPopup('popupAgregarModerador');
    }

    function agregarModerador() {
      const username = document.getElementById('nuevoModeradorUsuario').value.trim();
      const password = document.getElementById('nuevoModeradorPassword').value;
      const role = document.getElementById('nuevoModeradorRol').value;

      if (!username || !password) {
        alert('Usuario y contraseña son obligatorios');
        return;
      }

      fetch('index.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `action=agregar_moderador&username=${encodeURIComponent(username)}&password=${encodeURIComponent(password)}&role=${encodeURIComponent(role)}`
      })
      .then(res => res.json())
      .then(data => {
        if (data.status === 'success') {
          alert('Moderador agregado correctamente');
          cerrarPopup('popupAgregarModerador');
          mostrarModeradores(); // Actualizar lista
        } else {
          alert('Error: ' + (data.message || 'Error desconocido'));
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Error de conexión');
      });
    }

    function editarModerador(username) {
      fetch('index.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `action=obtener_moderadores`
      })
      .then(res => res.json())
      .then(data => {
        if (data.status === 'success') {
          const moderador = data.data.find(m => m.username === username);
          if (moderador) {
            document.getElementById('editarModeradorUsuario').value = username;
            document.getElementById('editarModeradorRol').value = moderador.role;
            document.getElementById('editarModeradorActivo').value = moderador.active ? '1' : '0';

            // Mostrar información del perfil
            const infoDiv = document.getElementById('moderadorInfo');
            if (moderador.profile) {
              infoDiv.innerHTML = `
                <p><strong>Nombre:</strong> ${moderador.profile.nombre || 'No especificado'}</p>
                <p><strong>Apellidos:</strong> ${moderador.profile.apellidos || 'No especificados'}</p>
                <p><strong>Email:</strong> ${moderador.profile.email || 'No especificado'}</p>
                <p><strong>Teléfono:</strong> ${moderador.profile.telefono || 'No especificado'}</p>
                <p><strong>Registrado:</strong> ${formatDate(moderador.created_at)}</p>
                <p><strong>Último login:</strong> ${moderador.last_login ? formatDate(moderador.last_login) : 'Nunca'}</p>
              `;
            } else {
              infoDiv.innerHTML = '<p>No hay información de perfil disponible</p>';
            }

            mostrarPopup('popupEditarModerador');
          }
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Error al cargar datos del moderador');
      });
    }

    function actualizarModerador() {
      const username = document.getElementById('editarModeradorUsuario').value;
      const role = document.getElementById('editarModeradorRol').value;
      const active = document.getElementById('editarModeradorActivo').value === '1';

      fetch('index.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `action=actualizar_moderador&username=${encodeURIComponent(username)}&role=${encodeURIComponent(role)}&active=${active ? '1' : '0'}`
      })
      .then(res => res.json())
      .then(data => {
        if (data.status === 'success') {
          alert('Moderador actualizado correctamente');
          mostrarModeradores(); // Actualizar lista
          cerrarPopup('popupEditarModerador');
        } else {
          alert('Error: ' + (data.message || 'Error desconocido'));
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Error de conexión');
      });
    }

    function mostrarCambiarPassword() {
      const username = document.getElementById('editarModeradorUsuario').value;
      document.getElementById('cambiarPasswordUsuario').value = username;
      document.getElementById('nuevaPassword').value = '';
      document.getElementById('confirmarPassword').value = '';
      mostrarPopup('popupCambiarPassword');
    }

    function cambiarPasswordModerador() {
      const username = document.getElementById('cambiarPasswordUsuario').value;
      const password = document.getElementById('nuevaPassword').value;
      const confirmPassword = document.getElementById('confirmarPassword').value;

      if (!password) {
        alert('La contraseña no puede estar vacía');
        return;
      }

      if (password !== confirmPassword) {
        alert('Las contraseñas no coinciden');
        return;
      }

      fetch('index.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `action=actualizar_password&username=${encodeURIComponent(username)}&password=${encodeURIComponent(password)}`
      })
      .then(res => res.json())
      .then(data => {
        if (data.status === 'success') {
          alert('Contraseña actualizada correctamente');
          cerrarPopup('popupCambiarPassword');
        } else {
          alert('Error: ' + (data.message || 'Error desconocido'));
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Error de conexión');
      });
    }

    function eliminarModerador() {
      const username = document.getElementById('editarModeradorUsuario').value;
      document.getElementById('moderadorAEliminar').textContent = username;
      document.getElementById('eliminarModeradorUsuario').value = username;
      cerrarPopup('popupEditarModerador');
      mostrarPopup('popupEliminarModerador');
    }

    function confirmarEliminarModerador() {
      const username = document.getElementById('eliminarModeradorUsuario').value;

      fetch('index.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `action=eliminar_moderador&username=${encodeURIComponent(username)}`
      })
      .then(res => res.json())
      .then(data => {
        if (data.status === 'success') {
          alert('Moderador eliminado correctamente');
          cerrarPopup('popupEliminarModerador');
          mostrarModeradores(); // Actualizar lista
        } else {
          alert('Error: ' + (data.message || 'Error desconocido'));
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Error de conexión');
      });
    }

    // Inicialización cuando el DOM está cargado
    document.addEventListener('DOMContentLoaded', function() {
      // Asignar eventos a elementos principales
      const matriculaInput = document.getElementById('matriculaInput');
      if (matriculaInput) {
        matriculaInput.addEventListener('input', function(e) {
          clearTimeout(autocompleteTimeout);
          const query = this.value.toUpperCase().trim();
          autocompleteTimeout = setTimeout(() => buscarAutocompletado(query), 300);
        });

        matriculaInput.addEventListener('keypress', function(e) {
          if (e.key === 'Enter') buscarMatricula();
        });
      }

      // Asignar eventos a elementos de popups
      asignarEvento('plazaAparcar', 'keypress', function(e) {
        if (e.key === 'Enter') guardarNuevaPlaza();
      });

      asignarEvento('matriculaEliminar', 'keypress', function(e) {
        if (e.key === 'Enter') eliminarCoche();
      });

      asignarEvento('historialMatricula', 'keypress', function(e) {
        if (e.key === 'Enter') filtrarHistorial();
      });

      asignarEvento('loginUser', 'keypress', function(e) {
        if (e.key === 'Enter') login();
      });

      asignarEvento('loginPass', 'keypress', function(e) {
        if (e.key === 'Enter') login();
      });

      asignarEvento('currentPassword', 'keypress', function(e) {
        if (e.key === 'Enter') restablecerPassword();
      });

      asignarEvento('newPassword', 'keypress', function(e) {
        if (e.key === 'Enter') restablecerPassword();
      });

      asignarEvento('confirmPassword', 'keypress', function(e) {
        if (e.key === 'Enter') restablecerPassword();
      });

      // Configuración inicial
      crearMapa();
      actualizarReloj();
      setInterval(actualizarReloj, 1000);

      if (window.innerWidth <= 480) {
        document.getElementById('matriculaInput').placeholder = "Matrícula";
      }

      // Fechas por defecto en historial
      const today = new Date();
      const oneWeekAgo = new Date();
      oneWeekAgo.setDate(today.getDate() - 7);
      document.getElementById('historialFechaInicio').valueAsDate = oneWeekAgo;
      document.getElementById('historialFechaFin').valueAsDate = today;

      // Actualizar mapa cada minuto
      setInterval(crearMapa, 60000);
    });

    // Cerrar autocompletado al hacer clic fuera
    document.addEventListener('click', function(e) {
      if (e.target.id !== 'matriculaInput') {
        const suggestions = document.getElementById('autocompleteSuggestions');
        if (suggestions) {
          suggestions.style.display = 'none';
        }
      }
    });
  </script>
</body>
</html>
