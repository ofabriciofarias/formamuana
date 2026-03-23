<?php require_once __DIR__ . '/../layouts/admin_header.php'; ?>

<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>Relatório Geral de Inscritos</h2>
        <a href="index.php?action=dashboard" class="btn" style="background-color: #95a5a6; color: white;">Voltar</a>
    </div>

    <!-- Filtros Superiores -->
    <div class="card" style="padding: 20px; background: #f8f9fa; border-radius: 8px; margin-bottom: 30px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
        <form action="index.php" method="GET" style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap;">
            <input type="hidden" name="action" value="relatorio_geral">
            
            <div style="flex: 1; min-width: 200px;">
                <label for="oferta_id" style="font-weight: bold; display: block; margin-bottom: 5px;">Filtrar por Oferta:</label>
                <select name="oferta_id" id="oferta_id" class="form-control" style="width: 100%; padding: 10px;">
                    <option value="">Selecione uma oferta...</option>
                    <?php foreach ($ofertas as $oferta): ?>
                        <option value="<?= $oferta->getId() ?>" <?= (isset($_GET['oferta_id']) && $_GET['oferta_id'] == $oferta->getId()) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($oferta->getNome()) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="flex: 1; min-width: 200px;">
                <label for="busca" style="font-weight: bold; display: block; margin-bottom: 5px;">Buscar por Nome:</label>
                <input type="text" name="busca" id="busca" class="form-control" placeholder="Nome do aluno..." value="<?= htmlspecialchars($_GET['busca'] ?? '') ?>" style="width: 100%; padding: 10px;">
            </div>

            <button type="submit" class="btn btn-primary" style="height: 42px;"><i class="fas fa-search"></i> Buscar</button>
        </form>
    </div>

    <?php if (!empty($inscritos)): ?>
        <div class="table-responsive">
            <table class="table" id="relatorioTable" style="width: 100%; border-collapse: collapse; box-shadow: 0 0 20px rgba(0,0,0,0.1);">
                <thead>
                    <tr style="background-color: var(--primary-color); color: white;">
                        <th style="padding: 12px;">
                            Nome do Aluno
                            <input type="text" class="filter-input" onkeyup="filterTable(0)" placeholder="Filtrar...">
                        </th>
                        <th style="padding: 12px;">
                            Oferta
                            <input type="text" class="filter-input" onkeyup="filterTable(1)" placeholder="Filtrar...">
                        </th>
                        <th style="padding: 12px;">
                            Email
                            <input type="text" class="filter-input" onkeyup="filterTable(2)" placeholder="Filtrar...">
                        </th>
                        <th style="padding: 12px;">
                            WhatsApp
                            <input type="text" class="filter-input" onkeyup="filterTable(3)" placeholder="Filtrar...">
                        </th>
                        <th style="padding: 12px;">
                            Renda Familiar
                            <input type="text" class="filter-input" onkeyup="filterTable(4)" placeholder="Filtrar...">
                        </th>
                        <th style="padding: 12px;">
                            Turno Pref.
                            <input type="text" class="filter-input" onkeyup="filterTable(5)" placeholder="Filtrar...">
                        </th>
                        <th style="padding: 12px; width: 200px;">Status da Seleção</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($inscritos as $inscrito): ?>
                        <tr style="border-bottom: 1px solid #ddd;">
                            <td style="padding: 12px;"><?= htmlspecialchars($inscrito['nome']) ?></td>
                            <td style="padding: 12px;"><?= htmlspecialchars($inscrito['nome_oferta']) ?></td>
                            <td style="padding: 12px;"><?= htmlspecialchars($inscrito['email']) ?></td>
                            <td style="padding: 12px;"><?= htmlspecialchars($inscrito['whatsapp']) ?></td>
                            <td style="padding: 12px;"><?= htmlspecialchars($inscrito['renda_familiar']) ?></td>
                            <td style="padding: 12px;"><?= htmlspecialchars($inscrito['turno_preferencia']) ?></td>
                            <td style="padding: 12px;">
                                <form action="index.php?action=updateStatus" method="POST">
                                    <input type="hidden" name="aluno_id" value="<?= $inscrito['alunos_id'] ?>">
                                    <input type="hidden" name="oferta_id" value="<?= $inscrito['oferta_id'] ?>">
                                    <input type="hidden" name="current_oferta_id" value="<?= $_GET['oferta_id'] ?? '' ?>">
                                    <input type="hidden" name="current_busca" value="<?= $_GET['busca'] ?? '' ?>">
                                    <?php
                                        $status = strtolower($inscrito['status_selecao']);
                                        $bgColor = '#fff3cd'; // Cor padrão para Pendente/Lista de Espera
                                        if ($status == 'aprovado') $bgColor = '#d4edda'; // Verde para Aprovado
                                        else if ($status == 'reprovado' || $status == 'cancelado') $bgColor = '#f8d7da'; // Vermelho para Reprovado/Cancelado
                                    ?>
                                    <select name="status_selecao" onchange="this.form.submit()" 
                                            style="padding: 5px; border-radius: 4px; width: 100%; background-color: <?= $bgColor ?>;">
                                        <option value="pendente" <?= $inscrito['status_selecao'] == 'pendente' ? 'selected' : '' ?>>Pendente</option>
                                        <option value="Aprovado" <?= $inscrito['status_selecao'] == 'Aprovado' || $inscrito['status_selecao'] == 'aprovado' ? 'selected' : '' ?>>Aprovado</option>
                                        <option value="Lista de Espera" <?= $inscrito['status_selecao'] == 'Lista de Espera' ? 'selected' : '' ?>>Lista de Espera</option>
                                        <option value="Reprovado" <?= $inscrito['status_selecao'] == 'Reprovado' || $inscrito['status_selecao'] == 'cancelado' ? 'selected' : '' ?>>Reprovado</option>
                                    </select>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php elseif (isset($_GET['oferta_id'])): ?>
        <div class="alert" style="background: #e2e3e5; padding: 15px; border-radius: 5px; text-align: center;">
            Nenhum inscrito encontrado para os filtros selecionados.
        </div>
    <?php else: ?>
        <div class="alert" style="background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px; text-align: center;">
            Selecione uma oferta acima para visualizar os inscritos.
        </div>
    <?php endif; ?>
</div>

<script>
function filterTable(colIndex) {
    var input, filter, table, tr, td, i, txtValue;
    // Pega o input específico da coluna
    var inputs = document.getElementsByClassName("filter-input");
    input = inputs[colIndex];
    filter = input.value.toUpperCase();
    table = document.getElementById("relatorioTable");
    tr = table.getElementsByTagName("tr");

    // Loop por todas as linhas da tabela (começando em 1 para pular o header)
    for (i = 1; i < tr.length; i++) {
        td = tr[i].getElementsByTagName("td")[colIndex];
        if (td) {
            txtValue = td.textContent || td.innerText;
            // Verifica se o texto da célula contém o filtro
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }       
    }
}
</script>

<?php require_once __DIR__ . '/../layouts/admin_footer.php'; ?>