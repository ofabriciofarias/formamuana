<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div style="display:flex; justify-content:space-between; align-items:center;">
    <h1>Dashboard Financeiro</h1>
    <div style="display:flex; align-items:center; gap: 1rem;">
        <a href="?action=dashboard&mes=<?= $mes_anterior ?>" class="btn btn-secondary" title="Mês Anterior">&lt;&lt;</a>
        <h2 style="color: var(--text-muted); margin: 0;"><?= ucfirst($mesAtual) ?></h2>
        <a href="?action=dashboard&mes=<?= $mes_proximo ?>" class="btn btn-secondary" title="Próximo Mês">&gt;&gt;</a>
    </div>
</div>

<?php if (is_admin()): ?>
<div class="dashboard-grid" style="margin-top: 2rem;">
    <div class="stat-card">
        <h3>Faturamento Bruto (Exemplo)</h3>
        <div class="stat-value">R$ <?= number_format($faturamentoBruto, 2, ',', '.') ?></div>
        <p class="text-muted">Total transacionado no mês</p>
    </div>
    
    <div class="stat-card" style="border-left-color: var(--danger-color);">
        <h3>Total de Despesas (Exemplo)</h3>
        <div class="stat-value">R$ <?= number_format($totalDespesas, 2, ',', '.') ?></div>
        <p class="text-muted">Soma de custos do mês</p>
    </div>

    <div class="stat-card" style="border-left-color: var(--success-color);">
        <h3>Resultado Líquido (Exemplo)</h3>
        <div class="stat-value">R$ <?= number_format($lucroLiquido - $totalDespesas, 2, ',', '.') ?></div>
        <p class="text-muted">Lucro de atendimentos - despesas no mês</p>
    </div>
</div>
<?php endif; ?>

<div class="card" style="margin-top: 2rem;">
    <div style="display:flex; justify-content:space-between; align-items:center;">
        <h3>Histórico de Atendimentos (Exemplo)</h3>
        <div style="display:flex; gap: 1rem; align-items:center;">
            <form method="GET" action="index.php" style="display:flex; gap: 0.5rem;">
                <input type="hidden" name="action" value="dashboard">
                <input type="hidden" name="mes" value="<?= htmlspecialchars($mes_selecionado) ?>">
                <input type="text" name="busca" placeholder="Buscar por..." value="<?= htmlspecialchars($busca ?? '') ?>" style="padding: 5px;">
                <button type="submit" class="btn btn-secondary">Buscar</button>
            </form>
            <?php if (is_admin()): ?>
            <a href="#" class="btn btn-primary">Novo Lançamento +</a>
            <?php endif; ?>
        </div>
    </div>
    
    <table class="mobile-card-table" style="margin-top: 1rem;">
        <thead>
            <tr>
                <th>Data</th>
                <th>Paciente</th>
                <th>Ações</th>
                <th>Procedimentos</th>
                <th>Dentista</th>
                <?php if (is_admin()): ?>
                <th>Valor Líquido (Clínica)</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php if(count($ultimosAtendimentos) > 0): ?>
                <?php foreach($ultimosAtendimentos as $at): ?>
                <tr>
                    <!-- Dados de exemplo -->
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="padding: 20px; text-align: center;">Nenhum atendimento registrado para este período.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Paginação -->
    <?php if ($totalPaginas > 1): ?>
    <div style="display: flex; justify-content: flex-end; margin-top: 1rem; gap: 0.5rem;">
        <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
            <a href="?action=dashboard&pagina=<?= $i ?>" class="btn btn-secondary"><?= $i ?></a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
</div>

<!-- Modal de Detalhes do Atendimento -->
<div id="detalhesModal" class="modal">
    <div class="modal-content">
        <span class="modal-close" id="modalCloseBtn">&times;</span>
        <h2>Detalhes do Atendimento</h2>
        <div id="modalBody" style="line-height: 1.6;">
            <!-- Conteúdo será preenchido via JS -->
        </div>
        <div class="modal-footer">
            <button id="modalFooterCloseBtn" class="btn btn-secondary">Fechar</button>
        </div>
    </div>
</div>

<script>
// O Javascript do exemplo foi omitido pois depende dos dados reais dos atendimentos.
// Ele precisará ser reativado e adaptado quando a lógica de dados for implementada.
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
