<?php require_once __DIR__ . '/admin_header.php'; ?>

<style>
    #professores-container { display: none; margin-top: 2rem; }
    .professor-list { list-style: none; padding: 0; margin: 0; max-height: 300px; overflow-y: auto; border: 1px solid #ddd; border-radius: 5px; }
    .professor-list li { padding: 10px 15px; cursor: pointer; border-bottom: 1px solid #eee; transition: background-color 0.2s; }
    .professor-list li:hover { background-color: #f5f5f5; }
    .professor-list li:last-child { border-bottom: none; }
    .linked-professores-table { width: 100%; border-collapse: collapse; }
    .linked-professores-table th, .linked-professores-table td { padding: 12px; border: 1px solid #ddd; text-align: left; vertical-align: middle;}
    .linked-professores-table th { background-color: #f8f9fa; }
    .delete-btn { color: #dc3545; cursor: pointer; font-size: 1.2rem; background: none; border: none;}
    .loading-spinner { display: none; text-align: center; padding: 40px; }
    .list-placeholder { text-align: center; color: #888; padding: 20px 0; }
    /* Estilos do Modal - adaptado do modal.css para evitar conflitos e ser auto-contido */
    .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5); }
    .modal-content { background-color: #fefefe; margin: 15% auto; padding: 25px; border: 1px solid #888; width: 90%; max-width: 450px; border-radius: 8px; box-shadow: 0 5px 15px rgba(0,0,0,0.3); text-align: center; }
    .modal-buttons { margin-top: 25px; }
    .modal-buttons button { padding: 10px 20px; margin: 0 10px; border: none; border-radius: 5px; cursor: pointer; }
    /* Notificação */
    #notification { visibility: hidden; min-width: 250px; margin-left: -125px; background-color: #333; color: #fff; text-align: center; border-radius: 2px; padding: 16px; position: fixed; z-index: 1001; left: 50%; bottom: 30px; }
    #notification.show { visibility: visible; -webkit-animation: fadein 0.5s, fadeout 0.5s 2.5s; animation: fadein 0.5s, fadeout 0.5s 2.5s; }
    @-webkit-keyframes fadein { from {bottom: 0; opacity: 0;} to {bottom: 30px; opacity: 1;} }
    @keyframes fadein { from {bottom: 0; opacity: 0;} to {bottom: 30px; opacity: 1;} }
    @-webkit-keyframes fadeout { from {bottom: 30px; opacity: 1;} to {bottom: 0; opacity: 0;} }
    @keyframes fadeout { from {bottom: 30px; opacity: 1;} to {bottom: 0; opacity: 0;} }
</style>

<h2>Vincular Professor à Turma</h2>

<div class="form-container" style="max-width: 950px; margin: auto;">
    <!-- 1. Seleção de Turma -->
    <div class="form-group">
        <label for="oferta_id_select"><strong>1. Selecione uma Turma:</strong></label>
        <select id="oferta_id_select" class="form-control">
            <option value="">-- Escolha uma turma --</option>
            <?php if (isset($ofertas) && is_array($ofertas)): ?>
                <?php foreach ($ofertas as $oferta): ?>
                    <option value="<?= $oferta['id'] ?>"><?= htmlspecialchars($oferta['nome']) ?></option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
    </div>

    <!-- Spinner de Carregamento -->
    <div id="loading-spinner" class="loading-spinner">
        <i class="fas fa-spinner fa-spin fa-2x"></i>
    </div>

    <!-- Container para as listas de professores (controlado via JS) -->
    <div id="professores-container">
        <div class="row">
            <!-- 2. Lista de Professores Disponíveis -->
            <div class="col-md-6">
                <h5 class="mt-4"><strong>2. Clique no professor para vincular:</strong></h5>
                <ul id="professores-disponiveis-list" class="professor-list">
                    <li class="list-placeholder">Selecione uma turma para ver os professores disponíveis.</li>
                </ul>
            </div>
            
            <!-- 3. Tabela de Professores Vinculados -->
            <div class="col-md-6">
                <h5 class="mt-4"><strong>3. Professores já vinculados:</strong></h5>
                <div class="table-responsive auto-responsive-cards active">
                    <table class="table linked-professores-table">
                        <thead>
                            <tr>
                                <th>Professor</th>
                                <th style="width: 50px; text-align:center;">Ação</th>
                            </tr>
                        </thead>
                        <tbody id="professores-vinculados-tbody">
                            <tr>
                                <td colspan="2" class="list-placeholder">Selecione uma turma.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmação para Exclusão -->
<div id="deleteConfirmModal" class="modal">
    <div class="modal-content">
        <h4>Confirmar Exclusão</h4>
        <p>Tem certeza que deseja desvincular este professor?</p>
        <div class="modal-buttons">
            <button id="confirmDeleteBtn" class="btn btn-danger">Sim, desvincular</button>
            <button id="cancelDeleteBtn" class="btn btn-secondary">Cancelar</button>
        </div>
    </div>
</div>

<!-- Div para Notificações -->
<div id="notification"></div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const ofertaSelect = document.getElementById('oferta_id_select');
    const professoresContainer = document.getElementById('professores-container');
    const disponiveisList = document.getElementById('professores-disponiveis-list');
    const vinculadosTbody = document.getElementById('professores-vinculados-tbody');
    const loadingSpinner = document.getElementById('loading-spinner');
    
    const modal = document.getElementById('deleteConfirmModal');
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');

    let currentOfertaId = null;
    let vinculoParaDeletar = null;

    // --- FUNÇÕES DE RENDERIZAÇÃO ---

    const renderDisponiveis = (professores) => {
        disponiveisList.innerHTML = '';
        if (professores.length === 0) {
            disponiveisList.innerHTML = '<li class="list-placeholder">Nenhum professor disponível.</li>';
            return;
        }
        professores.forEach(prof => {
            const li = document.createElement('li');
            li.textContent = prof.nome;
            li.dataset.id = prof.id;
            disponiveisList.appendChild(li);
        });
    };

    const renderVinculados = (professores) => {
        vinculadosTbody.innerHTML = '';
        if (professores.length === 0) {
            vinculadosTbody.innerHTML = '<tr><td colspan="2" class="list-placeholder">Nenhum professor vinculado.</td></tr>';
            return;
        }
        professores.forEach(prof => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td data-label="Professor">${prof.nome}</td>
                <td data-label="Ação" style="text-align:center;">
                    <button class="delete-btn" data-vinculo-id="${prof.vinculo_id}" title="Desvincular Professor">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>
            `;
            vinculadosTbody.appendChild(tr);
        });
    };

    const showNotification = (message, isError = false) => {
        const notification = document.getElementById("notification");
        notification.textContent = message;
        notification.style.backgroundColor = isError ? '#dc3545' : '#28a745';
        notification.className = "show";
        setTimeout(() => { notification.className = notification.className.replace("show", ""); }, 3000);
    };

    // --- FUNÇÕES DE LÓGICA E AJAX ---

    const fetchDataForOferta = async (ofertaId) => {
        currentOfertaId = ofertaId;
        if (!currentOfertaId) {
            professoresContainer.style.display = 'none';
            return;
        }
        loadingSpinner.style.display = 'block';
        professoresContainer.style.display = 'none';

        try {
            const response = await fetch(`index.php?action=vincular_professor&oferta_id=${ofertaId}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!response.ok) throw new Error('Falha na resposta do servidor.');
            
            const data = await response.json();
            if (data.success) {
                renderDisponiveis(data.disponiveis);
                renderVinculados(data.vinculados);
                professoresContainer.style.display = 'block';
            } else {
                throw new Error(data.message || 'Não foi possível carregar os dados.');
            }
        } catch (error) {
            showNotification(error.message, true);
        } finally {
            loadingSpinner.style.display = 'none';
        }
    };

    const linkProfessor = async (professorId) => {
        try {
            const response = await fetch('index.php?action=ajaxLinkProfessor', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ professor_id: professorId, oferta_id: currentOfertaId })
            });
            if (!response.ok) throw new Error('Falha ao vincular professor.');
            
            const data = await response.json();
            if (data.success) {
                showNotification(data.message || 'Professor vinculado com sucesso!');
                fetchDataForOferta(currentOfertaId); // Recarrega os dados para manter a consistência
            } else {
                throw new Error(data.message || 'Erro desconhecido ao vincular.');
            }
        } catch (error) {
            showNotification(error.message, true);
        }
    };

    const unlinkProfessor = async (vinculoId) => {
        try {
            const response = await fetch('index.php?action=ajaxUnlinkProfessor', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ vinculo_id: vinculoId })
            });
            if (!response.ok) throw new Error('Falha ao desvincular professor.');
            
            const data = await response.json();
            if (data.success) {
                showNotification(data.message || 'Professor desvinculado com sucesso!');
                fetchDataForOferta(currentOfertaId); // Recarrega os dados
            } else {
                throw new Error(data.message || 'Erro desconhecido ao desvincular.');
            }
        } catch (error) {
            showNotification(error.message, true);
        }
    };

    // --- EVENT LISTENERS ---

    ofertaSelect.addEventListener('change', () => {
        fetchDataForOferta(ofertaSelect.value);
    });

    disponiveisList.addEventListener('click', (e) => {
        if (e.target && e.target.tagName === 'LI') {
            const professorId = e.target.dataset.id;
            linkProfessor(professorId);
        }
    });

    vinculadosTbody.addEventListener('click', (e) => {
        const deleteButton = e.target.closest('.delete-btn');
        if (deleteButton) {
            vinculoParaDeletar = deleteButton.dataset.vinculoId;
            modal.style.display = 'block';
        }
    });

    cancelDeleteBtn.addEventListener('click', () => {
        modal.style.display = 'none';
        vinculoParaDeletar = null;
    });

    confirmDeleteBtn.addEventListener('click', () => {
        if (vinculoParaDeletar) {
            unlinkProfessor(vinculoParaDeletar);
            modal.style.display = 'none';
            vinculoParaDeletar = null;
        }
    });
    
    // Fecha o modal se clicar fora dele
    window.addEventListener('click', (e) => {
        if (e.target == modal) {
            modal.style.display = 'none';
            vinculoParaDeletar = null;
        }
    });
});
</script>

<?php require_once __DIR__ . '/admin_footer.php'; ?>
