<?php
// Cargamos la configuración de la base de datos
require_once 'config/db.php';

// Obtenemos el marcador actual para mostrarlo al cargar la página
$victorias = 0;
$derrotas  = 0;
$empates   = 0;

try {
    $db  = conectarDB();
    $sql = "SELECT
                SUM(resultado = 'victoria') AS victorias,
                SUM(resultado = 'derrota')  AS derrotas,
                SUM(resultado = 'empate')   AS empates
            FROM partidas";
    $res = $db->query($sql);
    if ($res) {
        $fila      = $res->fetch_assoc();
        $victorias = (int)($fila['victorias'] ?? 0);
        $derrotas  = (int)($fila['derrotas']  ?? 0);
        $empates   = (int)($fila['empates']   ?? 0);
    }

    // Últimas 5 partidas para el historial
    $historial = [];
    $res2 = $db->query(
        "SELECT jugador, maquina, resultado, fecha
         FROM partidas
         ORDER BY fecha DESC
         LIMIT 5"
    );
    if ($res2) {
        while ($fila2 = $res2->fetch_assoc()) {
            $historial[] = $fila2;
        }
    }
    $db->close();
} catch (Exception $e) {
    // Si hay error de conexión, el marcador queda en 0 y el juego sigue funcionando
}

// Emojis para mostrar las elecciones
$emojis = [
    'piedra' => '🪨',
    'papel'  => '📄',
    'tijera' => '✂️',
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Piedra, Papel o Tijera 🎮</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <!-- CABECERA -->
    <header>
        <h1>🪨 📄 ✂️</h1>
        <h1>Piedra, Papel o Tijera</h1>
        <p>¡Demuestra que puedes ganarle a la máquina!</p>
    </header>

    <!-- MARCADOR -->
    <div class="marcador">
        <div class="marcador-item victorias">
            <div class="marcador-numero" id="num-victorias"><?= $victorias ?></div>
            <div class="marcador-label">✅ Victorias</div>
        </div>
        <div class="marcador-item empates">
            <div class="marcador-numero" id="num-empates"><?= $empates ?></div>
            <div class="marcador-label">🟡 Empates</div>
        </div>
        <div class="marcador-item derrotas">
            <div class="marcador-numero" id="num-derrotas"><?= $derrotas ?></div>
            <div class="marcador-label">❌ Derrotas</div>
        </div>
    </div>

    <!-- ZONA DE JUEGO -->
    <div class="juego">
        <p class="instruccion">¿Cuál es tu elección?</p>

        <!-- Botones para elegir -->
        <div class="opciones">
            <button class="btn-opcion piedra" onclick="jugar('piedra')">
                <span class="emoji">🪨</span>
                Piedra
            </button>
            <button class="btn-opcion papel" onclick="jugar('papel')">
                <span class="emoji">📄</span>
                Papel
            </button>
            <button class="btn-opcion tijera" onclick="jugar('tijera')">
                <span class="emoji">✂️</span>
                Tijera
            </button>
        </div>

        <!-- Zona donde aparece el resultado (oculta hasta que se juega) -->
        <div class="resultado-zona oculto" id="resultado-zona">

            <!-- Jugador VS Máquina -->
            <div class="versus">
                <div class="eleccion-box">
                    <div class="emoji" id="emoji-jugador">🤔</div>
                    <div class="nombre">Tú</div>
                    <div class="eleccion-texto" id="texto-jugador">—</div>
                </div>
                <div class="vs-texto">VS</div>
                <div class="eleccion-box">
                    <div class="emoji" id="emoji-maquina">🤖</div>
                    <div class="nombre">Máquina</div>
                    <div class="eleccion-texto" id="texto-maquina">—</div>
                </div>
            </div>

            <!-- Chip de resultado: Victoria / Derrota / Empate -->
            <div class="chip-resultado" id="chip-resultado"></div>

            <!-- Botón para jugar otra vez -->
            <button class="btn-reset" id="btn-reset" onclick="resetear()">
                🔄 Jugar otra vez
            </button>
        </div>

    </div>

    <!-- HISTORIAL DE PARTIDAS -->
    <section class="historial-seccion">
        <h2 class="historial-titulo">Últimas partidas</h2>
        <ul class="historial-lista" id="historial-lista">
            <?php if (empty($historial)): ?>
                <li class="sin-historial">Todavía no has jugado ninguna partida 🎮</li>
            <?php else: ?>
                <?php foreach ($historial as $partida): ?>
                    <li class="historial-item <?= $partida['resultado'] ?>">
                        <span>
                            <?= $emojis[$partida['jugador']] ?> <?= ucfirst($partida['jugador']) ?>
                            &nbsp;vs&nbsp;
                            <?= $emojis[$partida['maquina']] ?> <?= ucfirst($partida['maquina']) ?>
                        </span>
                        <span class="badge"><?= ucfirst($partida['resultado']) ?></span>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </section>

    <!-- JAVASCRIPT -->
    <script>
        // Emojis para cada elección
        const emojis = {
            piedra: '🪨',
            papel:  '📄',
            tijera: '✂️'
        };

        // Mensajes según resultado
        const mensajes = {
            victoria: '🎉 ¡GANASTE!',
            derrota:  '💀 PERDISTE',
            empate:   '🤝 EMPATE'
        };

        // Botones del juego
        const botonesOpcion = document.querySelectorAll('.btn-opcion');

        /**
         * Función principal: envía la elección al servidor y muestra el resultado
         */
        async function jugar(eleccion) {
            // Desactivamos los botones mientras esperamos respuesta
            botonesOpcion.forEach(btn => btn.disabled = true);

            // Mostramos la zona de resultado con "cargando"
            const zonaResultado = document.getElementById('resultado-zona');
            zonaResultado.classList.remove('oculto');
            document.getElementById('chip-resultado').textContent = '⏳ Calculando...';
            document.getElementById('chip-resultado').className = 'chip-resultado';

            try {
                // Enviamos petición POST a la API con nuestra elección
                const respuesta = await fetch('api/game.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ eleccion: eleccion })
                });

                const datos = await respuesta.json();

                if (datos.error) {
                    alert('Error: ' + datos.error);
                    return;
                }

                // Mostramos las elecciones
                document.getElementById('emoji-jugador').textContent  = emojis[datos.jugador];
                document.getElementById('texto-jugador').textContent  = datos.jugador;
                document.getElementById('emoji-maquina').textContent  = emojis[datos.maquina];
                document.getElementById('texto-maquina').textContent  = datos.maquina;

                // Mostramos el resultado con estilo
                const chip = document.getElementById('chip-resultado');
                chip.textContent  = mensajes[datos.resultado];
                chip.className    = 'chip-resultado ' + datos.resultado;

                // Actualizamos el marcador en pantalla
                document.getElementById('num-victorias').textContent = datos.marcador.victorias;
                document.getElementById('num-derrotas').textContent  = datos.marcador.derrotas;
                document.getElementById('num-empates').textContent   = datos.marcador.empates;

                // Añadimos la partida al historial
                agregarAlHistorial(datos.jugador, datos.maquina, datos.resultado);

                // Mostramos el botón de "Jugar otra vez"
                document.getElementById('btn-reset').style.display = 'block';

            } catch (error) {
                alert('No se pudo conectar con el servidor. ¿Está ejecutándose PHP?');
                console.error(error);
            }
        }

        /**
         * Añade una partida al historial visible en pantalla
         */
        function agregarAlHistorial(jugador, maquina, resultado) {
            const lista = document.getElementById('historial-lista');

            // Quitamos el mensaje de "todavía no has jugado" si existe
            const sinHistorial = lista.querySelector('.sin-historial');
            if (sinHistorial) sinHistorial.remove();

            // Creamos el nuevo elemento
            const item = document.createElement('li');
            item.className = 'historial-item ' + resultado;
            item.innerHTML = `
                <span>
                    ${emojis[jugador]} ${capitalizar(jugador)}
                    &nbsp;vs&nbsp;
                    ${emojis[maquina]} ${capitalizar(maquina)}
                </span>
                <span class="badge">${capitalizar(resultado)}</span>
            `;

            // Lo insertamos al principio y limitamos a 5 entradas
            lista.insertBefore(item, lista.firstChild);
            const items = lista.querySelectorAll('.historial-item');
            if (items.length > 5) items[items.length - 1].remove();
        }

        /**
         * Resetea la zona de resultado para jugar otra vez
         */
        function resetear() {
            document.getElementById('resultado-zona').classList.add('oculto');
            document.getElementById('btn-reset').style.display = 'none';
            botonesOpcion.forEach(btn => btn.disabled = false);
        }

        /**
         * Capitaliza la primera letra de una cadena
         */
        function capitalizar(texto) {
            return texto.charAt(0).toUpperCase() + texto.slice(1);
        }
    </script>

</body>
</html>
