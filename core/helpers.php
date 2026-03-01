<?php
// Arquivo de funções auxiliares

define('BASE_URL', '/formamuana/');

function is_admin() {
    return isset($_SESSION['user_role_id']) && $_SESSION['user_role_id'] == 1; // 1 = Admin
}

function is_professor() {
    return isset($_SESSION['user_role_id']) && $_SESSION['user_role_id'] == 2; // 2 = Professor
}

function is_aluno() {
    return isset($_SESSION['user_role_id']) && $_SESSION['user_role_id'] == 3; // 3 = Aluno
}

// As funções abaixo são do exemplo do usuário e podem não se aplicar diretamente
// mas as criamos para evitar erros fatais.
function is_dentista() {
    // No contexto atual, podemos mapear para professor ou admin
    return is_admin() || is_professor();
}

function is_recepcionista() {
    // Poderia ser um outro tipo de usuário, por enquanto retorna false
    return false;
}
