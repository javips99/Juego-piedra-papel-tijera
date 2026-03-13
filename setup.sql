-- ============================================
-- PIEDRA, PAPEL O TIJERA - Base de datos
-- Ejecuta este archivo en phpMyAdmin o MySQL
-- ============================================

-- 1. Crear la base de datos
CREATE DATABASE IF NOT EXISTS ppt_game
    CHARACTER SET utf8
    COLLATE utf8_general_ci;

USE ppt_game;

-- 2. Crear la tabla de partidas
CREATE TABLE IF NOT EXISTS partidas (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    jugador     VARCHAR(50)  NOT NULL,           -- Elección del jugador: piedra, papel, tijera
    maquina     VARCHAR(50)  NOT NULL,           -- Elección de la máquina
    resultado   ENUM('victoria','derrota','empate') NOT NULL,
    fecha       TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. Ver las partidas guardadas (para probar)
-- SELECT * FROM partidas ORDER BY fecha DESC;
