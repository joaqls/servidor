-- Base de datos para la tarea DWES05: CRUD Equipos
-- Importar con: mysql -u root -p < equipos.sql

CREATE DATABASE IF NOT EXISTS equipos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE equipos;

DROP TABLE IF EXISTS equipos;

CREATE TABLE equipos (
    id      INT          NOT NULL AUTO_INCREMENT,
    nombre  VARCHAR(100) NOT NULL,
    puesto  INT          NOT NULL,
    escudo  MEDIUMBLOB   DEFAULT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Datos de ejemplo (sin escudo; las imágenes se añaden desde la interfaz)
INSERT INTO equipos (nombre, puesto) VALUES
('Real Madrid',          1),
('FC Barcelona',         2),
('Atlético de Madrid',   3),
('Sevilla FC',           4),
('Real Betis',           5),
('Athletic Club',        6),
('Real Sociedad',        7),
('Villarreal CF',        8),
('Valencia CF',          9),
('Celta de Vigo',       10);
