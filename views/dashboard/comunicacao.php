<?php
// views/dashboard/comunicacao.php

require_once __DIR__ . '/../layouts/admin_header.php';
?>

<h2>Comunicação com Alunos</h2>
<p>Selecione uma oferta para visualizar os alunos inscritos e enviar comunicações.</p>

<!-- Formulário de Filtro -->
<form id="form-comunicacao" method="GET" action="index.php">
    <input type="hidden" name="action" value="comunicacao">
    <div class="form-group">
        <label for="oferta_id">Selecione a Oferta:</label>
        <select name="oferta_id" id="oferta_id" required>
            <option value="">-- Escolha uma oferta --</option>
            <?php if (isset($ofertas) && is_array($ofertas)): ?>
                <?php foreach ($ofertas as $oferta): ?>
                    <option value="<?= htmlspecialchars($oferta['id']) ?>" <?= (isset($ofertaId) && $ofertaId == $oferta['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($oferta['nome']) ?>
                    </option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Buscar Alunos</button>
</form>

<!-- Placeholder para notificações -->
<div id="notification-toast" class="toast">
    <span id="toast-message"></span>
</div>

<!-- Tabela de Alunos -->
<?php if (!empty($inscritos)): ?>
<div class="table-responsive" style="margin-top: 30px;">
    <button id="mass-email-btn" class="btn btn-success" style="margin-bottom: 20px;"><i class="fas fa-users"></i> Envio Geral</button>
    <table class="responsive-table">
        <thead>
            <tr>
                <th>Nome do Aluno</th>
                <th>Email</th>
                <th>Status</th>
                <th>Ação</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($inscritos as $inscrito): ?>
            <tr>
                <td data-label="Nome"><?= htmlspecialchars($inscrito['nome']) ?></td>
                <td data-label="Email"><?= htmlspecialchars($inscrito['email']) ?></td>
                <td data-label="Status"><?= htmlspecialchars(ucfirst($inscrito['status_selecao'])) ?></td>
                <td data-label="Ação">
                    <button class="btn btn-primary btn-individual-email" 
                            data-aluno-id="<?= $inscrito['alunos_id'] ?>" 
                            data-aluno-email="<?= htmlspecialchars($inscrito['email']) ?>">
                        <i class="fas fa-paper-plane"></i> Enviar Email
                    </button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php elseif (isset($ofertaId)): ?>
<div style="background: #eaf6ff; border-left: 5px solid #63a3d4; padding: 15px; margin-top: 20px;">Nenhum aluno inscrito para a oferta selecionada.</div>
<?php endif; ?>

<!-- Modal: Escolha do Grupo de Email -->
<div id="emailGroupModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Enviar Email em Massa</h2>
            <span class="close-modal" data-modal-id="emailGroupModal">&times;</span>
        </div>
        <div class="modal-body" style="text-align: center; padding: 30px;">
            <p>Para qual grupo você deseja enviar o email?</p>
            <button class="btn btn-primary email-group-choice" data-target="Geral">Geral</button>
            <button class="btn btn-success email-group-choice" data-target="Aprovado">Aprovados</button>
            <button class="btn email-group-choice" data-target="Lista de Espera" style="background: #ffc107; color: black;">Lista de Espera</button>
        </div>
    </div>
</div>

<!-- Modal: Editor de Email -->
<div id="emailEditorModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="email-editor-title">Escrever Email</h2>
            <span class="close-modal" data-modal-id="emailEditorModal">&times;</span>
        </div>
        <div class="modal-body">
            <form id="email-editor-form">
                <input type="hidden" id="email-target" name="target">
                <div class="form-group"><label for="email-subject">Assunto:</label><input type="text" id="email-subject" class="form-control" required></div>
                <div class="form-group"><label for="email-body">Mensagem:</label><textarea id="email-body" class="form-control" rows="10" required></textarea></div>
                <button type="submit" id="send-email-btn" class="btn btn-primary" style="width: 100%;"><i class="fas fa-paper-plane"></i> Enviar</button>
                <div id="email-loading" style="display: none; text-align: center; margin-top: 15px;"><i class="fas fa-spinner fa-spin fa-2x"></i></div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Modals
    const emailGroupModal = document.getElementById('emailGroupModal');
    const emailEditorModal = document.getElementById('emailEditorModal');
    
    // Buttons
    const massEmailBtn = document.getElementById('mass-email-btn');
    const closeButtons = document.querySelectorAll('.close-modal');
    
    // Forms and inputs
    const emailEditorForm = document.getElementById('email-editor-form');
    const emailTargetInput = document.getElementById('email-target');
    const emailEditorTitle = document.getElementById('email-editor-title');
    const emailSubjectInput = document.getElementById('email-subject');

    function showToast(message, isSuccess = true) {
        const toast = document.getElementById('notification-toast');
        const toastMessage = document.getElementById('toast-message');
        
        toastMessage.textContent = message;
        toast.className = 'toast show ' + (isSuccess ? 'success' : 'error');

        setTimeout(() => {
            toast.className = toast.className.replace('show', '');
        }, 3000);
    }

    // --- Modal Control ---
    closeButtons.forEach(btn => {
        btn.onclick = function() {
            document.getElementById(this.getAttribute('data-modal-id')).style.display = 'none';
        }
    });
    window.onclick = function(event) {
        if (event.target.classList.contains('modal')) {
            event.target.style.display = 'none';
        }
    }

    // --- Event Listeners ---

    // Open mass email group choice modal
    if (massEmailBtn) {
        massEmailBtn.onclick = () => {
            emailGroupModal.style.display = 'block';
        };
    }

    // Handle individual email button clicks
    document.querySelectorAll('.btn-individual-email').forEach(button => {
        button.addEventListener('click', function() {
            const alunoId = this.dataset.alunoId;
            const alunoEmail = this.dataset.alunoEmail;
            
            emailTargetInput.value = alunoId;
            emailEditorTitle.innerText = `Email para: ${alunoEmail}`;
            emailEditorForm.reset();
            emailSubjectInput.value = '';
            emailEditorModal.style.display = 'block';
        });
    });

    // Handle mass email group choice
    document.querySelectorAll('.email-group-choice').forEach(button => {
        button.addEventListener('click', function() {
            const targetGroup = this.dataset.target;
            
            emailTargetInput.value = targetGroup;
            emailEditorTitle.innerText = `Email para o grupo: ${targetGroup}`;
            emailEditorForm.reset();
            emailSubjectInput.value = '';
            
            emailGroupModal.style.display = 'none';
            emailEditorModal.style.display = 'block';
        });
    });

    // Handle email form submission
    emailEditorForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const sendBtn = document.getElementById('send-email-btn');
        const loadingDiv = document.getElementById('email-loading');
        
        sendBtn.disabled = true;
        loadingDiv.style.display = 'block';

        const payload = {
            oferta_id: document.getElementById('oferta_id').value,
            target: emailTargetInput.value,
            subject: emailSubjectInput.value,
            body: document.getElementById('email-body').value.replace(/\n/g, '<br>')
        };

        fetch('index.php?action=send_email_notification', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message || 'Email(s) enviado(s) com sucesso!');
                emailEditorModal.style.display = 'none';
                emailEditorForm.reset();
            } else {
                showToast(data.message || 'Ocorreu um erro.', false);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Erro de comunicação com o servidor.', false);
        })
        .finally(() => {
            sendBtn.disabled = false;
            loadingDiv.style.display = 'none';
        });
    });
});
</script>

<?php
require_once __DIR__ . '/../layouts/admin_footer.php';
?>