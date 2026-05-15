<?php
$ofertaNome = (isset($oferta) && $oferta) ? htmlspecialchars($oferta->getNome()) : 'Turma não encontrada';
$pageTitle = 'Lançamento de Notas';
?>

<style>
    .table th, .table td { vertical-align: middle; }
    .nota-input { 
        width: 100px;
        text-align: center;
    }
    .status-icon {
        font-size: 1.2rem;
    }
    .text-success { color: #28a745 !important; }
    .text-danger { color: #dc3545 !important; }
    .text-muted { color: #6c757d !important; }
</style>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
        <h2 class="mb-2 mb-md-0"><?= $pageTitle ?> - <?= $ofertaNome ?></h2>
        <a href="index.php?action=professor_dashboard" class="btn btn-secondary">Voltar para Minhas Turmas</a>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">
            Notas salvas com sucesso!
        </div>
    <?php endif; ?>

    <form action="index.php?action=salvarNotas" method="POST">
        <input type="hidden" name="oferta_id" value="<?= htmlspecialchars($_GET['oferta_id'] ?? '') ?>">
        
        <div class="table-responsive">
            <table class="responsive-table">
                <thead>
                    <tr>
                        <th style="width: 40%;">Aluno</th>
                        <th class="text-center" style="text-align: center;">Nota no Banco</th>
                        <th class="text-center" style="text-align: center;">Lançar Nota (0 a 10)</th>
                        <th class="text-center" style="text-align: center;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($alunos) && !empty($alunos)): ?>
                        <?php foreach ($alunos as $aluno): ?>
                            <tr>
                                <td data-label="Aluno"><?= htmlspecialchars($aluno['nome']) ?></td>
                                <td data-label="Nota no Banco" class="text-center font-weight-bold">
                                    <?= $aluno['nota_final'] !== null ? htmlspecialchars(str_replace('.', ',', $aluno['nota_final'])) : '<span class="text-muted">-</span>' ?>
                                </td>
                                <td data-label="Lançar Nota" class="text-center">
                                    <input type="number" 
                                           class="form-control nota-input mx-auto"
                                           name="notas[<?= $aluno['id'] ?>]"
                                           value=""
                                           step="0.1" 
                                           min="0" 
                                           max="10"
                                           placeholder="Ex: 7,5">
                                </td>
                                <td data-label="Status" class="text-center">
                                    <?php
                                        $nota = $aluno['nota_final'];
                                        if ($nota !== null) {
                                            if ($nota >= 5) {
                                                echo '<i class="fas fa-check-circle text-success status-icon" title="Aprovado"></i>';
                                            } else {
                                                echo '<i class="fas fa-times-circle text-danger status-icon" title="Reprovado"></i>';
                                            }
                                        } else {
                                            echo '<i class="fas fa-question-circle text-muted status-icon" title="Pendente"></i>';
                                        }
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center" style="text-align: center;">Nenhum aluno encontrado nesta turma.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="text-right mt-4">
            <button type="submit" class="btn btn-primary">Salvar Todas as Notas</button>
        </div>
    </form>
</div>
