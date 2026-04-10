<?php
if (!defined('BASE_URL')) define('BASE_URL', '/formamuana/'); 
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forma Muana | Qualificação Profissional</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="stylesheet" href="views/assets/css/style.css">
</head>
<body>

    <header>
        <div class="container nav-flex">
            <div class="logo">
                <a href="#"><i class="fas fa-graduation-cap"></i> Forma Muaná</a>
            </div>
            
            <div class="mobile-toggle" id="mobile-toggle">
                <i class="fas fa-bars"></i>
            </div>

            <nav class="nav-links" id="nav-links">
                <a href="#inicio">Início</a>
                <a href="#ofertas">Vagas</a>
                <a href="#cursos">Cursos</a>
                <a href="#equipe">Realização</a>
               <!-- <a href="#localizacao">Localização</a> -->
                
                <div style="display: flex; align-items: center; gap: 10px;">
                    <form action="index.php?action=do_login" method="POST" class="header-login-form">
                        <input type="text" name="login" placeholder="Usuário" required oninvalid="this.setCustomValidity('Preencha este campo com seu login ou email.')" oninput="this.setCustomValidity('')">
                        <input type="password" name="senha" placeholder="Senha" required>
                        <button type="submit"><i class="fas fa-sign-in-alt"></i> Entrar</button>
                    </form>
                    <a href="#" onclick="openModal('registerModal'); return false;" class="btn btn-success" style="padding: 8px 15px; font-size: 0.8rem; text-transform: none; white-space: nowrap;">Aluno? Cadastre-se</a>
                </div>
            </nav>
        </div>
    </header>
