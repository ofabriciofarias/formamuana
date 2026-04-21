<?php
// Header do Admin
require_once __DIR__ . '/../layouts/admin_header.php';
?>

<h2>Seleção de Alunos</h2>
<p>Selecione uma oferta para visualizar os alunos inscritos e realizar a seleção.</p>

<!-- Formulário de Filtro -->
<form id="form-selecao" method="GET" action="/admin/selecao">
    <!-- <input type="hidden" name="action" value="selecao_alunos"> -->
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

<!-- Tabela de Alunos (será preenchida se houver inscritos) -->
<?php if (!empty($inscritos)): ?>

    <?php if (!$inscricoesEncerradas): ?>
        <div class="alert-info" style="margin-top: 20px;">
            As inscrições para esta oferta ainda estão abertas. A seleção de alunos só estará disponível após o encerramento das inscrições em <strong><?= htmlspecialchars(date('d/m/Y', strtotime($inscritos[0]['data_fim_inscricao']))) ?></strong>.
        </div>
    <?php endif; ?>

    <?php
        // A tabela fica visível se as inscrições estiverem abertas, ou escondida (para o modal aparecer) se estiverem fechadas.
        $tableStyle = !$inscricoesEncerradas ? 'display: block;' : 'display: none;';
    ?>

<?php
    $aprovadosCount = 0;
    foreach ($inscritos as $inscrito) {
        if ($inscrito['status_selecao'] === 'Aprovado') {
            $aprovadosCount++;
        }
    }
    $numeroVagas = (int)($inscritos[0]['numero_vagas'] ?? 0);
?>

<div id="tabela-alunos-container" class="table-responsive" style="margin-top: 30px; <?= $tableStyle ?>">
    <h4>Alunos Inscritos (<?= count($inscritos) ?>)</h4>
    <p>Vagas disponíveis: <strong id="numero-vagas"><?= htmlspecialchars($numeroVagas) ?></strong> | Vagas preenchidas: <strong id="aprovados-count"><?= $aprovadosCount ?></strong></p>
    <table class="responsive-table">
        <thead>
            <tr>
                <th>Nome do Aluno</th>
                <th>Email</th>
                <th>Detalhes</th>
                <th>Status Atual</th>
                <th>Ação</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($inscritos as $inscrito): ?>
            <tr>
                <td data-label="Nome"><?= htmlspecialchars($inscrito['nome']) ?></td>
                <td data-label="Email"><?= htmlspecialchars($inscrito['email']) ?></td>
                <td data-label="Detalhes" style="text-align: center;">
                    <button class="btn-view-details" data-aluno='<?= htmlspecialchars(json_encode($inscrito), ENT_QUOTES, 'UTF-8') ?>'>
                        <i class="fas fa-eye"></i>
                    </button>
                </td>
                <td data-label="Status" class="status-<?= str_replace(' ', '-', strtolower($inscrito['status_selecao'])) ?>">
                    <?= htmlspecialchars(ucfirst($inscrito['status_selecao'])) ?>
                </td>
                <td data-label="Ação">
                    <?php if ($inscricoesEncerradas): ?>
                        <!-- Formulário para atualização manual -->
                        <form class="manual-update-form" style="display: inline-flex; gap: 5px; align-items: center;">
                            <input type="hidden" name="aluno_id" value="<?= $inscrito['alunos_id'] ?>">
                            <input type="hidden" name="oferta_id" value="<?= $inscrito['oferta_id'] ?>">
                            <select name="status_selecao">
                                <option value="pendente" <?= $inscrito['status_selecao'] == 'pendente' ? 'selected' : '' ?>>Pendente</option>
                                <option value="Aprovado" <?= $inscrito['status_selecao'] == 'Aprovado' ? 'selected' : '' ?>>Aprovado</option>
                                <option value="Lista de Espera" <?= $inscrito['status_selecao'] == 'Lista de Espera' ? 'selected' : '' ?>>Lista de Espera</option>
                                <option value="Reprovado" <?= $inscrito['status_selecao'] == 'Reprovado' ? 'selected' : '' ?>>Reprovado</option>
                            </select>
                            <button type="button" class="btn btn-manual-save">Salvar</button>
                        </form>
                    <?php else: ?>
                        <span class="status-disabled">Seleção Indisponível</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php elseif (isset($ofertaId)): ?>
<div style="background: #eaf6ff; border-left: 5px solid #63a3d4; padding: 15px; margin-top: 20px;">Nenhum aluno inscrito para a oferta selecionada.</div>
<?php endif; ?>


<!-- Modal: Detalhes do Aluno -->
<div id="alunoDetailsModal" class="modal">
    <div class="modal-content" style="max-width: 700px;">
        <div class="modal-header">
            <h2>Detalhes do Aluno</h2>
            <span class="close-modal" data-modal-id="alunoDetailsModal">&times;</span>
        </div>
        <div class="modal-body" id="alunoDetailsBody" style="max-height: 60vh; overflow-y: auto;">
            <!-- Conteúdo será injetado via JS -->
        </div>
    </div>
</div>


<!-- Modal: Escolha do Tipo de Seleção -->
<div id="selectionTypeModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Como deseja fazer a seleção?</h2>
            <span class="close-modal" data-modal-id="selectionTypeModal">&times;</span>
        </div>
        <div class="modal-body" style="text-align: center; padding: 30px;">
            <p>Escolha entre o método manual, onde você define o status de cada aluno, ou use a nossa Inteligência Artificial para uma seleção automática baseada em critérios de vulnerabilidade.</p>
            <button id="manual-selection-btn" class="btn" style="background: #6c757d; color: white;">Seleção Manual</button>
            <button id="ga-selection-btn" class="btn btn-info" style="background-color: #17a2b8; color: white;">AG (Critérios Objetivos)</button>
            <button id="hybrid-ga-selection-btn" class="btn btn-success">AG + Análise de Texto (Híbrido)</button>
        </div>
    </div>
</div>

<!-- Modal: Parâmetros do Algoritmo Genético -->
<div id="gaParamsModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Parâmetros do Algoritmo Genético</h2>
            <span class="close-modal" data-modal-id="gaParamsModal">&times;</span>
        </div>
        <div class="modal-body">
            <form id="ga-form">
                <div class="form-group">
                    <label for="population_size">Tamanho da População (cromossomos)</label>
                    <input type="number" id="population_size" name="population_size" value="50" required>
                    <small>Indica quantas combinações de alunos serão testadas em cada geração. (Padrão: 50)</small>
                </div>
                <div class="form-group">
                    <label for="generations">Número de Gerações</label>
                    <input type="number" id="generations" name="generations" value="100" required>
                    <small>Quantas vezes o algoritmo irá evoluir a população para encontrar a melhor solução. (Padrão: 100)</small>
                </div>
                <div class="form-group">
                    <label for="mutation_rate">Taxa de Mutação</label>
                    <input type="number" step="0.01" min="0" max="1" id="mutation_rate" name="mutation_rate" value="0.1" required>
                    <small>A probabilidade de uma pequena mudança aleatória ocorrer (0 a 1). (Padrão: 0.1)</small>
                </div>
                <button type="submit" id="run-ga-btn" class="btn btn-primary" style="width: 100%;">
                    <i class="fas fa-cogs"></i> Rodar Algoritmo
                </button>
                <div id="ga-loading" style="display: none; text-align: center; margin-top: 15px;">
                    <p><strong>Processando...</strong> Isso pode levar alguns instantes.</p>
                    <i class="fas fa-spinner fa-spin fa-2x" style="color: var(--primary-color);"></i>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Parâmetros do Algoritmo Híbrido -->
<div id="hybridGaParamsModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Parâmetros do Algoritmo Híbrido</h2>
            <span class="close-modal" data-modal-id="hybridGaParamsModal">&times;</span>
        </div>
        <div class="modal-body">
            <form id="hybrid-ga-form">
                <div class="form-group">
                    <label for="hybrid_population_size">Tamanho da População</label>
                    <input type="number" id="hybrid_population_size" name="population_size" value="50" required>
                    <small>Indica quantas combinações de alunos serão testadas em cada geração.</small>
                </div>
                <div class="form-group">
                    <label for="hybrid_generations">Número de Gerações</label>
                    <input type="number" id="hybrid_generations" name="generations" value="100" required>
                    <small>Quantas vezes o algoritmo irá evoluir a população para encontrar a melhor solução.</small>
                </div>
                <div class="form-group">
                    <label for="hybrid_mutation_rate">Taxa de Mutação</label>
                    <input type="number" step="0.01" min="0" max="1" id="hybrid_mutation_rate" name="mutation_rate" value="0.1" required>
                    <small>A probabilidade de uma pequena mudança aleatória ocorrer (0 a 1).</small>
                </div>
                <hr style="margin: 20px 0;">
                <div class="form-group">
                    <label for="nlp_weight">Peso da Análise de Texto</label>
                    <input type="number" step="0.1" min="0" max="1" id="nlp_weight" name="nlp_weight" value="0.3" required>
                    <small>Define a importância das respostas textuais na pontuação final (0 a 1). Padrão: 0.3</small>
                </div>
                <button type="submit" id="run-hybrid-ga-btn" class="btn btn-primary" style="width: 100%;">
                    <i class="fas fa-cogs"></i> Rodar Algoritmo Híbrido
                </button>
                <div id="hybrid-ga-loading" style="display: none; text-align: center; margin-top: 15px;">
                    <p><strong>Processando...</strong> Isso pode levar alguns instantes.</p>
                    <i class="fas fa-spinner fa-spin fa-2x" style="color: var(--primary-color);"></i>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectionTypeModal = document.getElementById('selectionTypeModal');
    const gaParamsModal = document.getElementById('gaParamsModal');
    const manualSelectionBtn = document.getElementById('manual-selection-btn');
    const gaForm = document.getElementById('ga-form');
    const runGaBtn = document.getElementById('run-ga-btn');
    const gaLoading = document.getElementById('ga-loading');
    const closeButtons = document.querySelectorAll('.close-modal');
    const tabelaContainer = document.getElementById('tabela-alunos-container');
    const alunoDetailsModal = document.getElementById('alunoDetailsModal');

    // Novos elementos para o algoritmo híbrido
    const hybridGaParamsModal = document.getElementById('hybridGaParamsModal');
    const gaSelectionBtn = document.getElementById('ga-selection-btn');
    const hybridGaSelectionBtn = document.getElementById('hybrid-ga-selection-btn');
    const hybridGaForm = document.getElementById('hybrid-ga-form');


    function showToast(message, isSuccess = true) {
        const toast = document.getElementById('notification-toast');
        const toastMessage = document.getElementById('toast-message');
        
        toastMessage.textContent = message;
        toast.className = 'toast show ' + (isSuccess ? 'success' : 'error');

        setTimeout(() => {
            toast.className = toast.className.replace('show', '');
        }, 3000); // A notificação desaparece após 3 segundos
    }

    function buildDetailsHtml(aluno) {
        const personalFields = {
            'nome': 'Nome Completo', 'email': 'Email', 'whatsapp': 'WhatsApp', 'data_nascimento': 'Nascimento',
            'sexo': 'Sexo', 'identidade_genero': 'Identidade de Gênero', 'possui_nome_social': 'Possui Nome Social', 'nome_social': 'Nome Social',
            'estado_civil': 'Estado Civil', 'cidade_origem': 'Cidade de Origem', 'cidade_atual': 'Cidade Atual',
            'bairro_comunidade': 'Bairro/Comunidade'
        };

        const socialFields = {
            'cor_raca': 'Cor/Raça', 'possui_deficiencia': 'Possui Deficiência', 'tipo_deficiencia': 'Tipo de Deficiência',
            'comunidade_tradicional': 'Comunidade Tradicional', 'zona_residencia': 'Zona de Residência',
            'possui_filhos': 'Possui Filhos', 'quantidade_filhos': 'Qtd. Filhos',
            'nivel_escolaridade': 'Escolaridade', 'tipo_escola_ensino_medio': 'Tipo de Escola (Ens. Médio)',
            'renda_familiar': 'Renda Familiar', 'numero_membros_familia': 'Membros na Família', 
            'beneficiario_programa_social': 'Beneficiário de Programa Social',
            'acesso_internet': 'Acesso à Internet', 'dispositivo_principal': 'Dispositivo Principal'
        };

        let html = '<h4>Dados Pessoais</h4>';
        html += '<dl class="details-list">';
        for (const key in personalFields) {
            if (aluno[key] !== null && aluno[key] !== '') {
                 if (key === 'nome_social' && aluno['possui_nome_social'] !== 'Sim') continue;
                html += `<dt>${personalFields[key]}</dt><dd>${aluno[key]}</dd>`;
            }
        }
        html += '</dl>';

        html += '<h4 style="margin-top: 20px;">Perfil Social e Econômico</h4>';
        html += '<dl class="details-list">';
        for (const key in socialFields) {
            if (aluno[key] !== null && aluno[key] !== '') {
                if (key === 'tipo_deficiencia' && aluno['possui_deficiencia'] !== 'Sim') continue;
                if (key === 'quantidade_filhos' && aluno['possui_filhos'] !== 'Sim') continue;
                html += `<dt>${socialFields[key]}</dt><dd>${aluno[key]}</dd>`;
            }
        }
        html += '</dl>';

        const applicationFields = {
            'turno_preferencia': 'Turno de Preferência',
            'exerce_atividade_remunerada': 'Exerce atividade remunerada?',
            'vinculo_atividade': 'Vínculo da atividade',
            'afinidade_atividade_curso': 'Atividade tem afinidade com o curso?',
            'participacao_economica_familiar': 'Participação na economia familiar',
            'motivo_escolha_curso': 'Motivo da escolha do curso',
            'o_que_mais_espera_do_curso': 'O que mais espera do curso',
            'expectativas_relacao_curso': 'Expectativas em relação ao curso',
            'expectativas_profissionais_pos_curso': 'Expectativas profissionais pós-curso',
            'canal_informacao_curso': 'Como soube do curso?'
        };

        html += '<h4 style="margin-top: 20px;">Respostas da Inscrição</h4>';
        html += '<dl class="details-list">';
        for (const key in applicationFields) {
            if (aluno[key] !== null && aluno[key] !== '') {
                if (key === 'vinculo_atividade' && aluno['exerce_atividade_remunerada'] !== 'Sim') continue;
                if (key === 'afinidade_atividade_curso' && aluno['exerce_atividade_remunerada'] !== 'Sim') continue;
                html += `<dt>${applicationFields[key]}</dt><dd>${aluno[key]}</dd>`;
            }
        }
        html += '</dl>';

        return html;
    }

    <?php if ($inscricoesEncerradas && !empty($inscritos)): ?>
    selectionTypeModal.style.display = 'block';
    <?php endif; ?>

    closeButtons.forEach(btn => {
        btn.onclick = function() {
            const modal = document.getElementById(this.getAttribute('data-modal-id'));
            if (modal) modal.style.display = 'none';
        }
    });
    window.onclick = function(event) {
        if (event.target.classList.contains('modal')) {
            event.target.style.display = 'none';
        }
    }

    manualSelectionBtn.onclick = function() {
        selectionTypeModal.style.display = 'none';
        if(tabelaContainer) tabelaContainer.style.display = 'block';
    }

    gaSelectionBtn.onclick = function() {
        selectionTypeModal.style.display = 'none';
        gaParamsModal.style.display = 'block';
    }

    hybridGaSelectionBtn.onclick = function() {
        selectionTypeModal.style.display = 'none';
        hybridGaParamsModal.style.display = 'block';
    };

    // Delegação de evento para os botões de salvar e detalhes
    if (tabelaContainer) {
        tabelaContainer.addEventListener('click', function(e) {
            // Botão de ver detalhes
            const viewButton = e.target.closest('.btn-view-details');
            if (viewButton) {
                const alunoData = JSON.parse(viewButton.dataset.aluno);
                document.getElementById('alunoDetailsBody').innerHTML = buildDetailsHtml(alunoData);
                alunoDetailsModal.style.display = 'block';
                return;
            }

            if (e.target && e.target.classList.contains('btn-manual-save')) {
                const form = e.target.closest('.manual-update-form');
                const alunoId = form.querySelector('input[name="aluno_id"]').value;
                const ofertaId = form.querySelector('input[name="oferta_id"]').value;
                const statusSelect = form.querySelector('select[name="status_selecao"]');
                const newStatus = statusSelect.value;

                e.target.disabled = true;
                e.target.textContent = 'Salvando...';

                fetch('/admin/selecao/status/ajax', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({
                        aluno_id: alunoId,
                        oferta_id: ofertaId,
                        status: newStatus
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast('Status salvo com sucesso!');
                        // Atualiza o status na tela
                        const statusCell = form.closest('tr').querySelector('td[data-label="Status"]');
                        statusCell.textContent = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
                        statusCell.className = 'status-' + newStatus.toLowerCase().replace(' ', '-');

                        // Atualiza a contagem de aprovados
                        let aprovadosCount = 0;
                        document.querySelectorAll('.responsive-table td[data-label="Status"]').forEach(cell => {
                            if (cell.textContent.trim() === 'Aprovado') {
                                aprovadosCount++;
                            }
                        });
                        document.getElementById('aprovados-count').textContent = aprovadosCount;
                    } else {
                        showToast('Erro ao salvar o status.', false);
                    }
                })
                .finally(() => {
                    e.target.disabled = false;
                    e.target.textContent = 'Salvar';
                });
            }
        });
    }

    // Função genérica para rodar o algoritmo
    function runGeneticAlgorithm(params, loadingElement, buttonElement, modalElement) {
        buttonElement.disabled = true;
        loadingElement.style.display = 'block';

        fetch('/admin/selecao/ga', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(params)
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message || (data.success ? 'Seleção concluída!' : 'Ocorreu um erro.'));
            if (data.success) window.location.reload();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ocorreu um erro de comunicação com o servidor.');
        })
        .finally(() => {
            buttonElement.disabled = false;
            loadingElement.style.display = 'none';
            modalElement.style.display = 'none';
        });
    }

    // Evento para o GA Padrão
    gaForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const params = {
            mode: 'ga',
            oferta_id: document.getElementById('oferta_id').value,
            population_size: document.getElementById('population_size').value,
            generations: document.getElementById('generations').value,
            mutation_rate: document.getElementById('mutation_rate').value
        };
        runGeneticAlgorithm(params, document.getElementById('ga-loading'), document.getElementById('run-ga-btn'), gaParamsModal);
    });

    // Evento para o GA Híbrido
    hybridGaForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const params = {
            mode: 'hybrid',
            oferta_id: document.getElementById('oferta_id').value,
            population_size: document.getElementById('hybrid_population_size').value,
            generations: document.getElementById('hybrid_generations').value,
            mutation_rate: document.getElementById('hybrid_mutation_rate').value,
            nlp_weight: document.getElementById('nlp_weight').value
        };
        runGeneticAlgorithm(params, document.getElementById('hybrid-ga-loading'), document.getElementById('run-hybrid-ga-btn'), hybridGaParamsModal);
    });
});
</script>

<?php
// Footer do Admin
require_once __DIR__ . '/../layouts/admin_footer.php';
?>