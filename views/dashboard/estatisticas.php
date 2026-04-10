<?php require_once __DIR__ . '/../layouts/admin_header.php'; ?>

<main class="admin-content">
    <div class="container">
        <h2 class="section-title" style="font-size: 2rem; margin-bottom: 30px;">Relatório Detalhado - Estatísticas</h2>

        <div style="display: flex; gap: 20px; margin-bottom: 40px; flex-wrap: wrap;">
            <div style="flex: 1; background: var(--white); padding: 20px; border-radius: 10px; box-shadow: var(--shadow); text-align: center;">
                <h3 style="color: var(--text-light); font-size: 1.1rem;">Total de Alunos Cadastrados</h3>
                <p style="font-size: 2.5rem; font-weight: bold; color: var(--primary-color); margin: 0;"><?php echo $totalAlunos; ?></p>
            </div>
            <div style="flex: 1; background: var(--white); padding: 20px; border-radius: 10px; box-shadow: var(--shadow); text-align: center;">
                <h3 style="color: var(--text-light); font-size: 1.1rem;">Total Inscrito em Algum Curso</h3>
                <p style="font-size: 2.5rem; font-weight: bold; color: var(--secondary-color); margin: 0;"><?php echo $totalInscritosUnicos; ?></p>
                <p style="color: var(--text-light); font-size: 0.9rem;">
                    <?php 
                    if ($totalAlunos > 0) {
                        echo number_format(($totalInscritosUnicos / $totalAlunos) * 100, 2, ',', '.') . '% dos cadastrados'; 
                    } else {
                        echo '0%';
                    }
                    ?>
                </p>
            </div>
        </div>

        <div style="background: var(--white); padding: 30px; border-radius: 10px; box-shadow: var(--shadow);">
            <h3 style="margin-bottom: 20px; color: var(--primary-color);">Inscritos por Curso e Estatísticas de Porcentagem</h3>
            <div class="table-responsive">
                <table class="responsive-table">
                    <thead>
                        <tr>
                            <th>Curso</th>
                            <th>Turno</th>
                            <th>Vagas</th>
                            <th>Total de Inscritos</th>
                            <th>Aprovados</th>
                            <th>Ocupação de Vagas</th>
                            <th>Concorrência (Inscritos/Vaga)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($estatisticasCursos as $curso): 
                            $vagas = $curso['numero_vagas'] > 0 ? $curso['numero_vagas'] : 1; // Previne divisão por zero
                            $ocupacao = ($curso['total_aprovados'] / $vagas) * 100;
                            $concorrencia = $curso['total_inscritos'] / $vagas;
                        ?>
                            <tr>
                                <td data-label="Curso"><?php echo htmlspecialchars($curso['curso']); ?></td>
                                <td data-label="Turno"><?php echo htmlspecialchars($curso['turno'] ?? 'N/A'); ?></td>
                                <td data-label="Vagas"><?php echo $curso['numero_vagas']; ?></td>
                                <td data-label="Total de Inscritos"><?php echo $curso['total_inscritos']; ?></td>
                                <td data-label="Aprovados"><?php echo $curso['total_aprovados']; ?></td>
                                <td data-label="Ocupação de Vagas">
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <div style="flex: 1; background: #eee; height: 10px; border-radius: 5px; overflow: hidden;">
                                            <div style="background: <?php echo $ocupacao > 100 ? '#e74c3c' : 'var(--secondary-color)'; ?>; width: <?php echo min($ocupacao, 100); ?>%; height: 100%;"></div>
                                        </div>
                                        <span style="font-size: 0.85rem; font-weight: bold; width: 45px; text-align: right;">
                                            <?php echo number_format($ocupacao, 1, ',', '.'); ?>%
                                        </span>
                                    </div>
                                </td>
                                <td data-label="Concorrência">
                                    <?php echo number_format($concorrencia, 2, ',', '.'); ?> inscritos/vaga
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($estatisticasCursos)): ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 20px;">Nenhum curso encontrado.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../layouts/admin_footer.php'; ?>