-- SOLO si usas MySQL/MariaDB (XAMPP, Laragon, etc.). Con SQLite NO hace falta.
-- Ejecutar:  mysql -u root -p < database/sql/crear_base_datos.sql
CREATE DATABASE IF NOT EXISTS flota_vehicular
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
-- Las tablas las crea Laravel con:  php artisan migrate:fresh --seed
