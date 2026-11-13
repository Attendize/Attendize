-- Base de datos SOLO para las pruebas automáticas de Moderniza.
-- Se ejecuta una vez (ver README). Nunca uses "attendize" para las pruebas.
CREATE DATABASE IF NOT EXISTS moderniza_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
GRANT ALL PRIVILEGES ON moderniza_test.* TO 'attendize'@'%';
FLUSH PRIVILEGES;
