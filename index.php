<?php
session_start();
date_default_timezone_set('Europe/Madrid');

// Enable error display
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$jsonFile = 'coches.json';
$historyFile = 'historial.json';
$adminFile = 'admin.json';
$blockedFile = 'blocked.json';
$profilesFile = 'profiles.json';
$matriculasFile = 'matriculas.json';
$nonNumberedFile = 'non_numbered.json';
$observationsFile = 'observations.json';

// Create files if they don't exist
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
if (!file_exists($nonNumberedFile)) {
    file_put_contents($nonNumberedFile, json_encode([]));
}
if (!file_exists($observationsFile)) {
    file_put_contents($observationsFile, json_encode([]));
}

// Language handling
$availableLanguages = ['es' => 'Español', 'en' => 'English'];
$defaultLanguage = 'es';

// Translations
$translations = [
    'es' => [
        'title' => 'Plazas de aparcamiento - MooveCars Barcelona',
        'total_spaces' => 'Plazas totales',
        'occupied_spaces' => 'Plazas ocupadas',
        'free_spaces' => 'Plazas libres',
        'blocked_spaces' => 'Plazas bloqueadas',
        'recent_occupied' => 'Ocupado (menos de 4h)',
        'medium_occupied' => 'Ocupado (4-8h)',
        'old_occupied' => 'Ocupado (más de 8h)',
        'free' => 'Libre',
        'blocked' => 'Bloqueada (mantenimiento)',
        'cleaning' => 'Bloqueada (limpieza)',
        'non_numbered' => 'Plaza no numerada',
        'plate_placeholder' => 'Introduce la matrícula (ej: 1234ABC)',
        'search' => 'Buscar',
        'login' => 'Login',
        'history' => 'Historial',
        'all_spaces' => 'Todas las plazas',
        'occupied' => 'Plazas ocupadas',
        'free' => 'Plazas libres',
        'blocked' => 'Plazas bloqueadas',
        'vehicle_found' => 'Vehículo encontrado',
        'vehicle_parked_at' => 'El vehículo con matrícula <strong id="matriculaEncontrada"></strong> está estacionado en la plaza <strong id="plazaEncontrada"></strong>.',
        'remove_from_parking' => 'Sacar del parking',
        'vehicle_not_found' => 'Vehículo no encontrado',
        'vehicle_not_found_msg' => 'No se encontró el vehículo con matrícula <strong id="matriculaNoEncontrada"></strong> en el sistema.',
        'blocked_spaces_info' => 'Los vehículos estacionados en las plazas bloqueadas no están registrados en esta web, ya que son vehículos en reparación o mantenimiento, aunque podrían estar ya listos para circular.',
        'verify_plate' => 'Verifica que has escrito la matrícula correctamente o acércate a la zona de vehículos bloqueados con la aplicación GeoTab abierta.',
        'park_vehicle_prompt' => 'Si lo que deseas es aparcar tu vehículo, solo pulsa "Aparcar vehículo".',
        'incorrect_plate' => 'Matrícula incorrecta',
        'park_vehicle' => 'Aparcar vehículo',
        'park_vehicle_title' => 'Aparcar vehículo',
        'enter_space_number' => 'Introduce el número de plaza para la matrícula <strong id="matriculaAparcar"></strong>:',
        'space_number_placeholder' => 'Número de plaza (1-900)',
        'nearest_space_placeholder' => 'Plaza numerada más cercana',
        'non_numbered_checkbox' => 'Plaza sin número',
        'observations' => 'Observaciones',
        'observations_placeholder' => 'Detalles sobre la ubicación de la plaza',
        'confirm' => 'Confirmar',
        'remove_car_title' => 'Sacar coche del parking',
        'enter_plate_to_remove' => 'Introduce la matrícula del coche que vas a sacar:',
        'plate_placeholder_short' => 'Matrícula (ej: 1234ABC)',
        'movement_history' => 'Historial de movimientos',
        'filter_by_plate' => 'Filtrar por matrícula',
        'search_history' => 'BUSCAR',
        'admin_profile' => 'Perfil de Administrador',
        'name' => 'Nombre:',
        'surname' => 'Apellidos:',
        'email' => 'Correo electrónico:',
        'phone' => 'Teléfono móvil:',
        'cancel' => 'Cancelar',
        'save' => 'Guardar',
        'empty_spaces_title' => 'Vaciar todas las plazas',
        'empty_spaces_confirm' => '¿Estás seguro de que deseas vaciar todas las plazas del parking?',
        'empty_spaces_warning' => 'Esta acción registrará la salida de todos los vehículos en el historial pero mantendrá las matrículas en el sistema.',
        'moderator_management' => 'Gestión de Moderadores',
        'add_moderator' => '➕ Añadir Moderador',
        'new_moderator_title' => 'Añadir Nuevo Moderador',
        'username' => 'Usuario:',
        'password' => 'Contraseña:',
        'role' => 'Rol:',
        'moderator' => 'Moderador',
        'master' => 'Master',
        'edit_moderator_title' => 'Editar Moderador',
        'status' => 'Estado:',
        'active' => 'Activo',
        'inactive' => 'Inactivo',
        'change_password' => 'Cambiar Contraseña',
        'delete_moderator' => 'Eliminar Moderador',
        'save_changes' => 'Guardar Cambios',
        'change_password_title' => 'Cambiar Contraseña',
        'new_password' => 'Nueva Contraseña:',
        'confirm_password' => 'Confirmar Contraseña:',
        'delete_moderator_title' => 'Eliminar Moderador',
        'delete_moderator_confirm' => '¿Estás seguro de que deseas eliminar al moderador <strong id="moderadorAEliminar"></strong>?',
        'delete_moderator_warning' => 'Esta acción no se puede deshacer.',
        'admin_access' => 'Acceso Administrador',
        'user_placeholder' => 'Usuario',
        'password_placeholder' => 'Contraseña',
        'enter' => 'Entrar',
        'reset_password_title' => 'Restablecer contraseña',
        'reset_password_msg' => 'Es necesario que cambies tu contraseña antes de continuar.',
        'current_password' => 'Contraseña actual',
        'new_password_placeholder' => 'Nueva contraseña',
        'confirm_password_placeholder' => 'Confirmar nueva contraseña',
        'change_password_button' => 'Cambiar contraseña',
        'admin_mode' => 'Modo Admin: Bienvenido',
        'logout' => 'Cerrar sesión',
        'profile' => 'Perfil',
        'moderators' => 'Moderadores',
        'empty_spaces_button' => 'Vaciar Plazas',
        'no_moderators' => 'No hay moderadores registrados',
        'never' => 'Nunca',
        'actions' => 'Acciones',
        'edit' => 'Editar',
        'no_records' => 'No hay registros en el historial',
        'entry' => 'Entrada',
        'exit' => 'Salida',
        'space' => 'Plaza',
        'invalid_plate_format' => 'Formato de matrícula inválido. Debe ser 1234ABC o 1234-ABC (sin vocales ni Q/Ñ)',
        'invalid_space_number' => 'Por favor, introduce un número de plaza válido (1-900)',
        'space_blocked' => 'Esta plaza está bloqueada para mantenimiento',
        'space_cleaning' => 'Esta plaza está bloqueada para limpieza',
        'space_already_occupied' => 'La plaza {space} ya está ocupada. ¿Deseas sobrescribirla?',
        'confirm_remove_car' => '¿Estás seguro de que quieres sacar el coche con matrícula {plate} del parking?',
        'confirm_empty_spaces' => '¿Estás seguro de que deseas vaciar TODAS las plazas del parking?',
        'success' => 'Éxito',
        'error' => 'Error',
        'connection_error' => 'Error de conexión',
        'all_fields_required' => 'Todos los campos son obligatorios',
        'passwords_not_match' => 'Las contraseñas no coinciden',
        'no_profile_info' => 'No hay información de perfil guardada.',
        'no_moderator_info' => 'No hay información de perfil disponible',
        'registered' => 'Registrado',
        'last_login' => 'Último login',
        'not_specified' => 'No especificado',
        'language' => 'Idioma',
        'please_enter_plate' => 'Por favor, introduce una matrícula',
        'settings' => 'Ajustes',
        'show_parking_time' => 'Mostrar tiempo de estacionamiento',
        'simple_view' => 'Vista simple',
        'detailed_view' => 'Vista detallada',
        'settings_saved' => 'Ajustes guardados',
        'last_update' => 'Última actualización',
        'associated_space' => 'Asociada a plaza {space}',
        'manage_non_numbered' => 'Gestionar plaza no numerada',
        'non_numbered_info' => 'Esta plaza no tiene número. Está asociada a la plaza {space}',
        'save_association' => 'Guardar asociación',
        'edit_observations' => 'Editar observaciones',
        'save_observations' => 'Guardar observaciones',
        'press_space_info' => 'Presiona sobre cualquier plaza para ver su ubicación aproximada',
        'location_info' => 'Ubicación: {observations}'
    ],
    'en' => [
        'title' => 'Parking Spaces - MooveCars Barcelona',
        'total_spaces' => 'Total spaces',
        'occupied_spaces' => 'Occupied spaces',
        'free_spaces' => 'Free spaces',
        'blocked_spaces' => 'Blocked spaces',
        'recent_occupied' => 'Occupied (less than 4h)',
        'medium_occupied' => 'Occupied (4-8h)',
        'old_occupied' => 'Occupied (more than 8h)',
        'free' => 'Free',
        'blocked' => 'Blocked (maintenance)',
        'cleaning' => 'Blocked (cleaning)',
        'non_numbered' => 'Non-numbered space',
        'plate_placeholder' => 'Enter license plate (e.g. 1234ABC)',
        'search' => 'Search',
        'login' => 'Login',
        'history' => 'History',
        'all_spaces' => 'All spaces',
        'occupied' => 'Occupied spaces',
        'free' => 'Free spaces',
        'blocked' => 'Blocked spaces',
        'vehicle_found' => 'Vehicle found',
        'vehicle_parked_at' => 'The vehicle with license plate <strong id="matriculaEncontrada"></strong> is parked in space <strong id="plazaEncontrada"></strong>.',
        'remove_from_parking' => 'Remove from parking',
        'vehicle_not_found' => 'Vehicle not found',
        'vehicle_not_found_msg' => 'The vehicle with license plate <strong id="matriculaNoEncontrada"></strong> was not found in the system.',
        'blocked_spaces_info' => 'Vehicles parked in blocked spaces are not registered in this system as they are under repair or maintenance, though they might be ready to drive.',
        'verify_plate' => 'Please verify you entered the correct license plate or check the blocked vehicles area with the GeoTab app open.',
        'park_vehicle_prompt' => 'If you want to park your vehicle, just click "Park vehicle".',
        'incorrect_plate' => 'Incorrect plate',
        'park_vehicle' => 'Park vehicle',
        'park_vehicle_title' => 'Park Vehicle',
        'enter_space_number' => 'Enter the space number for license plate <strong id="matriculaAparcar"></strong>:',
        'space_number_placeholder' => 'Space number (1-900)',
        'nearest_space_placeholder' => 'Nearest numbered space',
        'non_numbered_checkbox' => 'Non-numbered space',
        'observations' => 'Observations',
        'observations_placeholder' => 'Details about the space location',
        'confirm' => 'Confirm',
        'remove_car_title' => 'Remove car from parking',
        'enter_plate_to_remove' => 'Enter the license plate of the car you want to remove:',
        'plate_placeholder_short' => 'License plate (e.g. 1234ABC)',
        'movement_history' => 'Movement History',
        'filter_by_plate' => 'Filter by license plate',
        'search_history' => 'SEARCH',
        'admin_profile' => 'Admin Profile',
        'name' => 'Name:',
        'surname' => 'Surname:',
        'email' => 'Email:',
        'phone' => 'Phone:',
        'cancel' => 'Cancel',
        'save' => 'Save',
        'empty_spaces_title' => 'Empty all spaces',
        'empty_spaces_confirm' => 'Are you sure you want to empty all parking spaces?',
        'empty_spaces_warning' => 'This action will register all vehicles as exited in the history but will keep the license plates in the system.',
        'moderator_management' => 'Moderator Management',
        'add_moderator' => '➕ Add Moderator',
        'new_moderator_title' => 'Add New Moderator',
        'username' => 'Username:',
        'password' => 'Password:',
        'role' => 'Role:',
        'moderator' => 'Moderator',
        'master' => 'Master',
        'edit_moderator_title' => 'Edit Moderator',
        'status' => 'Status:',
        'active' => 'Active',
        'inactive' => 'Inactive',
        'change_password' => 'Change Password',
        'delete_moderator' => 'Delete Moderator',
        'save_changes' => 'Save Changes',
        'change_password_title' => 'Change Password',
        'new_password' => 'New Password:',
        'confirm_password' => 'Confirm Password:',
        'delete_moderator_title' => 'Delete Moderator',
        'delete_moderator_confirm' => 'Are you sure you want to delete moderator <strong id="moderadorAEliminar"></strong>?',
        'delete_moderator_warning' => 'This action cannot be undone.',
        'admin_access' => 'Admin Access',
        'user_placeholder' => 'Username',
        'password_placeholder' => 'Password',
        'enter' => 'Enter',
        'reset_password_title' => 'Reset Password',
        'reset_password_msg' => 'You need to change your password before continuing.',
        'current_password' => 'Current password',
        'new_password_placeholder' => 'New password',
        'confirm_password_placeholder' => 'Confirm new password',
        'change_password_button' => 'Change password',
        'admin_mode' => 'Admin Mode: Welcome',
        'logout' => 'Logout',
        'profile' => 'Profile',
        'moderators' => 'Moderators',
        'empty_spaces_button' => 'Empty Spaces',
        'no_moderators' => 'No moderators registered',
        'never' => 'Never',
        'actions' => 'Actions',
        'edit' => 'Edit',
        'no_records' => 'No records in history',
        'entry' => 'Entry',
        'exit' => 'Exit',
        'space' => 'Space',
        'invalid_plate_format' => 'Invalid license plate format. Must be 1234ABC or 1234-ABC (no vowels or Q/Ñ)',
        'invalid_space_number' => 'Please enter a valid space number (1-900)',
        'space_blocked' => 'This space is blocked for maintenance',
        'space_cleaning' => 'This space is blocked for cleaning',
        'space_already_occupied' => 'Space {space} is already occupied. Do you want to overwrite it?',
        'confirm_remove_car' => 'Are you sure you want to remove the car with license plate {plate} from parking?',
        'confirm_empty_spaces' => 'Are you sure you want to empty ALL parking spaces?',
        'success' => 'Success',
        'error' => 'Error',
        'connection_error' => 'Connection error',
        'all_fields_required' => 'All fields are required',
        'passwords_not_match' => 'Passwords do not match',
        'no_profile_info' => 'No profile information saved.',
        'no_moderator_info' => 'No moderator information available',
        'registered' => 'Registered',
        'last_login' => 'Last login',
        'not_specified' => 'Not specified',
        'language' => 'Language',
        'please_enter_plate' => 'Please enter a license plate',
        'settings' => 'Settings',
        'show_parking_time' => 'Show parking time',
        'simple_view' => 'Simple view',
        'detailed_view' => 'Detailed view',
        'settings_saved' => 'Settings saved',
        'last_update' => 'Last update',
        'associated_space' => 'Associated with space {space}',
        'manage_non_numbered' => 'Manage non-numbered space',
        'non_numbered_info' => 'This space has no number. It is associated with space {space}',
        'save_association' => 'Save association',
        'edit_observations' => 'Edit observations',
        'save_observations' => 'Save observations',
        'press_space_info' => 'Press any space to see its approximate location',
        'location_info' => 'Location: {observations}'
    ]
];

function t($key) {
    global $translations, $currentLanguage;
    return $translations[$currentLanguage][$key] ?? $translations['es'][$key] ?? $key;
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    // Change language
    if (isset($_POST['action']) && $_POST['action'] === 'change_language') {
        if (isset($_POST['language']) && array_key_exists($_POST['language'], $availableLanguages)) {
            $_SESSION['language'] = $_POST['language'];
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid language']);
        }
        exit;
    }

    // Get non-numbered spaces
    if (isset($_POST['action']) && $_POST['action'] === 'get_non_numbered') {
        $nonNumbered = json_decode(file_get_contents($nonNumberedFile), true) ?: [];
        echo json_encode(['status' => 'success', 'data' => $nonNumbered]);
        exit;
    }

    // Save non-numbered space association
    if (isset($_POST['action']) && $_POST['action'] === 'save_non_numbered' && isset($_SESSION['admin'])) {
        $nonNumbered = json_decode(file_get_contents($nonNumberedFile), true) ?: [];
        $nonNumbered[$_POST['non_numbered_id']] = intval($_POST['numbered_id']);
        file_put_contents($nonNumberedFile, json_encode($nonNumbered, JSON_PRETTY_PRINT));
        echo json_encode(['status' => 'success']);
        exit;
    }

    // Get observations
    if (isset($_POST['action']) && $_POST['action'] === 'get_observations') {
        $observations = json_decode(file_get_contents($observationsFile), true) ?: [];
        echo json_encode(['status' => 'success', 'data' => $observations]);
        exit;
    }

    // Save observations
    if (isset($_POST['action']) && $_POST['action'] === 'save_observations' && isset($_SESSION['admin'])) {
        $observations = json_decode(file_get_contents($observationsFile), true) ?: [];
        $observations[$_POST['space']] = $_POST['observations'];
        file_put_contents($observationsFile, json_encode($observations, JSON_PRETTY_PRINT));
        echo json_encode(['status' => 'success']);
        exit;
    }

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
        $type = $_POST['type'] ?? 'blocked'; // 'blocked' or 'cleaning'

        if ($plaza > 0 && $plaza <= 900) {
            $blocked = json_decode(file_get_contents($blockedFile), true) ?: [];

            if (isset($blocked[$plaza])) {
                unset($blocked[$plaza]);
            } else {
                $blocked[$plaza] = $type;
            }

            file_put_contents($blockedFile, json_encode($blocked, JSON_PRETTY_PRINT));
            echo json_encode(['status' => 'success', 'blocked' => isset($blocked[$plaza]), 'type' => $blocked[$plaza] ?? null]);
            exit;
        }

        echo json_encode(['status' => 'error', 'message' => 'Plaza inválida']);
        exit;
    }

    // Guardar nuevas matrículas
    if (isset($_POST['action']) && $_POST['action'] === 'guardar') {
        $matricula = strtoupper(trim(str_replace('-', '', $_POST['matricula'] ?? '')));
        $plaza = $_POST['plaza'] ?? '';
        $isNonNumbered = isset($_POST['non_numbered']) && $_POST['non_numbered'] === 'true';
        $nearestSpace = isset($_POST['nearest_space']) ? intval($_POST['nearest_space']) : null;

        // Validación para plazas numeradas (1-900)
        $isNumberedPlaza = !$isNonNumbered && is_numeric($plaza) && $plaza > 0 && $plaza <= 900;

        if (!preg_match('/^[0-9BCDFGHJKLMNPRSTVWXYZ]{4}[0-9BCDFGHJKLMNPRSTVWXYZ]{3}$/', $matricula) || (!$isNumberedPlaza && !$isNonNumbered)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Datos inválidos']);
            exit;
        }

        $blocked = json_decode(file_get_contents($blockedFile), true) ?: [];
        $nonNumbered = json_decode(file_get_contents($nonNumberedFile), true) ?: [];

        // Si es plaza no numerada, usar la plaza numerada más cercana para verificar bloqueos
        $plazaNumerada = $isNumberedPlaza ? intval($plaza) : $nearestSpace;

        // Verificar si la plaza está bloqueada
        if ($plazaNumerada && isset($blocked[$plazaNumerada]) && !isset($_SESSION['admin'])) {
            echo json_encode(['status' => 'error', 'message' => $blocked[$plazaNumerada] === 'cleaning' ? 'Plaza en limpieza' : 'Plaza en mantenimiento']);
            exit;
        }

        $coches = json_decode(file_get_contents($jsonFile), true) ?: [];
        $historial = json_decode(file_get_contents($historyFile), true) ?: [];
        $matriculas = json_decode(file_get_contents($matriculasFile), true) ?: [];

        // Registrar entrada en historial
        $historial[] = [
            'matricula' => $matricula,
            'plaza' => $isNumberedPlaza ? intval($plaza) : 'NN-' . $nearestSpace,
            'accion' => 'entrada',
            'timestamp' => time(),
            'admin' => $_SESSION['admin'] ?? null
        ];

        // Actualizar plaza
        $coches[$matricula] = [
            'plaza' => $isNumberedPlaza ? intval($plaza) : 'NN-' . $nearestSpace,
            'plaza_numerada' => $plazaNumerada,
            'timestamp' => time()
        ];

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
$nonNumbered = json_decode(file_get_contents($nonNumberedFile), true) ?: [];
$observations = json_decode(file_get_contents($observationsFile), true) ?: [];
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

// Set current language
$currentLanguage = $_SESSION['language'] ?? $defaultLanguage;
?>
<!DOCTYPE html>
<html lang="<?php echo $currentLanguage; ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <title><?php echo t('title'); ?></title>
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
      --cleaning-color: #3498db;
      --clock-color: #2c3e50;
      --master-color: #9b59b6;
      --moderator-color: #3498db;
      --occupied-color: #f39c12;
      --non-numbered-color: #9b59b6;
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

    input[type="text"], select, input[type="date"], input[type="number"] {
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

    button.settings {
      background-color: #7f8c8d;
    }

    button.settings:hover {
      background-color: #6c7a7d;
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

    .legend-color.cleaning {
      background-color: var(--cleaning-color);
    }

    .legend-color.cleaning::before {
      content: "";
      position: absolute;
      left: 0;
      top: 50%;
      width: 100%;
      height: 2px;
      background-color: white;
      transform: rotate(45deg);
    }

    .legend-color.non-numbered {
      background-color: var(--non-numbered-color);
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

    .plaza.ocupada.simple {
      background-color: var(--occupied-color);
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

    .plaza.limpieza {
      background-color: var(--cleaning-color);
      color: white;
      font-weight: bold;
      position: relative;
      overflow: hidden;
    }

    .plaza.limpieza::before {
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

    .plaza.non-numbered {
      background-color: var(--non-numbered-color);
      color: white;
      font-weight: bold;
    }

    .plaza.non-numbered.ocupada {
      background-color: var(--non-numbered-color);
      position: relative;
    }

    .plaza.non-numbered.ocupada::after {
      content: "✓";
      position: absolute;
      bottom: -10px;
      font-size: 10px;
      color: var(--success-color);
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

    .popup-content input, .popup-content textarea {
      width: 100%;
      padding: 10px;
      border: 1px solid #ddd;
      border-radius: 5px;
      margin-bottom: 12px;
      font-size: 14px;
      min-height: 44px;
    }

    .popup-content textarea {
      min-height: 100px;
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

    .settings-button {
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
      transition: all 0.3s;
    }

    .settings-button:hover {
      transform: scale(1.1);
      background-color: var(--secondary-color);
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

    .settings-popup {
      position: fixed;
      bottom: 80px;
      right: 20px;
      background-color: white;
      padding: 15px;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.2);
      z-index: 1000;
      display: none;
      width: 90%;
      max-width: 300px;
    }

    .settings-popup.active {
      display: block;
    }

    .settings-item {
      margin-bottom: 10px;
    }

    .settings-item label {
      display: flex;
      align-items: center;
      gap: 8px;
      cursor: pointer;
    }

    .language-switcher {
      display: flex;
      gap: 10px;
      margin-top: 15px;
      justify-content: center;
    }

    .language-option {
      cursor: pointer;
      padding: 5px 10px;
      border-radius: 4px;
      transition: all 0.2s;
    }

    .language-option:hover {
      background-color: #f0f0f0;
    }

    .language-option.active {
      background-color: var(--secondary-color);
      color: white;
    }

    .non-numbered-checkbox {
      display: flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 12px;
    }

    .press-info {
      font-size: 12px;
      text-align: center;
      margin-bottom: 10px;
      color: #7f8c8d;
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

      input[type="text"], select, input[type="date"], input[type="number"] {
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
      <h2><?php echo t('reset_password_title'); ?></h2>
      <p><?php echo t('reset_password_msg'); ?></p>
      <input type="password" id="currentPassword" placeholder="<?php echo t('current_password'); ?>" autocomplete="current-password">
      <input type="password" id="newPassword" placeholder="<?php echo t('new_password_placeholder'); ?>" autocomplete="new-password">
      <input type="password" id="confirmPassword" placeholder="<?php echo t('confirm_password_placeholder'); ?>" autocomplete="new-password">
      <button onclick="restablecerPassword()"><?php echo t('change_password_button'); ?></button>
    </div>
  </div>
  <?php endif; ?>

  <?php if ($isAdmin && !$reset_password): ?>
  <div class="admin-bar">
    <span><?php echo t('admin_mode'); ?> <?php echo htmlspecialchars($adminName); ?> <span class="role-badge"><?php echo strtoupper($role); ?></span></span>
    <div>
      <button class="profile" onclick="mostrarPerfil()"><?php echo t('profile'); ?></button>
      <?php if ($role === 'master'): ?>
        <button onclick="mostrarModeradores()"><?php echo t('moderators'); ?></button>
        <button class="master" onclick="mostrarVaciarPlazas()"><?php echo t('empty_spaces_button'); ?></button>
      <?php endif; ?>
      <button onclick="logout()"><?php echo t('logout'); ?></button>
    </div>
  </div>
  <?php endif; ?>

  <button class="settings-button" onclick="toggleSettings()">⚙️</button>
  <div class="settings-popup" id="settingsPopup">
    <h3><?php echo t('settings'); ?></h3>
    <div class="settings-item">
      <label>
        <input type="checkbox" id="showParkingTime">
        <?php echo t('show_parking_time'); ?>
      </label>
    </div>
    <div class="settings-item">
      <label>
        <input type="radio" name="viewMode" value="simple" id="simpleView" checked>
        <?php echo t('simple_view'); ?>
      </label>
    </div>
    <div class="settings-item">
      <label>
        <input type="radio" name="viewMode" value="detailed" id="detailedView">
        <?php echo t('detailed_view'); ?>
      </label>
    </div>
    <div class="language-switcher">
      <div class="language-option <?php echo $currentLanguage === 'es' ? 'active' : ''; ?>" onclick="changeLanguage('es')">
        🇪🇸 Español
      </div>
      <div class="language-option <?php echo $currentLanguage === 'en' ? 'active' : ''; ?>" onclick="changeLanguage('en')">
        🇺🇸 English
      </div>
    </div>
    <button onclick="saveSettings()" style="margin-top: 15px;"><?php echo t('save'); ?></button>
  </div>

  <div class="container">
    <h1><?php echo t('title'); ?></h1>
    <div class="clock" id="reloj"></div>

    <div class="stats">
      <div class="stat-card">
        <h3><?php echo t('total_spaces'); ?></h3>
        <p>900</p>
      </div>
      <div class="stat-card">
        <h3><?php echo t('occupied_spaces'); ?></h3>
        <p id="plazas-ocupadas"><?php echo count($coches); ?></p>
      </div>
      <div class="stat-card">
        <h3><?php echo t('free_spaces'); ?></h3>
        <p id="plazas-libres"><?php echo 900 - count($coches) - count($blocked); ?></p>
      </div>
      <div class="stat-card">
        <h3><?php echo t('blocked_spaces'); ?></h3>
        <p id="plazas-bloqueadas"><?php echo count($blocked); ?></p>
      </div>
    </div>

    <div class="legend" id="legendContainer"></div>
    <div class="press-info"><?php echo t('press_space_info'); ?></div>

    <div class="controls">
      <div class="search-container">
        <input type="text" id="matriculaInput" placeholder="<?php echo t('plate_placeholder'); ?>" autocomplete="off" autofocus>
        <div id="autocompleteSuggestions" class="autocomplete-suggestions"></div>
        <button onclick="buscarMatricula()"><?php echo t('search'); ?></button>
        <?php if (!$isAdmin): ?>
        <button class="login" onclick="mostrarPopup('loginContainer')"><?php echo t('login'); ?></button>
        <?php else: ?>
        <button class="history" onclick="mostrarHistorial()"><?php echo t('history'); ?></button>
        <?php endif; ?>
      </div>

      <div class="filter-container">
        <select id="filtroEstado" onchange="filtrarPlazas()">
          <option value="todas"><?php echo t('all_spaces'); ?></option>
          <option value="ocupadas"><?php echo t('occupied'); ?></option>
          <option value="libres"><?php echo t('free'); ?></option>
          <?php if ($isAdmin): ?>
          <option value="bloqueadas"><?php echo t('blocked'); ?></option>
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
      <h2><?php echo t('vehicle_found'); ?></h2>
      <p><?php echo t('vehicle_parked_at'); ?></p>
      <div class="popup-actions">
        <button class="delete" onclick="confirmarSacarCoche()"><?php echo t('remove_from_parking'); ?></button>
      </div>
    </div>
  </div>

  <div id="popupCocheNoEncontrado" class="popup">
    <div class="popup-content">
      <button class="popup-close" onclick="cerrarPopup('popupCocheNoEncontrado')">×</button>
      <h2><?php echo t('vehicle_not_found'); ?></h2>
      <p><?php echo t('vehicle_not_found_msg'); ?></p>
      <p><?php echo t('blocked_spaces_info'); ?></p>
      <p><?php echo t('verify_plate'); ?></p>
      <p><?php echo t('park_vehicle_prompt'); ?></p>
      <div class="popup-actions popup-actions-column">
        <button onclick="volverAIntroducirMatricula()"><?php echo t('incorrect_plate'); ?></button>
        <button onclick="mostrarPopupAparcar()"><?php echo t('park_vehicle'); ?></button>
      </div>
    </div>
  </div>

  <div id="popupAparcar" class="popup">
    <div class="popup-content">
      <button class="popup-close" onclick="cerrarPopup('popupAparcar')">×</button>
      <h2><?php echo t('park_vehicle_title'); ?></h2>
      <p><?php echo t('enter_space_number'); ?></p>
      <div class="non-numbered-checkbox">
        <input type="checkbox" id="nonNumberedCheckbox">
        <label for="nonNumberedCheckbox"><?php echo t('non_numbered_checkbox'); ?></label>
      </div>
      <input type="number" id="plazaAparcar" placeholder="<?php echo t('space_number_placeholder'); ?>" min="1" max="900">
      <input type="number" id="nearestSpace" placeholder="<?php echo t('nearest_space_placeholder'); ?>" min="1" max="900" style="display: none;">
      <?php if ($isAdmin): ?>
        <textarea id="observations" placeholder="<?php echo t('observations_placeholder'); ?>"></textarea>
      <?php endif; ?>
      <div class="popup-actions">
        <button onclick="guardarNuevaPlaza()"><?php echo t('confirm'); ?></button>
      </div>
    </div>
  </div>

  <div id="popupEliminar" class="popup">
    <div class="popup-content">
      <button class="popup-close" onclick="cerrarPopup('popupEliminar')">×</button>
      <h2><?php echo t('remove_car_title'); ?></h2>
      <p><?php echo t('enter_plate_to_remove'); ?></p>
      <input type="text" id="matriculaEliminar" placeholder="<?php echo t('plate_placeholder_short'); ?>">
      <div class="popup-actions">
        <button class="delete" onclick="eliminarCoche()"><?php echo t('confirm'); ?></button>
      </div>
    </div>
  </div>

  <div id="popupHistorial" class="popup">
    <div class="popup-content">
      <button class="popup-close" onclick="cerrarPopup('popupHistorial')">×</button>
      <h2><?php echo t('movement_history'); ?></h2>
      <div class="filter-container" style="margin-bottom: 15px;">
        <input type="text" id="historialMatricula" placeholder="<?php echo t('filter_by_plate'); ?>">
        <input type="date" id="historialFechaInicio">
        <input type="date" id="historialFechaFin">
        <button onclick="filtrarHistorial()"><?php echo t('search_history'); ?></button>
      </div>
      <div id="historialContenido"></div>
    </div>
  </div>

  <div id="popupPerfil" class="popup">
    <div class="popup-content">
      <button class="popup-close" onclick="cerrarPopup('popupPerfil')">×</button>
      <h2><?php echo t('admin_profile'); ?></h2>
      <div id="perfilInfo" class="profile-info"></div>
      <form id="formPerfil">
        <label for="perfilNombre"><?php echo t('name'); ?></label>
        <input type="text" id="perfilNombre" placeholder="<?php echo t('name'); ?>">

        <label for="perfilApellidos"><?php echo t('surname'); ?></label>
        <input type="text" id="perfilApellidos" placeholder="<?php echo t('surname'); ?>">

        <label for="perfilEmail"><?php echo t('email'); ?></label>
        <input type="email" id="perfilEmail" placeholder="<?php echo t('email'); ?>">

        <label for="perfilTelefono"><?php echo t('phone'); ?></label>
        <input type="tel" id="perfilTelefono" placeholder="<?php echo t('phone'); ?>">

        <div class="popup-actions">
          <button type="button" class="cancel" onclick="cerrarPopup('popupPerfil')"><?php echo t('cancel'); ?></button>
          <button type="button" onclick="guardarPerfil()"><?php echo t('save'); ?></button>
        </div>
      </form>
    </div>
  </div>

  <div id="popupVaciarPlazas" class="popup">
    <div class="popup-content">
      <button class="popup-close" onclick="cerrarPopup('popupVaciarPlazas')">×</button>
      <h2><?php echo t('empty_spaces_title'); ?></h2>
      <p><?php echo t('empty_spaces_confirm'); ?></p>
      <p><?php echo t('empty_spaces_warning'); ?></p>
      <div class="popup-actions">
        <button class="cancel" onclick="cerrarPopup('popupVaciarPlazas')"><?php echo t('cancel'); ?></button>
        <button class="master" onclick="vaciarTodasLasPlazas()"><?php echo t('confirm'); ?></button>
      </div>
    </div>
  </div>

  <div id="popupModeradores" class="popup">
    <div class="popup-content">
      <button class="popup-close" onclick="cerrarPopup('popupModeradores')">×</button>
      <h2><?php echo t('moderator_management'); ?></h2>

      <div class="controls" style="margin-bottom: 15px;">
        <button onclick="mostrarAgregarModerador()"><?php echo t('add_moderator'); ?></button>
      </div>

      <div id="listaModeradores"></div>
    </div>
  </div>

  <div id="popupAgregarModerador" class="popup">
    <div class="popup-content">
      <button class="popup-close" onclick="cerrarPopup('popupAgregarModerador')">×</button>
      <h2><?php echo t('new_moderator_title'); ?></h2>

      <label for="nuevoModeradorUsuario"><?php echo t('username'); ?></label>
      <input type="text" id="nuevoModeradorUsuario" placeholder="<?php echo t('username'); ?>">

      <label for="nuevoModeradorPassword"><?php echo t('password'); ?></label>
      <input type="password" id="nuevoModeradorPassword" placeholder="<?php echo t('password'); ?>">

      <label for="nuevoModeradorRol"><?php echo t('role'); ?></label>
      <select id="nuevoModeradorRol">
        <option value="moderador"><?php echo t('moderator'); ?></option>
        <option value="master"><?php echo t('master'); ?></option>
      </select>

      <div class="popup-actions">
        <button class="cancel" onclick="cerrarPopup('popupAgregarModerador')"><?php echo t('cancel'); ?></button>
        <button onclick="agregarModerador()"><?php echo t('save'); ?></button>
      </div>
    </div>
  </div>

  <div id="popupEditarModerador" class="popup">
    <div class="popup-content">
      <button class="popup-close" onclick="cerrarPopup('popupEditarModerador')">×</button>
      <h2><?php echo t('edit_moderator_title'); ?></h2>
      <input type="hidden" id="editarModeradorUsuario">

      <div id="moderadorInfo" class="profile-info"></div>

      <label for="editarModeradorRol"><?php echo t('role'); ?></label>
      <select id="editarModeradorRol">
        <option value="moderador"><?php echo t('moderator'); ?></option>
        <option value="master"><?php echo t('master'); ?></option>
      </select>

      <label for="editarModeradorActivo"><?php echo t('status'); ?></label>
      <select id="editarModeradorActivo">
        <option value="1"><?php echo t('active'); ?></option>
        <option value="0"><?php echo t('inactive'); ?></option>
      </select>

      <div class="popup-actions">
        <button onclick="mostrarCambiarPassword()"><?php echo t('change_password'); ?></button>
        <button class="delete" onclick="eliminarModerador()"><?php echo t('delete_moderator'); ?></button>
        <button onclick="actualizarModerador()"><?php echo t('save_changes'); ?></button>
      </div>
    </div>
  </div>

  <div id="popupCambiarPassword" class="popup">
    <div class="popup-content">
      <button class="popup-close" onclick="cerrarPopup('popupCambiarPassword')">×</button>
      <h2><?php echo t('change_password_title'); ?></h2>
      <input type="hidden" id="cambiarPasswordUsuario">

      <label for="nuevaPassword"><?php echo t('new_password'); ?></label>
      <input type="password" id="nuevaPassword" placeholder="<?php echo t('new_password'); ?>">

      <label for="confirmarPassword"><?php echo t('confirm_password'); ?></label>
      <input type="password" id="confirmarPassword" placeholder="<?php echo t('confirm_password'); ?>">

      <div class="popup-actions">
        <button class="cancel" onclick="cerrarPopup('popupCambiarPassword')"><?php echo t('cancel'); ?></button>
        <button onclick="cambiarPasswordModerador()"><?php echo t('save'); ?></button>
      </div>
    </div>
  </div>

  <div id="popupEliminarModerador" class="popup">
    <div class="popup-content">
      <button class="popup-close" onclick="cerrarPopup('popupEliminarModerador')">×</button>
      <h2><?php echo t('delete_moderator_title'); ?></h2>
      <p><?php echo t('delete_moderator_confirm'); ?></p>
      <p><?php echo t('delete_moderator_warning'); ?></p>
      <input type="hidden" id="eliminarModeradorUsuario">
      <div class="popup-actions">
        <button class="cancel" onclick="cerrarPopup('popupEliminarModerador')"><?php echo t('cancel'); ?></button>
        <button class="delete" onclick="confirmarEliminarModerador()"><?php echo t('delete_moderator'); ?></button>
      </div>
    </div>
  </div>

  <div id="popupObservaciones" class="popup">
    <div class="popup-content">
      <button class="popup-close" onclick="cerrarPopup('popupObservaciones')">×</button>
      <h2><?php echo t('edit_observations'); ?></h2>
      <input type="hidden" id="observationsSpace">
      <textarea id="observationsText" placeholder="<?php echo t('observations_placeholder'); ?>"></textarea>
      <div class="popup-actions">
        <button class="cancel" onclick="cerrarPopup('popupObservaciones')"><?php echo t('cancel'); ?></button>
        <button onclick="guardarObservaciones()"><?php echo t('save_observations'); ?></button>
      </div>
    </div>
  </div>

  <div id="loginContainer" class="popup">
    <div class="popup-content">
      <button class="popup-close" onclick="cerrarPopup('loginContainer')">×</button>
      <h2><?php echo t('admin_access'); ?></h2>
      <input type="text" id="loginUser" placeholder="<?php echo t('user_placeholder'); ?>" autocomplete="username">
      <input type="password" id="loginPass" placeholder="<?php echo t('password_placeholder'); ?>" autocomplete="current-password">
      <div class="popup-actions">
        <button onclick="login()"><?php echo t('enter'); ?></button>
      </div>
    </div>
  </div>

  <script>
    const coches = <?php echo json_encode($coches); ?>;
    const blockedPlazas = <?php echo json_encode($blocked); ?>;
    const nonNumberedPlazas = <?php echo json_encode($nonNumbered); ?>;
    const observations = <?php echo json_encode($observations); ?>;
    const isAdmin = <?php echo $isAdmin ? 'true' : 'false'; ?>;
    const role = '<?php echo $role; ?>';
    const reset_password = <?php echo $reset_password ? 'true' : 'false'; ?>;
    let matriculaPendiente = '';
    let filtroActual = 'todas';
    let autocompleteTimeout = null;
    let settings = {
      showParkingTime: true,
      viewMode: 'simple'
    };

    // Cargar configuración desde localStorage
    function loadSettings() {
      const savedSettings = localStorage.getItem('parkingSettings');
      if (savedSettings) {
        settings = JSON.parse(savedSettings);
      }

      document.getElementById('showParkingTime').checked = settings.showParkingTime;
      if (settings.viewMode === 'simple') {
        document.getElementById('simpleView').checked = true;
      } else {
        document.getElementById('detailedView').checked = true;
      }

      updateLegend();
    }

    // Guardar configuración en localStorage
    function saveSettings() {
      settings.showParkingTime = document.getElementById('showParkingTime').checked;
      settings.viewMode = document.querySelector('input[name="viewMode"]:checked').value;

      localStorage.setItem('parkingSettings', JSON.stringify(settings));
      alert('<?php echo t('settings_saved'); ?>');
      toggleSettings();
      crearMapa();
      updateLegend();
    }

    // Mostrar/ocultar ajustes
    function toggleSettings() {
      document.getElementById('settingsPopup').classList.toggle('active');
    }

    // Actualizar leyenda según configuración
    function updateLegend() {
      const legendContainer = document.getElementById('legendContainer');
      legendContainer.innerHTML = '';

      if (settings.viewMode === 'detailed' && settings.showParkingTime) {
        legendContainer.innerHTML = `
          <div class="legend-item">
            <div class="legend-color" style="background-color: var(--recent-color);"></div>
            <span><?php echo t('recent_occupied'); ?></span>
          </div>
          <div class="legend-item">
            <div class="legend-color" style="background-color: var(--medium-color);"></div>
            <span><?php echo t('medium_occupied'); ?></span>
          </div>
          <div class="legend-item">
            <div class="legend-color" style="background-color: var(--old-color);"></div>
            <span><?php echo t('old_occupied'); ?></span>
          </div>
          <div class="legend-item">
            <div class="legend-color" style="background-color: var(--light-color);"></div>
            <span><?php echo t('free'); ?></span>
          </div>
          <div class="legend-item">
            <div class="legend-color blocked"></div>
            <span><?php echo t('blocked'); ?></span>
          </div>
          <div class="legend-item">
            <div class="legend-color cleaning"></div>
            <span><?php echo t('cleaning'); ?></span>
          </div>
          <div class="legend-item">
            <div class="legend-color non-numbered"></div>
            <span><?php echo t('non_numbered'); ?></span>
          </div>
        `;
      } else {
        legendContainer.innerHTML = `
          <div class="legend-item">
            <div class="legend-color" style="background-color: var(--occupied-color);"></div>
            <span><?php echo t('occupied'); ?></span>
          </div>
          <div class="legend-item">
            <div class="legend-color" style="background-color: var(--light-color);"></div>
            <span><?php echo t('free'); ?></span>
          </div>
          <div class="legend-item">
            <div class="legend-color blocked"></div>
            <span><?php echo t('blocked'); ?></span>
          </div>
          <div class="legend-item">
            <div class="legend-color cleaning"></div>
            <span><?php echo t('cleaning'); ?></span>
          </div>
          <div class="legend-item">
            <div class="legend-color non-numbered"></div>
            <span><?php echo t('non_numbered'); ?></span>
          </div>
        `;
      }
    }

    // Función para asignar eventos de forma segura
    const asignarEvento = (id, evento, callback) => {
      const elemento = document.getElementById(id);
      if (elemento) {
        elemento.addEventListener(evento, callback);
      }
    };

    function changeLanguage(lang) {
      fetch('index.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `action=change_language&language=${lang}`
      })
      .then(res => res.json())
      .then(data => {
        if (data.status === 'success') {
          location.reload();
        }
      });
    }

    function actualizarReloj() {
      const ahora = new Date();
      const opciones = {
        timeZone: 'Europe/Madrid',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: false
      };
      const horaEspaña = ahora.toLocaleTimeString('<?php echo $currentLanguage; ?>', opciones);
      const fechaEspaña = ahora.toLocaleDateString('<?php echo $currentLanguage; ?>', {
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
      return date.toLocaleTimeString('<?php echo $currentLanguage; ?>', opciones);
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
      return date.toLocaleString('<?php echo $currentLanguage; ?>', opciones);
    }

    function formatDuration(timestamp) {
      const ahora = Math.floor(Date.now() / 1000);
      const segundos = ahora - timestamp;

      if (segundos < 60) {
        return `${segundos} <?php echo t('seconds'); ?>`;
      }

      const minutos = Math.floor(segundos / 60);
      if (minutos < 60) {
        return `${minutos} <?php echo t('minutes'); ?>`;
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
          alert('<?php echo t('error'); ?>: ' + (data.message || '<?php echo t('incorrect_credentials'); ?>'));
        }
      })
      .catch(error => {
        console.error('<?php echo t('error'); ?>:', error);
        alert('<?php echo t('connection_error'); ?>');
      });
    }

    function restablecerPassword() {
      const currentPassword = document.getElementById('currentPassword').value;
      const newPassword = document.getElementById('newPassword').value;
      const confirmPassword = document.getElementById('confirmPassword').value;

      if (!currentPassword || !newPassword || !confirmPassword) {
        alert('<?php echo t('all_fields_required'); ?>');
        return;
      }

      if (newPassword !== confirmPassword) {
        alert('<?php echo t('passwords_not_match'); ?>');
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
          alert('<?php echo t('error'); ?>: ' + (data.message || '<?php echo t('error_changing_password'); ?>'));
        }
      })
      .catch(error => {
        console.error('<?php echo t('error'); ?>:', error);
        alert('<?php echo t('connection_error'); ?>');
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

    function toggleBlockPlaza(plaza, type) {
      if (!isAdmin) return;

      fetch('index.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `action=toggle_block&plaza=${plaza}&type=${type}`
      })
      .then(res => res.json())
      .then(data => {
        if (data.status === 'success') {
          if (data.blocked) {
            blockedPlazas[plaza] = type;
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

      // Crear plazas numeradas (1-900)
      for (let i = 1; i <= 900; i++) {
        const div = document.createElement('div');
        div.className = 'plaza';
        div.textContent = i.toString().padStart(3, '0');
        div.id = 'plaza-' + i;
        div.dataset.plaza = i;

        // Verificar si la plaza está bloqueada
        if (blockedPlazas[i]) {
          if (blockedPlazas[i] === 'cleaning') {
            div.classList.add('limpieza');
          } else {
            div.classList.add('bloqueada');
          }
          if (isAdmin) {
            div.onclick = () => {
              if (confirm('¿Qué tipo de bloqueo deseas aplicar?')) {
                toggleBlockPlaza(i, 'blocked');
              } else {
                toggleBlockPlaza(i, 'cleaning');
              }
            };
          }
        } else {
          // Verificar si la plaza está ocupada
          const matriculaEnPlaza = Object.entries(coches).find(([_, datos]) =>
            (typeof datos.plaza === 'number' && datos.plaza === i));

          if (matriculaEnPlaza) {
            if (settings.viewMode === 'detailed' && settings.showParkingTime) {
              const ahora = Math.floor(Date.now() / 1000);
              const horasOcupacion = (ahora - matriculaEnPlaza[1].timestamp) / 3600;

              if (horasOcupacion < 4) {
                div.classList.add('ocupada', 'reciente');
              } else if (horasOcupacion < 8) {
                div.classList.add('ocupada', 'medio');
              } else {
                div.classList.add('ocupada', 'antiguo');
              }
            } else {
              div.classList.add('ocupada', 'simple');
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
          }

          // Mostrar observaciones al hacer clic
          div.onclick = () => {
            mostrarObservaciones(i);
          };
        }

        // Aplicar filtro
        if (filtroActual === 'ocupadas') {
          if (!div.classList.contains('ocupada')) {
            div.style.display = 'none';
          } else {
            div.style.display = 'flex';
          }
        } else if (filtroActual === 'libres') {
          if (div.classList.contains('ocupada') || div.classList.contains('bloqueada') || div.classList.contains('limpieza')) {
            div.style.display = 'none';
          } else {
            div.style.display = 'flex';
          }
        } else if (filtroActual === 'bloqueadas') {
          if (!div.classList.contains('bloqueada') && !div.classList.contains('limpieza')) {
            div.style.display = 'none';
          } else {
            div.style.display = 'flex';
          }
        } else {
          div.style.display = 'flex';
        }

        contenedor.appendChild(div);
      }

      // Crear plazas no numeradas
      Object.entries(coches).forEach(([matricula, datos]) => {
        if (typeof datos.plaza !== 'number') {
          const plazaNumerada = datos.plaza_numerada;
          if (plazaNumerada) {
            const div = document.createElement('div');
            div.className = 'plaza non-numbered ocupada';
            div.textContent = 'NN';
            div.id = `plaza-nn-${matricula}`;
            div.dataset.plaza = plazaNumerada;
            
            // Posicionar al lado de la plaza numerada
            const plazaRef = document.getElementById(`plaza-${plazaNumerada}`);
            if (plazaRef) {
              plazaRef.insertAdjacentElement('afterend', div);
            }

            if (window.innerWidth > 480) {
              const info = document.createElement('div');
              info.className = 'plaza-info';
              info.innerHTML = `
                <div>${matricula}</div>
                <div>${formatDate(datos.timestamp)}</div>
                <div>${formatDuration(datos.timestamp)}</div>
              `;
              div.appendChild(info);
            }

            div.onclick = () => {
              mostrarObservaciones(plazaNumerada);
            };
          }
        }
      });

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
        alert('<?php echo t('please_enter_plate'); ?>');
        return;
      }

      if (!validarMatricula(input)) {
        alert('<?php echo t('invalid_plate_format'); ?>');
        return;
      }

      const matriculaLimpia = input.replace('-', '');

      document.querySelectorAll('.plaza').forEach(p => p.classList.remove('resaltada'));

      if (coches[matriculaLimpia]) {
        matriculaPendiente = matriculaLimpia;
        document.getElementById('matriculaEncontrada').textContent = matriculaLimpia;

        // Mostrar plaza numerada si es no numerada
        const plaza = coches[matriculaLimpia].plaza;
        const plazaMostrar = typeof plaza === 'number' ? plaza : (coches[matriculaLimpia].plaza_numerada || plaza);

        document.getElementById('plazaEncontrada').textContent = typeof plazaMostrar === 'number' ?
          plazaMostrar.toString().padStart(3, '0') : plazaMostrar;

        mostrarPopup('popupCocheEncontrado');

        const div = document.getElementById(`plaza-${plazaMostrar}`) || 
                    document.getElementById(`plaza-nn-${matriculaLimpia}`);
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
          document.getElementById('historialContenido').innerHTML = '<p><?php echo t('error_loading_history'); ?></p>';
        }
      })
      .catch(error => {
        console.error('<?php echo t('error'); ?>:', error);
        document.getElementById('historialContenido').innerHTML = '<p><?php echo t('error_loading_history'); ?></p>';
      });
    }

    function mostrarHistorialContenido(historial) {
      const contenido = document.getElementById('historialContenido');
      contenido.innerHTML = '';

      if (historial.length === 0) {
        contenido.innerHTML = '<p><?php echo t('no_records'); ?></p>';
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
            <span class="history-action ${item.accion}">${item.accion === 'entrada' ? '<?php echo t('entry'); ?>' : '<?php echo t('exit'); ?>'}</span>
            <span><?php echo t('space'); ?> ${typeof item.plaza === 'number' ? item.plaza.toString().padStart(3, '0') : item.plaza}</span>
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
      const plazaInput = document.getElementById('plazaAparcar').value.trim();
      const isNonNumbered = document.getElementById('nonNumberedCheckbox').checked;
      const nearestSpace = document.getElementById('nearestSpace').value;
      const observations = document.getElementById('observations')?.value || '';

      // Validar plaza (debe ser número 1-900)
      const isNumberedPlaza = !isNonNumbered && !isNaN(plazaInput) && plazaInput > 0 && plazaInput <= 900;

      if (!isNumberedPlaza && !isNonNumbered) {
        alert('<?php echo t('invalid_space_number'); ?>');
        return;
      }

      if (isNonNumbered && (!nearestSpace || nearestSpace < 1 || nearestSpace > 900)) {
        alert('<?php echo t('invalid_space_number'); ?>');
        return;
      }

      const plazaNumerada = isNumberedPlaza ? parseInt(plazaInput) : parseInt(nearestSpace);

      if (blockedPlazas[plazaNumerada] && !isAdmin) {
        alert(blockedPlazas[plazaNumerada] === 'cleaning' ? '<?php echo t('space_cleaning'); ?>' : '<?php echo t('space_blocked'); ?>');
        return;
      }

      // Solo verificar ocupación si es plaza numerada
      if (isNumberedPlaza) {
        const plazaOcupada = Object.values(coches).some(coche =>
          typeof coche.plaza === 'number' && coche.plaza === plazaNumerada);

        if (plazaOcupada) {
          const plazaStr = plazaNumerada.toString().padStart(3, '0');
          if (!confirm(`<?php echo t('space_already_occupied'); ?>`.replace('{space}', plazaStr))) {
            return;
          }
        }
      }

      fetch('index.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `action=guardar&matricula=${encodeURIComponent(matriculaPendiente)}&plaza=${isNumberedPlaza ? plazaInput : 'NN'}&non_numbered=${isNonNumbered}&nearest_space=${plazaNumerada}`
      })
      .then(res => res.json())
      .then(data => {
        if (data.status === 'success') {
          coches[matriculaPendiente] = {
            plaza: isNumberedPlaza ? parseInt(plazaInput) : 'NN-' + plazaNumerada,
            plaza_numerada: plazaNumerada,
            timestamp: Math.floor(Date.now() / 1000)
          };

          // Guardar observaciones si es admin
          if (isAdmin && observations) {
            fetch('index.php', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
              },
              body: `action=save_observations&space=${plazaNumerada}&observations=${encodeURIComponent(observations)}`
            });
          }

          cerrarPopup('popupAparcar');
          document.getElementById('plazaAparcar').value = '';
          document.getElementById('nearestSpace').value = '';
          document.getElementById('nonNumberedCheckbox').checked = false;
          crearMapa();

          const div = document.getElementById(isNumberedPlaza ? `plaza-${plazaNumerada}` : `plaza-nn-${matriculaPendiente}`);
          if (div) {
            div.classList.add('resaltada');
            div.scrollIntoView({ behavior: 'smooth', block: 'center' });
          }
        } else {
          alert('<?php echo t('error_saving'); ?>: ' + (data.message || '<?php echo t('unknown_error'); ?>'));
        }
      })
      .catch(error => {
        console.error('<?php echo t('error'); ?>:', error);
        alert('<?php echo t('connection_error'); ?>');
      });
    }

    function eliminarCoche(matricula = null) {
      const matriculaInput = matricula || document.getElementById('matriculaEliminar').value.toUpperCase().replace('-', '');

      if (!matriculaInput) {
        alert('<?php echo t('please_enter_plate'); ?>');
        return;
      }

      if (!validarMatricula(matriculaInput)) {
        alert('<?php echo t('invalid_plate_format'); ?>');
        return;
      }

      if (!confirm(`<?php echo t('confirm_remove_car'); ?>`.replace('{plate}', matriculaInput))) {
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
          alert('<?php echo t('error_deleting'); ?>: ' + (data.message || '<?php echo t('plate_not_found'); ?>'));
        }
      })
      .catch(error => {
        console.error('<?php echo t('error'); ?>:', error);
        alert('<?php echo t('connection_error'); ?>');
      });
    }

    function mostrarVaciarPlazas() {
      if (role !== 'master') return;
      mostrarPopup('popupVaciarPlazas');
    }

    function vaciarTodasLasPlazas() {
      if (role !== 'master') return;

      if (!confirm('<?php echo t('confirm_empty_spaces'); ?>')) {
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
          alert(`<?php echo t('success'); ?>: ${data.count} <?php echo t('spaces_emptied'); ?>`);
          for (const matricula in coches) {
            delete coches[matricula];
          }
          crearMapa();
          cerrarPopup('popupVaciarPlazas');
        } else {
          alert('<?php echo t('error_emptying_spaces'); ?>');
        }
      })
      .catch(error => {
        console.error('<?php echo t('error'); ?>:', error);
        alert('<?php echo t('connection_error'); ?>');
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
              <p><strong><?php echo t('name'); ?>:</strong> ${perfil.nombre || '<?php echo t('not_specified'); ?>'}</p>
              <p><strong><?php echo t('surname'); ?>:</strong> ${perfil.apellidos || '<?php echo t('not_specified'); ?>'}</p>
              <p><strong><?php echo t('email'); ?>:</strong> ${perfil.email || '<?php echo t('not_specified'); ?>'}</p>
              <p><strong><?php echo t('phone'); ?>:</strong> ${perfil.telefono || '<?php echo t('not_specified'); ?>'}</p>
              <p><strong><?php echo t('last_update'); ?>:</strong> ${formatDate(perfil.actualizado)}</p>
            `;
          } else {
            perfilInfo.innerHTML = '<p><?php echo t('no_profile_info'); ?></p>';
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
        console.error('<?php echo t('error'); ?>:', error);
        alert('<?php echo t('error_loading_profile'); ?>');
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
          alert('<?php echo t('profile_saved_success'); ?>');
          mostrarPerfil(); // Actualizar la vista
        } else {
          alert('<?php echo t('error_saving_profile'); ?>');
        }
      })
      .catch(error => {
        console.error('<?php echo t('error'); ?>:', error);
        alert('<?php echo t('connection_error'); ?>');
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
          alert('<?php echo t('error_loading_moderators'); ?>');
        }
      })
      .catch(error => {
        console.error('<?php echo t('error'); ?>:', error);
        alert('<?php echo t('connection_error'); ?>');
      });
    }

    function mostrarListaModeradores(moderadores) {
      const lista = document.getElementById('listaModeradores');
      lista.innerHTML = '';

      if (moderadores.length === 0) {
        lista.innerHTML = '<p><?php echo t('no_moderators'); ?></p>';
        return;
      }

      const table = document.createElement('table');
      table.style.width = '100%';
      table.style.borderCollapse = 'collapse';

      // Cabecera
      const thead = document.createElement('thead');
      thead.innerHTML = `
        <tr>
          <th style="text-align: left; padding: 8px; border-bottom: 1px solid #ddd;"><?php echo t('username'); ?></th>
          <th style="text-align: left; padding: 8px; border-bottom: 1px solid #ddd;"><?php echo t('role'); ?></th>
          <th style="text-align: left; padding: 8px; border-bottom: 1px solid #ddd;"><?php echo t('status'); ?></th>
          <th style="text-align: left; padding: 8px; border-bottom: 1px solid #ddd;"><?php echo t('last_login'); ?></th>
          <th style="text-align: left; padding: 8px; border-bottom: 1px solid #ddd;"><?php echo t('actions'); ?></th>
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
          '<span class="badge badge-active">✔ <?php echo t('active'); ?></span>' :
          '<span class="badge badge-inactive">✖ <?php echo t('inactive'); ?></span>';

        // Rol como badge
        const rolClass = mod.role === 'master' ? 'badge-master' : 'badge-moderator';
        const rol = `<span class="badge ${rolClass}">${mod.role.toUpperCase()}</span>`;

        // Último login formateado
        const lastLogin = mod.last_login ? formatDate(mod.last_login) : '<?php echo t('never'); ?>';

        tr.innerHTML = `
          <td style="padding: 8px;">${mod.username}</td>
          <td style="padding: 8px;">${rol}</td>
          <td style="padding: 8px;">${estado}</td>
          <td style="padding: 8px;">${lastLogin}</td>
          <td style="padding: 8px;">
            <button style="padding: 4px 8px; font-size: 12px;" onclick="editarModerador('${mod.username}')"><?php echo t('edit'); ?></button>
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
        alert('<?php echo t('username_password_required'); ?>');
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
          alert('<?php echo t('moderator_added_success'); ?>');
          cerrarPopup('popupAgregarModerador');
          mostrarModeradores(); // Actualizar lista
        } else {
          alert('<?php echo t('error'); ?>: ' + (data.message || '<?php echo t('unknown_error'); ?>'));
        }
      })
      .catch(error => {
        console.error('<?php echo t('error'); ?>:', error);
        alert('<?php echo t('connection_error'); ?>');
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
                <p><strong><?php echo t('name'); ?>:</strong> ${moderador.profile.nombre || '<?php echo t('not_specified'); ?>'}</p>
                <p><strong><?php echo t('surname'); ?>:</strong> ${moderador.profile.apellidos || '<?php echo t('not_specified'); ?>'}</p>
                <p><strong><?php echo t('email'); ?>:</strong> ${moderador.profile.email || '<?php echo t('not_specified'); ?>'}</p>
                <p><strong><?php echo t('phone'); ?>:</strong> ${moderador.profile.telefono || '<?php echo t('not_specified'); ?>'}</p>
                <p><strong><?php echo t('registered'); ?>:</strong> ${formatDate(moderador.created_at)}</p>
                <p><strong><?php echo t('last_login'); ?>:</strong> ${moderador.last_login ? formatDate(moderador.last_login) : '<?php echo t('never'); ?>'}</p>
              `;
            } else {
              infoDiv.innerHTML = '<p><?php echo t('no_moderator_info'); ?></p>';
            }

            mostrarPopup('popupEditarModerador');
          }
        }
      })
      .catch(error => {
        console.error('<?php echo t('error'); ?>:', error);
        alert('<?php echo t('error_loading_moderator'); ?>');
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
          alert('<?php echo t('moderator_updated_success'); ?>');
          mostrarModeradores(); // Actualizar lista
          cerrarPopup('popupEditarModerador');
        } else {
          alert('<?php echo t('error'); ?>: ' + (data.message || '<?php echo t('unknown_error'); ?>'));
        }
      })
      .catch(error => {
        console.error('<?php echo t('error'); ?>:', error);
        alert('<?php echo t('connection_error'); ?>');
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
        alert('<?php echo t('password_cannot_be_empty'); ?>');
        return;
      }

      if (password !== confirmPassword) {
        alert('<?php echo t('passwords_not_match'); ?>');
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
          alert('<?php echo t('password_updated_success'); ?>');
          cerrarPopup('popupCambiarPassword');
        } else {
          alert('<?php echo t('error'); ?>: ' + (data.message || '<?php echo t('unknown_error'); ?>'));
        }
      })
      .catch(error => {
        console.error('<?php echo t('error'); ?>:', error);
        alert('<?php echo t('connection_error'); ?>');
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
          alert('<?php echo t('moderator_deleted_success'); ?>');
          cerrarPopup('popupEliminarModerador');
          mostrarModeradores(); // Actualizar lista
        } else {
          alert('<?php echo t('error'); ?>: ' + (data.message || '<?php echo t('unknown_error'); ?>'));
        }
      })
      .catch(error => {
        console.error('<?php echo t('error'); ?>:', error);
        alert('<?php echo t('connection_error'); ?>');
      });
    }

    function mostrarObservaciones(space) {
      document.getElementById('observationsSpace').value = space;
      document.getElementById('observationsText').value = observations[space] || '';
      
      if (isAdmin) {
        mostrarPopup('popupObservaciones');
      } else if (observations[space]) {
        alert(`<?php echo t('location_info'); ?>`.replace('{observations}', observations[space]));
      }
    }

    function guardarObservaciones() {
      const space = document.getElementById('observationsSpace').value;
      const text = document.getElementById('observationsText').value;

      fetch('index.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `action=save_observations&space=${space}&observations=${encodeURIComponent(text)}`
      })
      .then(res => res.json())
      .then(data => {
        if (data.status === 'success') {
          observations[space] = text;
          cerrarPopup('popupObservaciones');
        } else {
          alert('<?php echo t('error'); ?>: ' + (data.message || '<?php echo t('unknown_error'); ?>'));
        }
      })
      .catch(error => {
        console.error('<?php echo t('error'); ?>:', error);
        alert('<?php echo t('connection_error'); ?>');
      });
    }

    // Inicialización cuando el DOM está cargado
    document.addEventListener('DOMContentLoaded', function() {
      // Cargar configuración
      loadSettings();

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

      // Toggle para plazas no numeradas
      document.getElementById('nonNumberedCheckbox').addEventListener('change', function() {
        const plazaInput = document.getElementById('plazaAparcar');
        const nearestSpaceInput = document.getElementById('nearestSpace');
        
        if (this.checked) {
          plazaInput.placeholder = '<?php echo t('non_numbered_checkbox'); ?>';
          nearestSpaceInput.style.display = 'block';
          plazaInput.style.display = 'none';
        } else {
          plazaInput.placeholder = '<?php echo t('space_number_placeholder'); ?>';
          nearestSpaceInput.style.display = 'none';
          plazaInput.style.display = 'block';
        }
      });

      // Configuración inicial
      crearMapa();
      actualizarReloj();
      setInterval(actualizarReloj, 1000);

      if (window.innerWidth <= 480) {
        document.getElementById('matriculaInput').placeholder = "<?php echo t('plate'); ?>";
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

      // Cerrar ajustes si se hace clic fuera
      if (!e.target.closest('.settings-button') && !e.target.closest('.settings-popup')) {
        document.getElementById('settingsPopup').classList.remove('active');
      }
    });
  </script>
</body>
</html>