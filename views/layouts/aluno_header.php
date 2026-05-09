<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../core/helpers.php';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forma Muana - Área do Aluno</title>
    <link rel="stylesheet" href="views/assets/css/style.css">
    <!-- Font Awesome para ícones -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
</head>
<body>

<header>
    <div class="container nav-flex">
        <div class="logo">
            <a href="index.php?action=aluno_dashboard"><i class="fas fa-graduation-cap"></i>Forma Muaná</a>
        </div>
        <nav>
            <div class="mobile-toggle" id="mobile-toggle">
                <i class="fas fa-bars"></i>
            </div>
            <ul class="nav-links" id="nav-links">
                <?php if (is_logged_in() && is_aluno()): ?>
                    <!-- Menu do Aluno -->
                    <li><a href="index.php?action=aluno_dashboard#inicio">Inicio</a></li>
                    <li><a href="index.php?action=aluno_dashboard#ofertas">Vagas</a></li>
                    <li><a href="index.php?action=aluno_dashboard#minhas_inscricoes">Minhas Inscrições</a></li>
                    <li><a href="index.php?action=aluno_dashboard#cursos">Cursos</a></li>
                    <li><a href="index.php?action=aluno_dashboard#equipe">Realização</a></li>
                    
                    <li class="dropdown">
                        <a href="#" class="dropbtn">Configurações</a>
                        <div class="dropdown-content">
                            <a href="#" id="profileBtn">Meu Perfil</a>
                        </div>
                    </li>

                    <li><a href="index.php?action=logout" class="btn btn-primary">Sair</a></li>
                <?php else: ?>
                     <!-- Fallback se não estiver logado ou não for aluno, manda para a home -->
                    <li><a href="index.php#sobre">Sobre</a></li>
                    <li><a href="index.php#equipe">Equipe</a></li>
                    <li><a href="index.php#contato">Contato</a></li>
                    <li><a href="index.php" class="btn btn-success">Login</a></li>
                <?php endif; ?>
            </ul>
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
</script>

<script>
// Script para menu mobile e talvez outras interações do header
document.addEventListener('DOMContentLoaded', function() {
    const mobileToggle = document.getElementById('mobile-toggle');
    const navLinks = document.getElementById('nav-links');

    if (mobileToggle && navLinks) {
        mobileToggle.addEventListener('click', function() {
            navLinks.classList.toggle('active');
            const icon = mobileToggle.querySelector('i');
            if (icon) {
                if(navLinks.classList.contains('active')) {
                    icon.classList.remove('fa-bars');
                    icon.classList.add('fa-times');
                } else {
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            }
        });

        const links = navLinks.querySelectorAll('a:not(.dropbtn)');
        links.forEach(link => {
            link.addEventListener('click', () => {
                navLinks.classList.remove('active');
                const icon = mobileToggle.querySelector('i');
                if(icon) {
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            });
        });
    }

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
                    // A página irá recarregar para refletir o nome do usuário atualizado na sessão, se necessário.
                    // window.location.reload(); 
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
});
</script>