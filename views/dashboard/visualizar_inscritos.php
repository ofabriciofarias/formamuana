<?php 
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../core/helpers.php';
if (is_professor()) {
    require_once __DIR__ . '/../layouts/professor_header.php';
} else {
    require_once __DIR__ . '/../layouts/admin_header.php';
}
?>
<style>
    .custom-modal { display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4); }
    .custom-modal-content { background-color: #fefefe; margin: 5% auto; padding: 20px; border: 1px solid #888; width: 80%; max-width: 600px; border-radius: 8px; position: relative; }
    .custom-close { color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer; }
    .custom-close:hover { color: black; }
    .modal-body { max-height: 70vh; overflow-y: auto; }
</style>

<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>Visualizar Inscritos</h2>
        <a href="index.php?action=dashboard" class="btn" style="background-color: #95a5a6; color: white;">Voltar</a>
    </div>

    <div class="card" style="padding: 20px; background: #f8f9fa; border-radius: 8px; margin-bottom: 30px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
        <div style="margin-bottom: 15px;">
            <input type="text" id="searchInput" onkeyup="filterInscritos()" placeholder="Buscar por nome, login ou email..." class="form-control" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
        </div>
        <div class="table-responsive">
            <table class="table" style="width: 100%; border-collapse: collapse; box-shadow: 0 0 20px rgba(0,0,0,0.1);">
                <thead>
                    <tr style="background-color: var(--primary-color); color: white;">
                        <th style="padding: 12px; text-align: left;">Nome</th>
                        <th style="padding: 12px; text-align: left;">Email</th>
                        <th style="padding: 12px; text-align: left;">Contato</th>
                        <th style="padding: 12px; text-align: center;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($inscritos)): foreach($inscritos as $aluno): ?>
                    <tr class="aluno-row" data-login="<?= htmlspecialchars($aluno['usuario_login'] ?? '') ?>" style="border-bottom: 1px solid #ddd;">
                        <td style="padding: 12px;"><?= htmlspecialchars($aluno['nome']) ?></td>
                        <td style="padding: 12px;"><?= htmlspecialchars($aluno['email']) ?></td>
                        <td style="padding: 12px;"><?= htmlspecialchars($aluno['whatsapp'] ?: $aluno['telefone']) ?></td>
                        <td style="padding: 12px; text-align: center;">
                            <button class="btn btn-primary" onclick="viewAluno(<?= $aluno['id'] ?>)" style="margin-right: 5px;">Visualizar</button>
                            <button class="btn" style="background-color:#28a745; color:white;" onclick="editAluno(<?= $aluno['id'] ?>)">Editar</button>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr><td colspan="4" style="text-align: center; padding: 20px;">Nenhum inscrito encontrado.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal View -->
<div id="viewModal" class="custom-modal">
    <div class="custom-modal-content">
        <span class="custom-close" onclick="closeModal('viewModal')">&times;</span>
        <h3 style="margin-top: 0;">Dados do Aluno</h3>
        <div class="modal-body" id="viewModalBody" style="padding: 15px 0;">
            Carregando...
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div id="editModal" class="custom-modal">
    <div class="custom-modal-content">
        <span class="custom-close" onclick="closeModal('editModal')">&times;</span>
        <h3 style="margin-top: 0;">Editar Aluno</h3>
        <div class="modal-body" style="padding: 15px 0;">
            <form id="editForm" onsubmit="saveAluno(event)">
                <input type="hidden" name="id" id="edit_id">
                <input type="hidden" name="usuario_id" id="edit_usuario_id">
                <div style="margin-bottom: 15px;">
                    <label style="display:block; margin-bottom:5px; font-weight:bold;">Nome</label>
                    <input type="text" name="nome" id="edit_nome" class="form-control" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
                </div>
                <div style="margin-bottom: 15px;">
                    <label style="display:block; margin-bottom:5px; font-weight:bold;">Email</label>
                    <input type="email" name="email" id="edit_email" class="form-control" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
                </div>
                <div style="margin-bottom: 15px;">
                    <label style="display:block; margin-bottom:5px; font-weight:bold;">WhatsApp</label>
                    <input type="text" name="whatsapp" id="edit_whatsapp" class="form-control" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
                </div>
                <div style="margin-bottom: 15px;">
                    <label style="display:block; margin-bottom:5px; font-weight:bold;">Telefone</label>
                    <input type="text" name="telefone" id="edit_telefone" class="form-control" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
                </div>
                <div style="margin-bottom: 20px;">
                    <label style="display:block; margin-bottom:5px; font-weight:bold;">Nova Senha</label>
                    <input type="password" name="senha" id="edit_senha" class="form-control" placeholder="Deixe em branco para não alterar" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; font-size: 16px;">Salvar Alterações</button>
            </form>
        </div>
    </div>
</div>

<script>
function filterInscritos() {
    let input = document.getElementById("searchInput").value.toLowerCase();
    let rows = document.getElementsByClassName("aluno-row");
    let hasVisible = false;

    for (let i = 0; i < rows.length; i++) {
        let name = rows[i].cells[0].innerText.toLowerCase();
        let email = rows[i].cells[1].innerText.toLowerCase();
        let login = rows[i].getAttribute("data-login").toLowerCase();

        if (name.includes(input) || email.includes(input) || login.includes(input)) {
            rows[i].style.display = "";
            hasVisible = true;
        } else {
            rows[i].style.display = "none";
        }
    }

    let noResultsRow = document.getElementById("noResultsRow");
    if (!hasVisible) {
        if (!noResultsRow) {
            let tbody = document.querySelector(".table tbody");
            tbody.insertAdjacentHTML("beforeend", '<tr id="noResultsRow"><td colspan="4" style="text-align: center; padding: 20px;">Nenhum inscrito encontrado com esse termo.</td></tr>');
        } else {
            noResultsRow.style.display = "";
        }
    } else if (noResultsRow) {
        noResultsRow.style.display = "none";
    }
}

function closeModal(id) {
    document.getElementById(id).style.display = 'none';
}

// Fechar ao clicar fora
window.onclick = function(event) {
    let vm = document.getElementById('viewModal');
    let em = document.getElementById('editModal');
    if (event.target == vm) vm.style.display = 'none';
    if (event.target == em) em.style.display = 'none';
}

function viewAluno(id) {
    document.getElementById('viewModal').style.display = 'block';
    document.getElementById('viewModalBody').innerHTML = '<p>Carregando...</p>';
    fetch(`index.php?action=get_inscrito&id=${id}`)
        .then(r => r.json())
        .then(data => {
            if(data.error) {
                document.getElementById('viewModalBody').innerHTML = `<p style="color:red">${data.error}</p>`;
                return;
            }
            let html = '<table style="width:100%; border-collapse: collapse;">';
            for (const [key, value] of Object.entries(data)) {
                if(value && key !== 'usuario_id' && key !== 'id') {
                    // Format the key to be more readable
                    let formattedKey = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                    html += `
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 8px; font-weight: bold; width: 40%;">${formattedKey}:</td>
                            <td style="padding: 8px; word-break: break-word;">${value}</td>
                        </tr>
                    `;
                }
            }
            html += '</table>';
            document.getElementById('viewModalBody').innerHTML = html;
        })
        .catch(err => {
            document.getElementById('viewModalBody').innerHTML = `<p style="color:red">Erro de comunicação.</p>`;
        });
}

function editAluno(id) {
    document.getElementById('editModal').style.display = 'block';
    fetch(`index.php?action=get_inscrito&id=${id}`)
        .then(r => r.json())
        .then(data => {
            if(data.error) {
                alert(data.error);
                closeModal('editModal');
                return;
            }
            document.getElementById('edit_id').value = data.id || '';
            document.getElementById('edit_usuario_id').value = data.usuario_id || '';
            document.getElementById('edit_nome').value = data.nome || '';
            document.getElementById('edit_email').value = data.email || '';
            document.getElementById('edit_whatsapp').value = data.whatsapp || '';
            document.getElementById('edit_telefone').value = data.telefone || '';
            document.getElementById('edit_senha').value = '';
        })
        .catch(err => {
            alert('Erro de comunicação.');
            closeModal('editModal');
        });
}

function saveAluno(e) {
    e.preventDefault();
    const fd = new FormData(document.getElementById('editForm'));
    fetch('index.php?action=update_inscrito', {
        method: 'POST',
        body: fd
    })
    .then(r => r.json())
    .then(data => {
        if(data.success) {
            alert('Aluno atualizado com sucesso!');
            location.reload();
        } else {
            alert(data.error || 'Erro ao atualizar.');
        }
    })
    .catch(err => {
        alert('Erro ao atualizar: ' + err.message);
    });
}
</script>
