<?php
// Garante que a sessão está iniciada e as funções de ajuda carregadas
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../core/helpers.php';

// Se o usuário não for admin, redireciona para o login
if (!is_admin()) {
    header('Location: index.php?action=login');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Forma Muana</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="views/assets/css/style.css">
</head>
<body>

<header class="admin-header">
    <div class="container">
        <div class="logo">
            <a href="index.php?action=dashboard">Admin Forma Muana</a>
        </div>
        <nav class="nav-links">
            <div class="dropdown">
                <a href="#">Cadastro <i class="fas fa-caret-down"></i></a>
                <div class="dropdown-content">
                    <a href="index.php?action=register-oferta">Oferta</a>
                    <a href="index.php?action=register">Usuário</a>
                </div>
            </div>
        <nav class="nav-links">
            <div class="dropdown">
                <a href="#">Ações<i class="fas fa-caret-down"></i></a>
                <div class="dropdown-content">
                    <a href="index.php?action=selecao_alunos">Seleção de Alunos</a>
                </div>
            </div>
            <div class="dropdown">
                <a href="#">Relatório <i class="fas fa-caret-down"></i></a>
                <div class="dropdown-content">
                    <a href="index.php?action=relatorio_geral">Relatório Geral</a>
                </div>
            </div>
            <a href="index.php?action=logout">Logout</a>
        </nav>
    </div>
</header>

<main class="admin-content">
    <div class="container" style="padding-top: 40px; padding-bottom: 40px;">
