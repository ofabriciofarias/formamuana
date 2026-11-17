<?php require_once __DIR__ . '/../layouts/admin_header.php'; ?>

<?php
$dimLabels = [
    'ivs_geral'   => 'IVS Geral (Composto)',
    'ivs_infra'   => 'IVS Infraestrutura Urbana',
    'ivs_renda'   => 'IVS Renda e Trabalho',
    'ivs_capital' => 'IVS Capital Humano',
];
$dimAtualLabel = $dimLabels[$filtros['dimensao']] ?? 'IVS Geral (Composto)';

// Helper para construir URL mantendo filtros atuais
$buildFilterUrl = function (array $overrides = []) use ($filtros): string {
    $params = array_merge(['action' => 'relatorio_vulnerabilidade'], $filtros, $overrides);
    return 'index.php?' . http_build_query($params);
};

$abaAtiva = $filtros['aba'] ?: 'geral';
$deltaJustica = $geralInscricoes['delta_justica'];
$justicaPositiva = $deltaJustica >= 0;
?>

<style>
.ivs-header-bar {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    flex-wrap: wrap;
    gap: 15px;
    margin-bottom: 20px;
}
.ivs-subtitle {
    color: var(--text-light);
    font-size: 0.95rem;
    margin-top: -10px;
}
.ivs-actions-bar {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}
.btn-sm {
    padding: 8px 16px;
    font-size: 0.82rem;
    border-radius: 6px;
}
.btn-outline {
    background: var(--white);
    color: var(--primary-color);
    border: 1px solid var(--primary-color);
}
.btn-outline:hover {
    background: var(--primary-color);
    color: var(--white);
}
.ivs-dim-pills {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-bottom: 18px;
}
.dim-pill {
    padding: 8px 16px;
    border-radius: 25px;
    font-size: 0.85rem;
    font-weight: 600;
    background: #eef3f7;
    color: var(--text-dark);
    border: 1px solid #d5e0ea;
    transition: var(--transition);
}
.dim-pill:hover {
    background: #dce8f2;
}
.dim-pill.active {
    background: var(--primary-color);
    color: var(--white);
    border-color: var(--primary-color);
}
.ivs-filter-card {
    background: #f8f9fa;
    border: 1px solid #e3e6ea;
    border-radius: 10px;
    padding: 18px;
    margin-bottom: 24px;
}
.ivs-filter-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
    gap: 12px;
    align-items: end;
}
.ivs-filter-grid label {
    display: block;
    font-size: 0.8rem;
    font-weight: 700;
    color: var(--text-dark);
    margin-bottom: 4px;
}
.ivs-filter-grid select,
.ivs-filter-grid input {
    width: 100%;
    padding: 8px 10px;
    border: 1px solid #ced4da;
    border-radius: 6px;
    font-size: 0.88rem;
    background: #fff;
}
.ivs-kpi-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 16px;
    margin-bottom: 25px;
}
.ivs-kpi-card {
    background: var(--white);
    border-radius: 10px;
    padding: 18px;
    box-shadow: 0 4px 14px rgba(0,0,0,0.06);
    border-top: 4px solid var(--primary-color);
}
.ivs-kpi-card.kpi-vuln { border-top-color: #e67e22; }
.ivs-kpi-card.kpi-rural { border-top-color: #27ae60; }
.ivs-kpi-card.kpi-urbana { border-top-color: #2980b9; }
.ivs-kpi-card.kpi-justica { border-top-color: <?= $justicaPositiva ? '#00b894' : '#e74c3c' ?>; }
.ivs-kpi-title {
    font-size: 0.82rem;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    color: var(--text-light);
    font-weight: 700;
    margin-bottom: 6px;
}
.ivs-kpi-value {
    font-size: 1.85rem;
    font-weight: 800;
    color: var(--text-dark);
    line-height: 1.15;
}
.ivs-kpi-sub {
    font-size: 0.83rem;
    color: var(--text-light);
    margin-top: 6px;
}
.ivs-tabs {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    border-bottom: 2px solid #e1e6eb;
    margin-bottom: 24px;
    padding-bottom: 10px;
}
.ivs-tab-btn {
    padding: 9px 18px;
    border-radius: 8px;
    border: 1px solid transparent;
    background: #f1f4f8;
    color: var(--text-dark);
    font-weight: 600;
    font-size: 0.88rem;
    cursor: pointer;
    transition: var(--transition);
}
.ivs-tab-btn:hover {
    background: #e2e9f0;
}
.ivs-tab-btn.active {
    background: var(--primary-color);
    color: var(--white);
}
.ivs-tab-panel {
    display: none;
}
.ivs-tab-panel.active {
    display: block;
}
.ivs-card {
    background: var(--white);
    border-radius: 10px;
    padding: 22px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.06);
    margin-bottom: 24px;
}
.ivs-card h3 {
    font-size: 1.12rem;
    margin-bottom: 14px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.ivs-charts-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(360px, 1fr));
    gap: 20px;
    margin-bottom: 24px;
}
.ivs-chart-box {
    background: var(--white);
    border-radius: 10px;
    padding: 18px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.06);
}
.ivs-chart-box h4 {
    font-size: 0.98rem;
    margin-bottom: 12px;
    color: var(--primary-color);
}
.chart-canvas-wrap {
    position: relative;
    height: 280px;
    width: 100%;
}
.ivs-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.88rem;
}
.ivs-table th {
    background: var(--primary-color);
    color: #fff;
    padding: 10px 10px;
    text-align: left;
    font-weight: 600;
    white-space: nowrap;
}
.ivs-table td {
    padding: 9px 10px;
    border-bottom: 1px solid #eaedf0;
    vertical-align: middle;
}
.ivs-table tbody tr:hover {
    background: #f8fafc;
}
.badge-pill {
    display: inline-block;
    padding: 3px 9px;
    border-radius: 12px;
    font-size: 0.76rem;
    font-weight: 700;
    color: #fff;
    white-space: nowrap;
}
.badge-alcancado { background: #00b894; }
.badge-lacuna { background: #e74c3c; }
.badge-justo { background: #00b894; }
.badge-atencao { background: #e67e22; }
.progress-mini {
    display: flex;
    align-items: center;
    gap: 8px;
    min-width: 110px;
}
.progress-mini-bar {
    flex: 1;
    height: 8px;
    background: #e9ecef;
    border-radius: 4px;
    overflow: hidden;
}
.progress-mini-fill {
    height: 100%;
    border-radius: 4px;
}
.methodology-box {
    background: #f4f8fb;
    border-left: 4px solid var(--primary-color);
    padding: 16px 20px;
    border-radius: 6px;
    font-size: 0.86rem;
    color: var(--text-dark);
}
@media print {
    .admin-header, .ivs-filter-card, .ivs-actions-bar, .ivs-dim-pills, .ivs-tabs, footer {
        display: none !important;
    }
    .ivs-tab-panel {
        display: block !important;
    }
}
</style>

<div class="ivs-header-bar">
    <div>
        <h2 class="section-title" style="font-size: 1.85rem; margin-bottom: 10px;">
            <i class="fas fa-balance-scale"></i> Relatório de Vulnerabilidade Social, Territorial e Alcance (IVS - IPEA)
        </h2>
        <p class="ivs-subtitle">
            Estratificação por <strong>Geral</strong>, <strong>Curso</strong> e <strong>Turma</strong> • Dimensão ativa: <strong><?= htmlspecialchars($dimAtualLabel) ?></strong>
        </p>
    </div>
    <div class="ivs-actions-bar">
        <button type="button" class="btn btn-outline btn-sm" onclick="exportTableToCSV('relatorio_ivs_vulnerabilidade.csv')">
            <i class="fas fa-file-csv"></i> Exportar CSV
        </button>
        <button type="button" class="btn btn-outline btn-sm" onclick="window.print()">
            <i class="fas fa-print"></i> Imprimir
        </button>
        <a href="index.php?action=dashboard" class="btn btn-sm" style="background: #7f8c8d; color: #fff;">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>
</div>

<!-- Botões Rápidos de Dimensão IVS -->
<div class="ivs-dim-pills">
    <?php foreach ($dimLabels as $dKey => $dLabel): ?>
        <a href="<?= htmlspecialchars($buildFilterUrl(['dimensao' => $dKey])) ?>"
           class="dim-pill <?= $filtros['dimensao'] === $dKey ? 'active' : '' ?>">
            <?= htmlspecialchars($dLabel) ?>
        </a>
    <?php endforeach; ?>
</div>

<!-- Filtros de Navegabilidade -->
<div class="ivs-filter-card">
    <form action="index.php" method="GET">
        <input type="hidden" name="action" value="relatorio_vulnerabilidade">
        <input type="hidden" name="aba" id="inputAbaAtiva" value="<?= htmlspecialchars($abaAtiva) ?>">
        <div class="ivs-filter-grid">
            <div>
                <label for="filtro_curso">Curso</label>
                <select name="curso" id="filtro_curso">
                    <option value="">Todos os Cursos</option>
                    <?php foreach ($cursosUnicos as $cNome): ?>
                        <option value="<?= htmlspecialchars($cNome) ?>" <?= $filtros['curso'] === $cNome ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cNome) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="filtro_oferta">Turma (Oferta)</label>
                <select name="oferta_id" id="filtro_oferta">
                    <option value="">Todas as Turmas</option>
                    <?php foreach ($ofertas as $of): ?>
                        <option value="<?= $of['id'] ?>" <?= (string)$filtros['oferta_id'] === (string)$of['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($of['nome'] . ' — ' . ($of['turno'] ?? 'N/I')) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="filtro_dimensao">Dimensão IVS</label>
                <select name="dimensao" id="filtro_dimensao">
                    <?php foreach ($dimLabels as $dKey => $dLabel): ?>
                        <option value="<?= $dKey ?>" <?= $filtros['dimensao'] === $dKey ? 'selected' : '' ?>>
                            <?= htmlspecialchars($dLabel) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="filtro_faixa">Faixa de Vulnerabilidade (IPEA)</label>
                <select name="faixa" id="filtro_faixa">
                    <option value="todos" <?= $filtros['faixa'] === 'todos' ? 'selected' : '' ?>>Todas as Faixas</option>
                    <option value="vulneravel" <?= $filtros['faixa'] === 'vulneravel' ? 'selected' : '' ?>>Em Vulnerabilidade (Alta + Muito Alta ≥ 0,401)</option>
                    <option value="muito_alta" <?= $filtros['faixa'] === 'muito_alta' ? 'selected' : '' ?>>Muito Alta (0,501–1,000)</option>
                    <option value="alta" <?= $filtros['faixa'] === 'alta' ? 'selected' : '' ?>>Alta (0,401–0,500)</option>
                    <option value="media" <?= $filtros['faixa'] === 'media' ? 'selected' : '' ?>>Média (0,301–0,400)</option>
                    <option value="baixa" <?= $filtros['faixa'] === 'baixa' ? 'selected' : '' ?>>Baixa (0,201–0,300)</option>
                    <option value="muito_baixa" <?= $filtros['faixa'] === 'muito_baixa' ? 'selected' : '' ?>>Muito Baixa (0,000–0,200)</option>
                </select>
            </div>
            <div>
                <label for="filtro_zona">Zona de Residência</label>
                <select name="zona" id="filtro_zona">
                    <option value="todos" <?= $filtros['zona'] === 'todos' ? 'selected' : '' ?>>Todas (Rural e Urbana)</option>
                    <option value="Rural" <?= $filtros['zona'] === 'Rural' ? 'selected' : '' ?>>Zona Rural</option>
                    <option value="Urbana" <?= $filtros['zona'] === 'Urbana' ? 'selected' : '' ?>>Zona Urbana</option>
                    <option value="Não Informada" <?= $filtros['zona'] === 'Não Informada' ? 'selected' : '' ?>>Não Informada</option>
                </select>
            </div>
            <div>
                <label for="filtro_status">Situação de Atendimento</label>
                <select name="status" id="filtro_status">
                    <option value="todos" <?= $filtros['status'] === 'todos' ? 'selected' : '' ?>>Todos</option>
                    <option value="alcancado" <?= $filtros['status'] === 'alcancado' ? 'selected' : '' ?>>Alcançados (Aprovados)</option>
                    <option value="lacuna" <?= $filtros['status'] === 'lacuna' ? 'selected' : '' ?>>Lacuna (Não Atendidos / Pendentes)</option>
                </select>
            </div>
            <div>
                <label for="filtro_cidade">Cidade de Origem / Atual</label>
                <select name="cidade" id="filtro_cidade">
                    <option value="">Todas as Cidades</option>
                    <?php foreach ($cidadesDisponiveis as $cid): ?>
                        <option value="<?= htmlspecialchars($cid) ?>" <?= $filtros['cidade'] === $cid ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cid) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div style="display: flex; gap: 8px;">
                <button type="submit" class="btn btn-primary btn-sm" style="flex: 1;">
                    <i class="fas fa-filter"></i> Aplicar
                </button>
                <a href="index.php?action=relatorio_vulnerabilidade" class="btn btn-sm" style="background: #95a5a6; color: #fff;" title="Limpar Filtros">
                    <i class="fas fa-undo"></i>
                </a>
            </div>
        </div>
    </form>
</div>

<!-- KPIs Principais -->
<div class="ivs-kpi-grid">
    <div class="ivs-kpi-card kpi-vuln">
        <div class="ivs-kpi-title">Vulneráveis (Alta/Muito Alta)</div>
        <div class="ivs-kpi-value"><?= $geralInscricoes['vuln_alcancados'] ?> <span style="font-size: 1rem; color: #7f8c8d;">/ <?= $geralInscricoes['vuln_total'] ?></span></div>
        <div class="ivs-kpi-sub">
            Alcance: <strong><?= number_format($geralInscricoes['taxa_alcance_vuln'], 1, ',', '.') ?>%</strong> •
            Lacuna: <strong style="color: #e74c3c;"><?= $geralInscricoes['vuln_lacuna'] ?> não atendidos</strong>
        </div>
    </div>

    <div class="ivs-kpi-card kpi-rural">
        <div class="ivs-kpi-title">Zona Rural</div>
        <div class="ivs-kpi-value"><?= $geralInscricoes['rural_alcancados'] ?> <span style="font-size: 1rem; color: #7f8c8d;">/ <?= $geralInscricoes['rural_total'] ?></span></div>
        <div class="ivs-kpi-sub">
            Alcance: <strong><?= number_format($geralInscricoes['taxa_alcance_rural'], 1, ',', '.') ?>%</strong> •
            Lacuna: <strong style="color: #e74c3c;"><?= $geralInscricoes['rural_lacuna'] ?> não atendidos</strong>
        </div>
    </div>

    <div class="ivs-kpi-card kpi-urbana">
        <div class="ivs-kpi-title">Zona Urbana</div>
        <div class="ivs-kpi-value"><?= $geralInscricoes['urbana_alcancados'] ?> <span style="font-size: 1rem; color: #7f8c8d;">/ <?= $geralInscricoes['urbana_total'] ?></span></div>
        <div class="ivs-kpi-sub">
            Alcance: <strong><?= number_format($geralInscricoes['taxa_alcance_urbana'], 1, ',', '.') ?>%</strong> •
            Lacuna: <strong style="color: #e74c3c;"><?= $geralInscricoes['urbana_lacuna'] ?> não atendidos</strong>
        </div>
    </div>

    <div class="ivs-kpi-card kpi-justica">
        <div class="ivs-kpi-title">Justiça da Seleção (<?= htmlspecialchars($dimAtualLabel) ?>)</div>
        <div class="ivs-kpi-value">
            <?= $deltaJustica >= 0 ? '+' : '' ?><?= number_format($deltaJustica, 3, ',', '.') ?>
        </div>
        <div class="ivs-kpi-sub">
            IVS Médio Aprovados: <strong><?= number_format($geralInscricoes['media_dim_alcancados'], 3, ',', '.') ?></strong> vs
            Lacuna: <strong><?= number_format($geralInscricoes['media_dim_lacuna'], 3, ',', '.') ?></strong>
            <br>
            <span class="badge-pill <?= $justicaPositiva ? 'badge-justo' : 'badge-atencao' ?>" style="margin-top: 4px;">
                <?= $justicaPositiva ? 'Priorizou Mais Vulneráveis' : 'Atenção: Lacuna tem IVS Maior' ?>
            </span>
        </div>
    </div>
</div>

<!-- Botões de Navegação entre Estratificações -->
<div class="ivs-tabs">
    <button type="button" class="ivs-tab-btn <?= $abaAtiva === 'geral' ? 'active' : '' ?>" onclick="switchTab('geral', this)">
        <i class="fas fa-globe"></i> Visão Geral &amp; Gráficos
    </button>
    <button type="button" class="ivs-tab-btn <?= $abaAtiva === 'curso' ? 'active' : '' ?>" onclick="switchTab('curso', this)">
        <i class="fas fa-book"></i> Estratificação por Curso (<?= count($porCurso) ?>)
    </button>
    <button type="button" class="ivs-tab-btn <?= $abaAtiva === 'turma' ? 'active' : '' ?>" onclick="switchTab('turma', this)">
        <i class="fas fa-chalkboard-teacher"></i> Estratificação por Turma (<?= count($porTurma) ?>)
    </button>
    <button type="button" class="ivs-tab-btn <?= $abaAtiva === 'justica' ? 'active' : '' ?>" onclick="switchTab('justica', this)">
        <i class="fas fa-map-marked-alt"></i> Localidade &amp; Justiça
    </button>
    <button type="button" class="ivs-tab-btn <?= $abaAtiva === 'nominal' ? 'active' : '' ?>" onclick="switchTab('nominal', this)">
        <i class="fas fa-users"></i> Lista Nominal (<?= count($registrosFiltrados) ?>)
    </button>
</div>

<!-- ABA 1: VISÃO GERAL & GRÁFICOS -->
<div id="tab-geral" class="ivs-tab-panel <?= $abaAtiva === 'geral' ? 'active' : '' ?>">

    <!-- Gráficos -->
    <div class="ivs-charts-grid">
        <div class="ivs-chart-box">
            <h4><i class="fas fa-chart-bar"></i> Vulneráveis Alcançados vs. Lacuna por Dimensão IVS</h4>
            <div class="chart-canvas-wrap">
                <canvas id="chartDimensoes"></canvas>
            </div>
        </div>

        <div class="ivs-chart-box">
            <h4><i class="fas fa-tractor"></i> Zona Rural vs. Zona Urbana (Alcançados vs. Lacuna)</h4>
            <div class="chart-canvas-wrap">
                <canvas id="chartZona"></canvas>
            </div>
        </div>

        <div class="ivs-chart-box">
            <h4><i class="fas fa-layer-group"></i> Distribuição pelas Faixas IPEA (<?= htmlspecialchars($dimAtualLabel) ?>)</h4>
            <div class="chart-canvas-wrap">
                <canvas id="chartFaixas"></canvas>
            </div>
        </div>

        <div class="ivs-chart-box">
            <h4><i class="fas fa-spider"></i> Radar de Justiça: IVS Médio (Aprovados vs. Lacuna)</h4>
            <div class="chart-canvas-wrap">
                <canvas id="chartRadarJustica"></canvas>
            </div>
        </div>
    </div>

    <!-- Resumo das 4 Dimensões IVS -->
    <div class="ivs-card">
        <h3><i class="fas fa-sliders-h"></i> Comparativo Geral pelas 4 Dimensões do IVS (IPEA)</h3>
        <div class="table-responsive">
            <table class="ivs-table">
                <thead>
                    <tr>
                        <th>Dimensão do IVS</th>
                        <th>Vulneráveis Totais (≥ 0,401)</th>
                        <th>Alcançados (Aprovados)</th>
                        <th>Lacuna (Não Atendidos)</th>
                        <th>Taxa de Alcance</th>
                        <th>IVS Médio (Aprovados)</th>
                        <th>IVS Médio (Lacuna)</th>
                        <th>Justiça (Δ IVS)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dimensoesResumo as $dKey => $d): ?>
                        <tr style="<?= $filtros['dimensao'] === $dKey ? 'background: #eef6fc; font-weight: 600;' : '' ?>">
                            <td><?= htmlspecialchars($d['nome']) ?></td>
                            <td><?= $d['vuln_total'] ?></td>
                            <td><span class="badge-pill badge-alcancado"><?= $d['vuln_alcancados'] ?></span></td>
                            <td><span class="badge-pill badge-lacuna"><?= $d['vuln_lacuna'] ?></span></td>
                            <td>
                                <div class="progress-mini">
                                    <div class="progress-mini-bar">
                                        <div class="progress-mini-fill" style="width: <?= min($d['taxa_alcance'], 100) ?>%; background: var(--secondary-color);"></div>
                                    </div>
                                    <span><?= number_format($d['taxa_alcance'], 1, ',', '.') ?>%</span>
                                </div>
                            </td>
                            <td><?= number_format($d['media_alc'], 3, ',', '.') ?></td>
                            <td><?= number_format($d['media_lac'], 3, ',', '.') ?></td>
                            <td>
                                <span class="badge-pill <?= $d['delta_justica'] >= 0 ? 'badge-justo' : 'badge-atencao' ?>">
                                    <?= $d['delta_justica'] >= 0 ? '+' : '' ?><?= number_format($d['delta_justica'], 3, ',', '.') ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Alcance Global da Base de Alunos (Únicos) vs Inscrições -->
    <div class="ivs-card">
        <h3><i class="fas fa-database"></i> Alcance Global do Projeto (Base Total de Alunos Únicos vs. Inscrições)</h3>
        <div class="table-responsive">
            <table class="ivs-table">
                <thead>
                    <tr>
                        <th>Escopo</th>
                        <th>Total</th>
                        <th>Alcançados (Geral)</th>
                        <th>Lacuna (Geral)</th>
                        <th>Vulneráveis (Alcançados / Lacuna)</th>
                        <th>Zona Rural (Alcançados / Lacuna)</th>
                        <th>Zona Urbana (Alcançados / Lacuna)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Alunos Únicos Cadastrados (Base <code>alunos</code>)</strong><br>
                            <small style="color: #7f8c8d;"><?= $geralAlunosUnicos['sem_inscricao'] ?> sem inscrição em curso (<?= $geralAlunosUnicos['vuln_sem_inscricao'] ?> vulneráveis)</small>
                        </td>
                        <td><?= $geralAlunosUnicos['total'] ?></td>
                        <td><?= $geralAlunosUnicos['alcancados'] ?> (<?= number_format($geralAlunosUnicos['taxa_alcance_geral'], 1, ',', '.') ?>%)</td>
                        <td><?= $geralAlunosUnicos['lacuna'] ?></td>
                        <td>
                            <span class="badge-pill badge-alcancado"><?= $geralAlunosUnicos['vuln_alcancados'] ?></span> /
                            <span class="badge-pill badge-lacuna"><?= $geralAlunosUnicos['vuln_lacuna'] ?></span>
                            (<?= number_format($geralAlunosUnicos['taxa_alcance_vuln'], 1, ',', '.') ?>%)
                        </td>
                        <td>
                            <span class="badge-pill badge-alcancado"><?= $geralAlunosUnicos['rural_alcancados'] ?></span> /
                            <span class="badge-pill badge-lacuna"><?= $geralAlunosUnicos['rural_lacuna'] ?></span>
                            (<?= number_format($geralAlunosUnicos['taxa_alcance_rural'], 1, ',', '.') ?>%)
                        </td>
                        <td>
                            <span class="badge-pill badge-alcancado"><?= $geralAlunosUnicos['urbana_alcancados'] ?></span> /
                            <span class="badge-pill badge-lacuna"><?= $geralAlunosUnicos['urbana_lacuna'] ?></span>
                            (<?= number_format($geralAlunosUnicos['taxa_alcance_urbana'], 1, ',', '.') ?>%)
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Inscrições Filtradas (<code>alunos_has_oferta</code>)</strong></td>
                        <td><?= $geralInscricoes['total'] ?></td>
                        <td><?= $geralInscricoes['alcancados'] ?> (<?= number_format($geralInscricoes['taxa_alcance_geral'], 1, ',', '.') ?>%)</td>
                        <td><?= $geralInscricoes['lacuna'] ?></td>
                        <td>
                            <span class="badge-pill badge-alcancado"><?= $geralInscricoes['vuln_alcancados'] ?></span> /
                            <span class="badge-pill badge-lacuna"><?= $geralInscricoes['vuln_lacuna'] ?></span>
                            (<?= number_format($geralInscricoes['taxa_alcance_vuln'], 1, ',', '.') ?>%)
                        </td>
                        <td>
                            <span class="badge-pill badge-alcancado"><?= $geralInscricoes['rural_alcancados'] ?></span> /
                            <span class="badge-pill badge-lacuna"><?= $geralInscricoes['rural_lacuna'] ?></span>
                            (<?= number_format($geralInscricoes['taxa_alcance_rural'], 1, ',', '.') ?>%)
                        </td>
                        <td>
                            <span class="badge-pill badge-alcancado"><?= $geralInscricoes['urbana_alcancados'] ?></span> /
                            <span class="badge-pill badge-lacuna"><?= $geralInscricoes['urbana_lacuna'] ?></span>
                            (<?= number_format($geralInscricoes['taxa_alcance_urbana'], 1, ',', '.') ?>%)
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ABA 2: ESTRATIFICAÇÃO POR CURSO -->
<div id="tab-curso" class="ivs-tab-panel <?= $abaAtiva === 'curso' ? 'active' : '' ?>">
    <div class="ivs-chart-box" style="margin-bottom: 24px;">
        <h4><i class="fas fa-chart-bar"></i> Taxa de Alcance de Vulnerabilidade por Curso (%)</h4>
        <div class="chart-canvas-wrap" style="height: 300px;">
            <canvas id="chartCursos"></canvas>
        </div>
    </div>

    <div class="ivs-card">
        <h3><i class="fas fa-book"></i> Estratificação por Curso (Todas as Turmas Agrupadas)</h3>
        <div class="table-responsive">
            <table class="ivs-table">
                <thead>
                    <tr>
                        <th>Curso</th>
                        <th>Turmas / Vagas</th>
                        <th>Inscritos</th>
                        <th>Vulneráveis (Alc. / Lac.)</th>
                        <th>Alcance Vuln.</th>
                        <th>Rural (Alc. / Lac.)</th>
                        <th>Urbana (Alc. / Lac.)</th>
                        <th>IVS Médio (G / I / R / C)</th>
                        <th>Justiça (Δ IVS)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($porCurso as $c): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($c['label']) ?></strong></td>
                            <td><?= $c['qtd_turmas'] ?> t / <?= $c['vagas'] ?> v</td>
                            <td><?= $c['total'] ?> (<?= $c['alcancados'] ?> apr.)</td>
                            <td>
                                <span class="badge-pill badge-alcancado"><?= $c['vuln_alcancados'] ?></span> /
                                <span class="badge-pill badge-lacuna"><?= $c['vuln_lacuna'] ?></span>
                            </td>
                            <td>
                                <div class="progress-mini">
                                    <div class="progress-mini-bar">
                                        <div class="progress-mini-fill" style="width: <?= min($c['taxa_alcance_vuln'], 100) ?>%; background: #e67e22;"></div>
                                    </div>
                                    <span><?= number_format($c['taxa_alcance_vuln'], 1, ',', '.') ?>%</span>
                                </div>
                            </td>
                            <td><?= $c['rural_alcancados'] ?> / <?= $c['rural_lacuna'] ?> (<?= number_format($c['taxa_alcance_rural'], 0) ?>%)</td>
                            <td><?= $c['urbana_alcancados'] ?> / <?= $c['urbana_lacuna'] ?> (<?= number_format($c['taxa_alcance_urbana'], 0) ?>%)</td>
                            <td style="font-size: 0.8rem;">
                                <strong><?= number_format($c['media_ivs_geral'], 3, ',', '.') ?></strong> /
                                <?= number_format($c['media_ivs_infra'], 3, ',', '.') ?> /
                                <?= number_format($c['media_ivs_renda'], 3, ',', '.') ?> /
                                <?= number_format($c['media_ivs_capital'], 3, ',', '.') ?>
                            </td>
                            <td>
                                <span class="badge-pill <?= $c['delta_justica'] >= 0 ? 'badge-justo' : 'badge-atencao' ?>">
                                    <?= $c['delta_justica'] >= 0 ? '+' : '' ?><?= number_format($c['delta_justica'], 3, ',', '.') ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($porCurso)): ?>
                        <tr><td colspan="9" style="text-align: center; padding: 20px;">Nenhum dado encontrado para os filtros selecionados.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ABA 3: ESTRATIFICAÇÃO POR TURMA -->
<div id="tab-turma" class="ivs-tab-panel <?= $abaAtiva === 'turma' ? 'active' : '' ?>">
    <div class="ivs-card">
        <h3><i class="fas fa-chalkboard-teacher"></i> Estratificação por Turma (Oferta Individual)</h3>
        <div class="table-responsive">
            <table class="ivs-table">
                <thead>
                    <tr>
                        <th>Turma / Curso</th>
                        <th>Turno / Local</th>
                        <th>Vagas / Insc.</th>
                        <th>Vulneráveis (Alc. / Lac.)</th>
                        <th>Alcance Vuln.</th>
                        <th>Rural (Alc. / Lac.)</th>
                        <th>Urbana (Alc. / Lac.)</th>
                        <th>IVS Médio (Aprov. vs Lac.)</th>
                        <th>Justiça (Δ IVS)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($porTurma as $t): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($t['label']) ?></strong></td>
                            <td><?= htmlspecialchars($t['turno']) ?><br><small style="color: #7f8c8d;"><?= htmlspecialchars($t['local']) ?></small></td>
                            <td><?= $t['vagas'] ?> v / <?= $t['total'] ?> i</td>
                            <td>
                                <span class="badge-pill badge-alcancado"><?= $t['vuln_alcancados'] ?></span> /
                                <span class="badge-pill badge-lacuna"><?= $t['vuln_lacuna'] ?></span>
                            </td>
                            <td>
                                <div class="progress-mini">
                                    <div class="progress-mini-bar">
                                        <div class="progress-mini-fill" style="width: <?= min($t['taxa_alcance_vuln'], 100) ?>%; background: #e67e22;"></div>
                                    </div>
                                    <span><?= number_format($t['taxa_alcance_vuln'], 1, ',', '.') ?>%</span>
                                </div>
                            </td>
                            <td><?= $t['rural_alcancados'] ?> / <?= $t['rural_lacuna'] ?></td>
                            <td><?= $t['urbana_alcancados'] ?> / <?= $t['urbana_lacuna'] ?></td>
                            <td><?= number_format($t['media_dim_alcancados'], 3, ',', '.') ?> vs <?= number_format($t['media_dim_lacuna'], 3, ',', '.') ?></td>
                            <td>
                                <span class="badge-pill <?= $t['delta_justica'] >= 0 ? 'badge-justo' : 'badge-atencao' ?>">
                                    <?= $t['delta_justica'] >= 0 ? '+' : '' ?><?= number_format($t['delta_justica'], 3, ',', '.') ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($porTurma)): ?>
                        <tr><td colspan="9" style="text-align: center; padding: 20px;">Nenhuma turma encontrada para os filtros selecionados.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ABA 4: LOCALIDADE & JUSTIÇA -->
<div id="tab-justica" class="ivs-tab-panel <?= $abaAtiva === 'justica' ? 'active' : '' ?>">
    <div class="ivs-card">
        <h3><i class="fas fa-map-marker-alt"></i> Estratificação por Cidade de Origem / Atual</h3>
        <div class="table-responsive">
            <table class="ivs-table">
                <thead>
                    <tr>
                        <th>Cidade</th>
                        <th>Total Inscritos</th>
                        <th>Alcançados</th>
                        <th>Lacuna</th>
                        <th>Vulneráveis (Alc. / Lac.)</th>
                        <th>Rural (Alc. / Lac.)</th>
                        <th>Urbana (Alc. / Lac.)</th>
                        <th>IVS Geral Médio</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($porCidade as $cid): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($cid['label']) ?></strong></td>
                            <td><?= $cid['total'] ?></td>
                            <td><?= $cid['alcancados'] ?></td>
                            <td><?= $cid['lacuna'] ?></td>
                            <td>
                                <span class="badge-pill badge-alcancado"><?= $cid['vuln_alcancados'] ?></span> /
                                <span class="badge-pill badge-lacuna"><?= $cid['vuln_lacuna'] ?></span>
                            </td>
                            <td><?= $cid['rural_alcancados'] ?> / <?= $cid['rural_lacuna'] ?></td>
                            <td><?= $cid['urbana_alcancados'] ?> / <?= $cid['urbana_lacuna'] ?></td>
                            <td><?= number_format($cid['media_ivs_geral'], 3, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="methodology-box">
        <strong><i class="fas fa-book-open"></i> Nota Metodológica — Modelo IVS (IPEA, 2015) aplicado às tabelas <code>alunos</code> e <code>alunos_has_oferta</code>:</strong>
        <ul style="margin-top: 8px; padding-left: 20px; list-style: disc;">
            <li><strong>IVS Infraestrutura Urbana (0,000–1,000):</strong> <code>zona_residencia</code> (Rural/Urbana), <code>acesso_internet</code>, <code>dispositivo_principal</code> e <code>comunidade_tradicional</code>.</li>
            <li><strong>IVS Capital Humano (0,000–1,000):</strong> <code>nivel_escolaridade</code>, <code>tipo_escola_ensino_medio</code>, <code>possui_filhos</code>/<code>quantidade_filhos</code> e <code>possui_deficiencia</code>.</li>
            <li><strong>IVS Renda e Trabalho (0,000–1,000):</strong> <code>renda_familiar</code>, <code>numero_membros_familia</code>, <code>exerce_atividade_remunerada</code>, <code>beneficiario_programa_social</code> e <code>participacao_economica_familiar</code>.</li>
            <li><strong>IVS Geral:</strong> Média aritmética das 3 dimensões (IPEA). Faixas: Muito Baixa (≤0,200), Baixa (0,201–0,300), Média (0,301–0,400), Alta (0,401–0,500) e Muito Alta (≥0,501). Alunos em <em>Alta</em> ou <em>Muito Alta</em> vulnerabilidade são contabilizados no grupo prioritário.</li>
            <li><strong>Justiça da Seleção (Δ IVS):</strong> Diferença entre o IVS médio dos <em>Aprovados</em> e o IVS médio dos <em>Não Atendidos (Lacuna)</em>. Valores positivos indicam que a seleção priorizou quem mais precisava.</li>
        </ul>
    </div>
</div>

<!-- ABA 5: LISTA NOMINAL AUDITÁVEL -->
<div id="tab-nominal" class="ivs-tab-panel <?= $abaAtiva === 'nominal' ? 'active' : '' ?>">
    <div class="ivs-card">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; margin-bottom: 14px;">
            <h3 style="margin-bottom: 0;"><i class="fas fa-users"></i> Detalhamento Nominal para Auditoria da Seleção</h3>
            <input type="text" id="buscaNominal" onkeyup="filtrarTabelaNominal()" placeholder="Buscar aluno, curso ou cidade..."
                   style="padding: 8px 12px; border: 1px solid #ced4da; border-radius: 6px; min-width: 260px;">
        </div>
        <div class="table-responsive">
            <table class="ivs-table" id="tabelaNominal">
                <thead>
                    <tr>
                        <th>Aluno</th>
                        <th>Curso / Turma</th>
                        <th>Zona / Cidade</th>
                        <th>IVS Geral</th>
                        <th>IVS Infra</th>
                        <th>IVS Renda</th>
                        <th>IVS Capital</th>
                        <th>Faixa (<?= htmlspecialchars($dimAtualLabel) ?>)</th>
                        <th>Situação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($registrosFiltrados as $reg):
                        $faixaReg = $reg[$faixaKey];
                    ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($reg['aluno_nome']) ?></strong></td>
                            <td><?= htmlspecialchars($reg['curso_nome']) ?><br><small style="color: #7f8c8d;"><?= htmlspecialchars($reg['oferta_turno'] ?? '') ?></small></td>
                            <td><?= htmlspecialchars($reg['zona_norm']) ?><br><small style="color: #7f8c8d;"><?= htmlspecialchars($reg['cidade_ref']) ?></small></td>
                            <td><strong><?= number_format($reg['ivs_geral'], 3, ',', '.') ?></strong></td>
                            <td><?= number_format($reg['ivs_infra'], 3, ',', '.') ?></td>
                            <td><?= number_format($reg['ivs_renda'], 3, ',', '.') ?></td>
                            <td><?= number_format($reg['ivs_capital'], 3, ',', '.') ?></td>
                            <td>
                                <span class="badge-pill" style="background: <?= htmlspecialchars($faixaReg['color']) ?>;">
                                    <?= htmlspecialchars($faixaReg['label']) ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($reg['alcancado']): ?>
                                    <span class="badge-pill badge-alcancado">Alcançado (Aprovado)</span>
                                <?php else: ?>
                                    <span class="badge-pill badge-lacuna">Lacuna (<?= htmlspecialchars($reg['status_selecao'] ?: 'Pendente') ?>)</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($registrosFiltrados)): ?>
                        <tr><td colspan="9" style="text-align: center; padding: 20px;">Nenhum registro encontrado.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
function switchTab(tabId, btn) {
    document.querySelectorAll('.ivs-tab-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.ivs-tab-btn').forEach(b => b.classList.remove('active'));
    const panel = document.getElementById('tab-' + tabId);
    if (panel) panel.classList.add('active');
    if (btn) btn.classList.add('active');
    const hiddenAba = document.getElementById('inputAbaAtiva');
    if (hiddenAba) hiddenAba.value = tabId;
}

function filtrarTabelaNominal() {
    const filtro = document.getElementById('buscaNominal').value.toUpperCase();
    const rows = document.querySelectorAll('#tabelaNominal tbody tr');
    rows.forEach(tr => {
        const text = tr.textContent || tr.innerText;
        tr.style.display = text.toUpperCase().indexOf(filtro) > -1 ? '' : 'none';
    });
}

function exportTableToCSV(filename) {
    const table = document.getElementById('tabelaNominal');
    if (!table) return;
    const rows = table.querySelectorAll('tr');
    const csv = [];
    rows.forEach(row => {
        if (row.style.display === 'none') return;
        const cols = row.querySelectorAll('th, td');
        const rowData = [];
        cols.forEach(col => {
            let txt = col.innerText.replace(/\s+/g, ' ').trim();
            txt = '"' + txt.replace(/"/g, '""') + '"';
            rowData.push(txt);
        });
        csv.push(rowData.join(';'));
    });
    const blob = new Blob(["\uFEFF" + csv.join('\n')], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = filename;
    link.click();
}

document.addEventListener('DOMContentLoaded', function() {
    if (typeof Chart === 'undefined') return;

    // 1. Gráfico Dimensões IVS
    new Chart(document.getElementById('chartDimensoes'), {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_column($dimensoesResumo, 'nome')) ?>,
            datasets: [
                {
                    label: 'Vulneráveis Alcançados',
                    data: <?= json_encode(array_column($dimensoesResumo, 'vuln_alcancados')) ?>,
                    backgroundColor: '#00b894'
                },
                {
                    label: 'Lacuna (Não Atendidos)',
                    data: <?= json_encode(array_column($dimensoesResumo, 'vuln_lacuna')) ?>,
                    backgroundColor: '#e74c3c'
                }
            ]
        },
        options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } } }
    });

    // 2. Gráfico Zona Rural vs Urbana
    new Chart(document.getElementById('chartZona'), {
        type: 'bar',
        data: {
            labels: ['Zona Rural', 'Zona Urbana'],
            datasets: [
                {
                    label: 'Alcançados (Aprovados)',
                    data: [<?= $geralInscricoes['rural_alcancados'] ?>, <?= $geralInscricoes['urbana_alcancados'] ?>],
                    backgroundColor: '#005b96'
                },
                {
                    label: 'Lacuna (Não Atendidos)',
                    data: [<?= $geralInscricoes['rural_lacuna'] ?>, <?= $geralInscricoes['urbana_lacuna'] ?>],
                    backgroundColor: '#fab1a0'
                }
            ]
        },
        options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } } }
    });

    // 3. Gráfico Faixas IPEA
    new Chart(document.getElementById('chartFaixas'), {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_column($faixasIpea, 'label')) ?>,
            datasets: [
                {
                    label: 'Alcançados',
                    data: <?= json_encode(array_column($faixasIpea, 'alcancados')) ?>,
                    backgroundColor: '#00b894'
                },
                {
                    label: 'Lacuna',
                    data: <?= json_encode(array_column($faixasIpea, 'lacuna')) ?>,
                    backgroundColor: '#e74c3c'
                }
            ]
        },
        options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } } }
    });

    // 4. Radar de Justiça
    new Chart(document.getElementById('chartRadarJustica'), {
        type: 'radar',
        data: {
            labels: ['IVS Geral', 'Infraestrutura Urbana', 'Renda e Trabalho', 'Capital Humano'],
            datasets: [
                {
                    label: 'Média Aprovados (Alcançados)',
                    data: [
                        <?= $geralInscricoes['media_geral_alcancados'] ?>,
                        <?= $geralInscricoes['media_infra_alcancados'] ?>,
                        <?= $geralInscricoes['media_renda_alcancados'] ?>,
                        <?= $geralInscricoes['media_capital_alcancados'] ?>
                    ],
                    backgroundColor: 'rgba(0, 184, 148, 0.25)',
                    borderColor: '#00b894',
                    pointBackgroundColor: '#00b894'
                },
                {
                    label: 'Média Lacuna (Não Atendidos)',
                    data: [
                        <?= $geralInscricoes['media_geral_lacuna'] ?>,
                        <?= $geralInscricoes['media_infra_lacuna'] ?>,
                        <?= $geralInscricoes['media_renda_lacuna'] ?>,
                        <?= $geralInscricoes['media_capital_lacuna'] ?>
                    ],
                    backgroundColor: 'rgba(231, 76, 60, 0.2)',
                    borderColor: '#e74c3c',
                    pointBackgroundColor: '#e74c3c'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: { r: { min: 0, max: 1 } }
        }
    });

    // 5. Gráfico Alcance por Curso
    const cursoLabels = <?= json_encode(array_column($porCurso, 'label')) ?>;
    const cursoRates = <?= json_encode(array_column($porCurso, 'taxa_alcance_vuln')) ?>;
    if (cursoLabels.length > 0) {
        new Chart(document.getElementById('chartCursos'), {
            type: 'bar',
            data: {
                labels: cursoLabels,
                datasets: [{
                    label: 'Alcance de Vulneráveis (%)',
                    data: cursoRates,
                    backgroundColor: '#0370b5'
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                scales: { x: { min: 0, max: 100 } }
            }
        });
    }
});
</script>

<?php require_once __DIR__ . '/../layouts/admin_footer.php'; ?>

