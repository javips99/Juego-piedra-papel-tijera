<?php
// Permite recibir peticiones desde JavaScript (AJAX)
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../config/db.php';

// Solo aceptamos peticiones POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

// Recogemos la elección del jugador enviada por JavaScript
$datos = json_decode(file_get_contents('php://input'), true);
$eleccion_jugador = $datos['eleccion'] ?? '';

// Validamos que la elección sea válida
$opciones_validas = ['piedra', 'papel', 'tijera'];
if (!in_array($eleccion_jugador, $opciones_validas)) {
    echo json_encode(['error' => 'Elección no válida']);
    exit;
}

// La máquina elige al azar
$eleccion_maquina = $opciones_validas[array_rand($opciones_validas)];

// Determinamos el resultado
$resultado = determinarGanador($eleccion_jugador, $eleccion_maquina);

// Guardamos la partida en la base de datos
$db = conectarDB();
$stmt = $db->prepare(
    "INSERT INTO partidas (jugador, maquina, resultado) VALUES (?, ?, ?)"
);
$stmt->bind_param('sss', $eleccion_jugador, $eleccion_maquina, $resultado);
$stmt->execute();
$stmt->close();

// Obtenemos el marcador actualizado
$marcador = obtenerMarcador($db);
$db->close();

// Devolvemos todo al navegador
echo json_encode([
    'jugador'  => $eleccion_jugador,
    'maquina'  => $eleccion_maquina,
    'resultado'=> $resultado,
    'marcador' => $marcador
]);


// -----------------------------------------------
// FUNCIONES AUXILIARES
// -----------------------------------------------

/**
 * Decide quién gana según las reglas del juego
 */
function determinarGanador($jugador, $maquina) {
    if ($jugador === $maquina) {
        return 'empate';
    }

    // Reglas: qué vence a qué
    $vence = [
        'piedra'  => 'tijera',  // piedra vence a tijera
        'tijera'  => 'papel',   // tijera vence a papel
        'papel'   => 'piedra',  // papel vence a piedra
    ];

    return ($vence[$jugador] === $maquina) ? 'victoria' : 'derrota';
}

/**
 * Obtiene el marcador total desde la base de datos
 */
function obtenerMarcador($db) {
    $sql = "SELECT
                SUM(resultado = 'victoria') AS victorias,
                SUM(resultado = 'derrota')  AS derrotas,
                SUM(resultado = 'empate')   AS empates
            FROM partidas";

    $resultado = $db->query($sql);
    $fila = $resultado->fetch_assoc();

    return [
        'victorias' => (int)($fila['victorias'] ?? 0),
        'derrotas'  => (int)($fila['derrotas']  ?? 0),
        'empates'   => (int)($fila['empates']   ?? 0),
    ];
}
?>
