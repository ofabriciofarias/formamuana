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
    .custom-modal-content { background-color: #fefefe; margin: 5% auto; padding: 20px; border: 1px solid #888; width: 80%; max-width: 700px; border-radius: 8px; position: relative; }
    .custom-close { color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer; }
    .custom-close:hover { color: black; }
    .modal-body { max-height: 70vh; overflow-y: auto; padding-right: 15px; }
    .list-placeholder { text-align: center; color: #888; padding: 20px 0; }
    #notification { display: none; padding: 15px; margin-bottom: 20px; border-radius: 4px; color: white; text-align: center; font-weight: bold; }
    .success-msg { background-color: #28a745; }
    .error-msg { background-color: #dc3545; }
</style>

<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>Vincular Aluno à Turma</h2>
        <a href="index.php?action=dashboard" class="btn" style="background-color: #95a5a6; color: white;">Voltar</a>
    </div>

    <div id="notification"></div>

    <div class="card" style="padding: 20px; background: #f8f9fa; border-radius: 8px; margin-bottom: 30px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
        <!-- Seleção da Turma -->
        <div style="margin-bottom: 20px;">
            <label for="oferta_id_select" style="font-weight:bold; display:block; margin-bottom:5px;">1. Selecione uma Turma (Oferta):</label>
            <select id="oferta_id_select" class="form-control" style="width: 100%; padding: 10px; border-radius: 4px; border: 1px solid #ccc;">
                <option value="">-- Escolha uma turma --</option>
                <?php if (!empty($ofertas)): foreach ($ofertas as $oferta): ?>
                    <option value="<?= $oferta['id'] ?>" data-turno="<?= htmlspecialchars($oferta['turno']) ?>"><?= htmlspecialchars($oferta['nome']) ?> (<?= htmlspecialchars($oferta['status_oferta'] ?? 'Fechada/Aberta') ?>)</option>
                <?php endforeach; endif; ?>
            </select>
        </div>

        <!-- Busca de Alunos (Apenas mostra após selecionar a turma) -->
        <div id="busca-aluno-container" style="display:none;">
            <label style="font-weight:bold; display:block; margin-bottom:5px;">2. Buscar Aluno (Nome, Login ou Email):</label>
            <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                <input type="text" id="searchInput" placeholder="Digite para buscar..." class="form-control" style="flex: 1; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
                <button type="button" class="btn btn-primary" onclick="searchAlunos()">Buscar</button>
            </div>
            
            <div class="table-responsive">
                <table class="table" style="width: 100%; border-collapse: collapse; box-shadow: 0 0 20px rgba(0,0,0,0.1);">
                    <thead>
                        <tr style="background-color: var(--primary-color); color: white;">
                            <th style="padding: 12px; text-align: left;">Nome</th>
                            <th style="padding: 12px; text-align: left;">Email / Login</th>
                            <th style="padding: 12px; text-align: center;">Ações</th>
                        </tr>
                    </thead>
                    <tbody id="alunos-tbody">
                        <tr><td colspan="3" class="list-placeholder">Faça uma busca para encontrar alunos não vinculados a esta turma.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal View -->
<div id="viewModal" class="custom-modal">
    <div class="custom-modal-content" style="max-width: 500px;">
        <span class="custom-close" onclick="closeModal('viewModal')">&times;</span>
        <h3 style="margin-top: 0;">Dados do Aluno</h3>
        <div class="modal-body" id="viewModalBody" style="padding: 15px 0;">
            Carregando...
        </div>
    </div>
</div>

<!-- Modal de Vínculo e Formulário Socioeconômico -->
<div id="vinculoModal" class="custom-modal">
    <div class="custom-modal-content">
        <span class="custom-close" onclick="closeModal('vinculoModal')">&times;</span>
        <h3 style="margin-top: 0;">Preencher Dados Sociais e Vincular</h3>
        <p style="margin-bottom: 15px;">Aluno: <strong id="vinculo_nome_aluno"></strong></p>
        
        <div class="modal-body">
            <form id="vinculoForm" onsubmit="submitVinculo(event)">
                <input type="hidden" name="aluno_id" id="vinculo_aluno_id">
                <input type="hidden" name="oferta_id" id="vinculo_oferta_id">
                <input type="hidden" name="turno_preferencia" id="vinculo_turno">
                
                <fieldset style="border: 1px solid #ccc; padding: 15px; border-radius: 5px; margin-bottom: 15px;">
                    <legend style="padding: 0 10px; font-weight: bold;">Informações Socioeconômicas</legend>
                    
                    <div style="margin-bottom: 15px;">
                        <label style="display:block; margin-bottom:5px; font-weight:bold;">Exerce alguma atividade remunerada?</label>
                        <select name="exerce_atividade_remunerada" class="form-control" style="width: 100%; padding: 8px;" required>
                            <option value="" disabled selected>Selecione</option>
                            <option value="Sim">Sim</option>
                            <option value="Nao">Não</option>
                            <option value="As_vezes">Às vezes</option>
                        </select>
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label style="display:block; margin-bottom:5px; font-weight:bold;">Se sim, qual o seu vínculo ou tipo de atividade?</label>
                        <input type="text" name="vinculo_atividade" class="form-control" style="width: 100%; padding: 8px;" required>
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label style="display:block; margin-bottom:5px; font-weight:bold;">Sua atividade remunerada tem afinidade com a área do curso?</label>
                        <select name="afinidade_atividade_curso" class="form-control" style="width: 100%; padding: 8px;" required>
                            <option value="" disabled selected>Selecione</option>
                            <option value="Sim">Sim</option>
                            <option value="Nao">Não</option>
                        </select>
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label style="display:block; margin-bottom:5px; font-weight:bold;">Qual sua principal participação na vida econômica familiar?</label>
                        <select name="participacao_economica_familiar" class="form-control" style="width: 100%; padding: 8px;" required>
                            <option value="" disabled selected>Selecione</option>
                            <option value="Responsavel_principal">Responsável principal pelo sustento da família</option>
                            <option value="Ajuda_despesas">Ajudo a pagar as despesas</option>
                            <option value="Nao_contribuo">Não contribuo com as despesas</option>
                            <option value="Outro">Outro</option>
                        </select>
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label style="display:block; margin-bottom:5px; font-weight:bold;">Quantas pessoas, incluindo você, moram em sua residência?</label>
                        <select name="numero_membros_familia" class="form-control" style="width: 100%; padding: 8px;" required>
                            <option value="" disabled selected>Selecione</option>
                            <?php for ($i = 1; $i <= 20; $i++) : ?>
                                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label style="display:block; margin-bottom:5px; font-weight:bold;">Qual a renda total da sua família (incluindo você)?</label>
                        <select name="renda_familiar" class="form-control" style="width: 100%; padding: 8px;" required>
                            <option value="" disabled selected>Selecione</option>
                            <option value="Ate_1_sm">Até 1 salário mínimo</option>
                            <option value="Entre_1_e_2_sm">Entre 1 e 2 salários mínimos</option>
                            <option value="Entre_2_e_3_sm">Entre 2 e 3 salários mínimos</option>
                            <option value="Entre_3_e_5_sm">Entre 3 e 5 salários mínimos</option>
                            <option value="Acima_5_sm">Acima de 5 salários mínimos</option>
                            <option value="Nao_sei">Não sei informar</option>
                        </select>
                    </div>
                </fieldset>

                <fieldset style="border: 1px solid #ccc; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                    <legend style="padding: 0 10px; font-weight: bold;">Sobre o Curso</legend>
                    <div style="margin-bottom: 15px;">
                        <label style="display:block; margin-bottom:5px; font-weight:bold;">Por que você escolheu este curso? Quais habilidades espera desenvolver?</label>
                        <textarea name="motivo_escolha_curso" rows="2" class="form-control" style="width: 100%; padding: 8px;" required></textarea>
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label style="display:block; margin-bottom:5px; font-weight:bold;">O que você mais espera do curso?</label>
                        <textarea name="o_que_mais_espera_do_curso" rows="2" class="form-control" style="width: 100%; padding: 8px;" required></textarea>
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label style="display:block; margin-bottom:5px; font-weight:bold;">Quais suas expectativas em relação ao curso?</label>
                        <textarea name="expectativas_relacao_curso" rows="2" class="form-control" style="width: 100%; padding: 8px;" required></textarea>
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label style="display:block; margin-bottom:5px; font-weight:bold;">Quais suas expectativas profissionais após a conclusão do curso?</label>
                        <textarea name="expectativas_profissionais_pos_curso" rows="2" class="form-control" style="width: 100%; padding: 8px;" required></textarea>
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label style="display:block; margin-bottom:5px; font-weight:bold;">Como você ficou sabendo deste curso?</label>
                        <select name="canal_informacao_curso" class="form-control" style="width: 100%; padding: 8px;" required>
                            <option value="" disabled selected>Selecione</option>
                            <option value="Redes_sociais">Redes Sociais (Instagram, Facebook, etc.)</option>
                            <option value="Amigos_familiares">Indicação de amigos ou familiares</option>
                            <option value="Site_instituicao">Site da Instituição</option>
                            <option value="Busca_internet">Busca na Internet (Google, etc.)</option>
                            <option value="Radio_tv">Rádio ou TV</option>
                            <option value="Outro">Outro</option>
                        </select>
                    </div>
                </fieldset>
                
                <button type="submit" class="btn" style="background-color: #28a745; color: white; width: 100%; padding: 12px; font-size: 16px;">
                    <i class="fas fa-check"></i> Vincular Aluno à Turma
                </button>
            </form>
        </div>
    </div>
</div>

<script>
let currentOfertaId = '';

document.getElementById('oferta_id_select').addEventListener('change', function() {
    currentOfertaId = this.value;
    const container = document.getElementById('busca-aluno-container');
    const tbody = document.getElementById('alunos-tbody');
    
    if (currentOfertaId) {
        container.style.display = 'block';
        tbody.innerHTML = '<tr><td colspan="3" class="list-placeholder">Faça uma busca para encontrar alunos não vinculados a esta turma.</td></tr>';
        document.getElementById('searchInput').value = '';
    } else {
        container.style.display = 'none';
    }
});

function showNotification(msg, isError) {
    const notif = document.getElementById('notification');
    notif.textContent = msg;
    notif.className = isError ? 'error-msg' : 'success-msg';
    notif.style.display = 'block';
    setTimeout(() => notif.style.display = 'none', 5000);
}

function searchAlunos() {
    if (!currentOfertaId) return;
    
    const busca = document.getElementById('searchInput').value.trim();
    const tbody = document.getElementById('alunos-tbody');
    tbody.innerHTML = '<tr><td colspan="3" class="list-placeholder">Buscando...</td></tr>';
    
    fetch(`index.php?action=ajaxBuscarAlunosParaVinculo&oferta_id=${currentOfertaId}&busca=${encodeURIComponent(busca)}`)
        .then(r => r.json())
        .then(data => {
            if (!data.success) {
                tbody.innerHTML = `<tr><td colspan="3" class="list-placeholder" style="color:red;">${data.message}</td></tr>`;
                return;
            }
            if (data.alunos.length === 0) {
                tbody.innerHTML = '<tr><td colspan="3" class="list-placeholder">Nenhum aluno encontrado ou todos já estão vinculados.</td></tr>';
                return;
            }
            
            tbody.innerHTML = '';
            data.alunos.forEach(aluno => {
                const tr = document.createElement('tr');
                tr.style.borderBottom = "1px solid #ddd";
                const loginEmail = aluno.usuario_login ? `${aluno.usuario_email} <br><small>(${aluno.usuario_login})</small>` : aluno.usuario_email;
                
                tr.innerHTML = `
                    <td style="padding: 12px;">${aluno.nome}</td>
                    <td style="padding: 12px;">${loginEmail}</td>
                    <td style="padding: 12px; text-align: center;">
                        <button class="btn btn-primary" onclick="viewAluno(${aluno.id})" style="margin-right: 5px;" title="Ver Dados"><i class="fas fa-eye"></i></button>
                        <button class="btn" style="background-color: #28a745; color: white;" onclick="openVinculoModal(${aluno.id}, '${aluno.nome.replace(/'/g, "\\'")}')" title="Selecionar e Vincular"><i class="fas fa-plus"></i></button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        })
        .catch(err => {
            tbody.innerHTML = '<tr><td colspan="3" class="list-placeholder" style="color:red;">Erro na comunicação com o servidor.</td></tr>';
        });
}

// Permitir buscar apertando Enter
document.getElementById('searchInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        searchAlunos();
    }
});

function closeModal(id) {
    document.getElementById(id).style.display = 'none';
}

window.onclick = function(event) {
    let vm = document.getElementById('viewModal');
    let vn = document.getElementById('vinculoModal');
    if (event.target == vm) vm.style.display = 'none';
    if (event.target == vn) vn.style.display = 'none';
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

function openVinculoModal(alunoId, alunoNome) {
    document.getElementById('vinculoModal').style.display = 'block';
    document.getElementById('vinculo_aluno_id').value = alunoId;
    document.getElementById('vinculo_oferta_id').value = currentOfertaId;
    document.getElementById('vinculo_nome_aluno').textContent = alunoNome;
    
    const selectOferta = document.getElementById('oferta_id_select');
    const selectedOption = selectOferta.options[selectOferta.selectedIndex];
    document.getElementById('vinculo_turno').value = selectedOption.getAttribute('data-turno');
    
    document.getElementById('vinculoForm').reset();
    document.getElementById('vinculo_aluno_id').value = alunoId;
    document.getElementById('vinculo_oferta_id').value = currentOfertaId;
}

function submitVinculo(e) {
    e.preventDefault();
    const fd = new FormData(document.getElementById('vinculoForm'));
    
    fetch('index.php?action=ajaxVincularAluno', {
        method: 'POST',
        body: fd
    })
    .then(r => r.json())
    .then(data => {
        if(data.success) {
            closeModal('vinculoModal');
            showNotification(data.message, false);
            searchAlunos(); // Atualiza a lista removendo o aluno recém-vinculado
        } else {
            alert(data.message || 'Erro ao vincular.');
        }
    })
    .catch(err => {
        alert('Erro ao processar: ' + err.message);
    });
}
</script>
