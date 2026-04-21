<?php
// Arquivo de funções auxiliares

define('BASE_URL', '/');

function is_admin() {
    return isset($_SESSION['user_role_id']) && $_SESSION['user_role_id'] == 1; // 1 = Admin
}

function is_professor() {
    return isset($_SESSION['user_role_id']) && $_SESSION['user_role_id'] == 2; // 2 = Professor
}

function is_aluno() {
    return isset($_SESSION['user_role_id']) && $_SESSION['user_role_id'] == 3; // 3 = Aluno
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}
