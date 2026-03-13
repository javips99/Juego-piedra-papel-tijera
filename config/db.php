<?php
// Configuración de la base de datos
// ⚠️ Cambia estos valores por los de tu servidor
define('DB_HOST', 'localhost');
define('DB_USER', 'root');       // Tu usuario de MySQL
define('DB_PASS', '1234');           // Tu contraseña de MySQL
define('DB_NAME', 'ppt_game');   // Nombre de la base de datos

function conectarDB() {
    $conexion = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if ($conexion->connect_error) {
        die(json_encode([
            'error' => 'Error de conexión: ' . $conexion->connect_error
        ]));
    }

    $conexion->set_charset('utf8');
    return $conexion;
}
?>
