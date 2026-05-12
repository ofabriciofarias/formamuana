<?php
// Garante que as variáveis para a view existam e define valores padrão.
$data_aula_selecionada = $_GET['data_aula'] ?? date('Y-m-d');
$isReadOnly = $isReadOnly ?? false;
$ofertaNome = (isset($oferta) && $oferta) ? htmlspecialchars($oferta->getNome()) : 'Turma não encontrada';
$pageTitle = $isReadOnly ? 'Consulta de Frequência' : 'Lançamento de Frequência';
?>

<style>
    .table th, .table td { vertical-align: middle; }
    .text-success { color: #28a745 !important; }
    .text-danger { color: #dc3545 !important; }
    .fa-check-circle, .fa-times-circle { font-size: 1.3rem; }
    .frequencia-checkbox { transform: scale(1.4); cursor: pointer; }
    .table-hover tbody tr:hover { background-color: #f5f5f5; }
    /* Estilos para a notificação */
    #notification {
        visibility: hidden; min-width: 250px; margin-left: -125px;
        color: #fff; text-align: center; border-radius: 5px; padding: 16px;
        position: fixed; z-index: 1001; left: 50%; bottom: 30px;
        opacity: 0; transition: visibility 0s, opacity 0.5s linear;
    }
    #notification.show {
        visibility: visible; opacity: 1;
    }
    /* Estilos da Barra de Progresso */
    .progress {
        height: 1.5rem;
        background-color: #e9ecef;
        border-radius: .375rem;
        overflow: hidden;
    }
    .progress-bar {
        display: flex;
        flex-direction: column;
        justify-content: center;
        color: white;
        text-align: center;
        white-space: nowrap;
        background-color: #28a745;
        transition: width .6s ease;
        font-weight: bold;
    }
    .bg-success { background-color: #28a745 !important; }
    .bg-warning { background-color: #ffc107 !important; color: #212529 !important; }
    .bg-danger { background-color: #dc3545 !important; }

    /* Estilos Responsivos para Tabela em Formato de Cards */
    @media (max-width: 768px) {
        .responsive-cards.table-responsive {
            overflow-x: visible;
        }
        .responsive-cards table, 
        .responsive-cards thead, 
        .responsive-cards tbody, 
        .responsive-cards th, 
        .responsive-cards td, 
        .responsive-cards tr {
            display: block;
            width: 100%;
        }
        .responsive-cards thead {
            display: none;
        }
        .responsive-cards tr {
            margin-bottom: 1rem;
            border: 1px solid #dee2e6;
            border-radius: 0.5rem;
            background-color: #fff;
            padding: 0.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
        }
        .responsive-cards td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: none;
            border-bottom: 1px solid #f8f9fa;
            padding: 0.75rem 0.5rem;
            text-align: right !important;
        }
        .responsive-cards td:last-child {
            border-bottom: none;
        }
        .responsive-cards td::before {
            content: attr(data-label);
            font-weight: bold;
            text-align: left;
            color: #495057;
            flex-basis: 40%;
            margin-right: 1rem;
        }
        .responsive-cards td .progress {
            flex-grow: 1;
            margin-left: 10px;
            width: auto;
        }
    }
</style>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
        <h2 class="mb-2 mb-md-0"><?= $pageTitle ?> - <?= $ofertaNome ?></h2>
        <a href="index.php?action=professor_dashboard" class="btn btn-secondary">Voltar para Minhas Turmas</a>
    </div>

    <?php if ($isReadOnly): ?>
        <!-- =================== MODO CONSULTA =================== -->
        <div class="table-responsive auto-responsive-cards active">
            <table class="table table-bordered table-hover text-center">
                <thead class="thead-light">
                    <tr>
                        <th class="text-left" style="min-width: 200px;">Aluno</th>
                        <?php if (isset($datas_aulas) && !empty($datas_aulas)): ?>
                            <?php foreach ($datas_aulas as $data): ?>
                                <th style="min-width: 80px;"><?= date('d/m', strtotime($data)) ?></th>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <th class="text-left" style="min-width: 180px;">Frequência (%)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($alunos) && !empty($alunos)): ?>
                        <?php foreach ($alunos as $aluno): ?>
                            <tr>
                                <td class="text-left" data-label="Aluno"><?= htmlspecialchars($aluno['nome']) ?></td>
                                <?php if (isset($datas_aulas) && !empty($datas_aulas)): ?>
                                    <?php foreach ($datas_aulas as $data): ?>
                                        <td data-label="Aula <?= date('d/m', strtotime($data)) ?>">
                                            <?php
                                                $presente = isset($frequencias[$aluno['id']][$data]);
                                                echo $presente ? '<i class="fas fa-check-circle text-success" title="Presente"></i>' : '<i class="fas fa-times-circle text-danger" title="Ausente"></i>';
                                            ?>
                                        </td>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                <td class="text-left" data-label="Frequência (%)">
                                    <?php
                                        $percent = $frequencia_percentual[$aluno['id']] ?? 0;
                                        $bar_color_class = 'bg-success';
                                        if ($percent < 75) $bar_color_class = 'bg-warning';
                                        if ($percent < 50) $bar_color_class = 'bg-danger';
                                    ?>
                                    <div class="progress">
                                        <div class="progress-bar <?= $bar_color_class ?>" role="progressbar" style="width: <?= $percent ?>%;" aria-valuenow="<?= $percent ?>" aria-valuemin="0" aria-valuemax="100">
                                            <?= $percent ?>%
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="<?= (isset($datas_aulas) ? count($datas_aulas) : 0) + 2 ?>" class="text-center">Nenhum aluno aprovado nesta turma.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if (empty($datas_aulas)): ?>
            <div class="alert alert-info text-center">Nenhuma frequência foi lançada para esta turma ainda.</div>
        <?php endif; ?>

    <?php else: ?>
        <!-- =================== MODO LANÇAMENTO =================== -->
        <div class="form-group mb-4" style="max-width: 300px;">
            <label for="data_aula"><strong>Selecione a data da aula:</strong></label>
            <input type="date" id="data_aula" class="form-control" value="<?= $data_aula_selecionada ?>">
        </div>

        <div class="table-responsive auto-responsive-cards active">
            <table class="table table-bordered table-hover" style="max-width: 700px;">
                <thead class="thead-light">
                    <tr>
                        <th>Aluno</th>
                        <th class="text-center" style="width: 150px;">
                            <i class="far fa-calendar-alt mr-2"></i>
                            <strong><?= $data_aula_selecionada ? date('d/m/Y', strtotime($data_aula_selecionada)) : '' ?></strong>
                        </th>
                    </tr>
                </thead>
                <tbody id="frequencia-tbody">
                    <?php if (isset($alunos) && !empty($alunos)): ?>
                        <?php foreach ($alunos as $aluno): ?>
                            <tr>
                                <td data-label="Aluno"><?= htmlspecialchars($aluno['nome']) ?></td>
                                <td class="text-center" data-label="<?= $data_aula_selecionada ? date('d/m/Y', strtotime($data_aula_selecionada)) : 'Data' ?>">
                                    <?php $presente = isset($frequencias[$aluno['id']][$data_aula_selecionada]); ?>
                                    <input type="checkbox" 
                                           class="frequencia-checkbox"
                                           data-aluno-id="<?= $aluno['id'] ?>"
                                           <?= $presente ? 'checked' : '' ?>>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="2" class="text-center">Nenhum aluno aprovado nesta turma.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <script>
        document.addEventListener('DOMContentLoaded', () => {
            const ofertaId = <?= json_encode($_GET['oferta_id'] ?? null) ?>;
            const dataAulaInput = document.getElementById('data_aula');

            dataAulaInput.addEventListener('change', () => {
                const novaData = dataAulaInput.value;
                window.location.href = `index.php?action=lancar_frequencia&oferta_id=${ofertaId}&data_aula=${novaData}`;
            });

            const showNotification = (message, isError = false) => {
                const notification = document.getElementById("notification");
                if (!notification) return;
                notification.textContent = message;
                notification.style.backgroundColor = isError ? '#dc3545' : '#28a745';
                notification.className = "show";
                setTimeout(() => { notification.className = notification.className.replace("show", ""); }, 3000);
            };

            const salvarFrequencia = async (alunoId, dataAula, presente) => {
                try {
                    const response = await fetch('index.php?action=salvar_frequencia', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({ ofertaId, alunoId, dataAula, presente })
                    });
                    if (!response.ok) {
                        const errorData = await response.json().catch(() => ({}));
                        throw new Error(errorData.message || 'Erro do servidor.');
                    }
                    const data = await response.json();
                    if (!data.success) {
                        throw new Error(data.message || 'Não foi possível salvar a frequência.');
                    }
                } catch (error) {
                    showNotification(error.message, true);
                    setTimeout(() => window.location.reload(), 1500);
                }
            };

            document.querySelectorAll('.frequencia-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', () => {
                    const alunoId = checkbox.dataset.alunoId;
                    const dataAula = dataAulaInput.value;
                    const presente = checkbox.checked;
                    salvarFrequencia(alunoId, dataAula, presente);
                });
            });
        });
        </script>
    <?php endif; ?>
</div>

<div id="notification"></div>
