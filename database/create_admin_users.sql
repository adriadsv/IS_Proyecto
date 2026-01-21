-- ============================================================
-- Script para crear usuarios administradores
-- Ejecutar en phpMyAdmin de Hostinger
-- ============================================================

-- NOTA: Las contraseñas están hasheadas con bcrypt
-- Contraseña original para todos: 12345

-- Insertar o actualizar usuarios administradores
INSERT INTO users (name, email, password, email_verified_at, created_at, updated_at)
VALUES
    ('Carlo Indemini', 'carloindemini@gmail.com', '$2y$12$LQv3c1yduTi5jbrRL.4YEuztS6.vAL0JgdqJaBxb9LFaKCBXhXKJO', NOW(), NOW(), NOW()),
    ('Adrian Sanchez', 'adriansanchez@gmail.com', '$2y$12$LQv3c1yduTi5jbrRL.4YEuztS6.vAL0JgdqJaBxb9LFaKCBXhXKJO', NOW(), NOW(), NOW()),
    ('Michael Lopez', 'michaellopez@gmail.com', '$2y$12$LQv3c1yduTi5jbrRL.4YEuztS6.vAL0JgdqJaBxb9LFaKCBXhXKJO', NOW(), NOW(), NOW())
ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    password = VALUES(password),
    email_verified_at = VALUES(email_verified_at),
    updated_at = NOW();

-- Verificar que se crearon
SELECT id, name, email, created_at FROM users WHERE email IN ('carloindemini@gmail.com', 'adriansanchez@gmail.com', 'michaellopez@gmail.com');
