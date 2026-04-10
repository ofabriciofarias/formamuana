<?php
// Garante que a sessão está iniciada e as funções de ajuda carregadas
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../core/helpers.php';

// Se o usuário não for professor, redireciona para o login
if (!is_professor()) {
    header('Location: index.php?action=login');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Professor | Forma Muana</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="views/assets/css/style.css">
</head>
<body>

<header class="admin-header">
    <div class="container">
        <div class="logo">
            <a href="index.php?action=professor_dashboard">Painel do Professor</a>
        </div>
        <nav class="nav-links">
            <a href="index.php?action=professor_dashboard">Minhas Turmas</a>
            <li class="dropdown">
                <a href="#" class="dropbtn">Vínculos <i class="fas fa-caret-down"></i></a>
                <div class="dropdown-content">
                    <a href="index.php?action=professor_visualizar_inscritos">Visualizar Inscritos</a>
                    <a href="index.php?action=professor_vincular_aluno">Vincular Aluno</a>
                </div>
            </li>
            <li class="dropdown">
                <a href="#" class="dropbtn">Configurações <i class="fas fa-caret-down"></i></a>
                <div class="dropdown-content">
                    <a href="#" id="profileBtn">Meu Perfil</a>
                </div>
            </li>
            <a href="index.php?action=logout">Logout</a>
        </nav>
    </div>
</header>

<div id="editProfileModal" class="modal">
    <div class="modal-content">
        <span class="close-modal" onclick="closeModal('editProfileModal')">&times;</span>
        <div class="modal-header">
            <h2><i class="fas fa-user-edit"></i> Editar Perfil</h2>
        </div>
        <div class="modal-body">
            <form id="profileForm">
                <div class="form-group">
                    <label for="nome">Nome:</label>
                    <input type="text" id="nome" name="nome" required class="form-control">
                </div>
                
                <div class="form-group">
                    <label for="login">Login:</label>
                    <input type="text" id="login" name="login" required class="form-control">
                </div>
                
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required class="form-control">
                </div>
                
                <div class="form-group">
                    <label for="telefone">Telefone:</label>
                    <input type="text" id="telefone" name="telefone" class="form-control">
                </div>
                
                <div class="form-group">
                    <label for="senha">Nova Senha (deixe em branco para não alterar):</label>
                    <input type="password" id="senha" name="senha" class="form-control">
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">Salvar Alterações</button>
            </form>
        </div>
    </div>
</div>

<div id="notification" class="toast"></div>

<main class="admin-content">
    <div class="container" style="padding-top: 40px; padding-bottom: 40px;">
<script>
// --- Lógica do Modal ---
let openModals = [];

function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;

    modal.style.display = "block";
    
    if (openModals.length === 0) {
        document.body.style.overflow = "hidden";
    }
    
    openModals.push(modalId);
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;

    modal.style.display = "none";
    
    openModals = openModals.filter(id => id !== modalId);

    if (openModals.length === 0) {
        document.body.style.overflow = "auto";
    }
}

window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        closeModal(event.target.id);
    }
};

document.getElementById('profileBtn').addEventListener('click', function(e) {
    e.preventDefault();
    fetch('index.php?action=get_profile')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                openModal('editProfileModal');
                document.getElementById('nome').value = data.data.nome;
                document.getElementById('login').value = data.data.login;
                document.getElementById('email').value = data.data.email;
                document.getElementById('telefone').value = data.data.telefone;
            } else {
                const notification = document.getElementById('notification');
                notification.textContent = data.message;
                notification.className = 'toast show error';
                setTimeout(() => {
                    notification.className = notification.className.replace('show', '');
                }, 3000);
            }
        });
});

document.getElementById('profileForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    fetch('index.php?action=update_profile', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        const notification = document.getElementById('notification');
        notification.textContent = data.message || (data.success ? 'Perfil atualizado com sucesso!' : 'Falha ao atualizar.');
        notification.className = 'toast show ' + (data.success ? 'success' : 'error');
        
        setTimeout(() => {
            notification.className = notification.className.replace('show', '');
            if (data.success) {
                closeModal('editProfileModal');
            }
        }, 3000);
    })
    .catch(error => {
        console.error('Error:', error);
        const notification = document.getElementById('notification');
        notification.textContent = 'Erro de comunicação com o servidor.';
        notification.className = 'toast show error';
        setTimeout(() => {
            notification.className = notification.className.replace('show', '');
        }, 3000);
    });
});
</script>