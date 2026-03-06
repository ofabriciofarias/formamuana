<?php
require_once __DIR__ . '/../layouts/admin_header.php';
require_once __DIR__ . '/../../models/Oferta.php';
require_once __DIR__ . '/../../models/AlunoOferta.php';

// Busca todas as ofertas para o dropdown
$ofertas = Oferta::findAll();
$ofertaId = $_GET['oferta_id'] ?? null;
$inscritos = [];
if ($ofertaId) {
    $inscritos = AlunoOferta::getRelatorio($ofertaId, null);
}

?>

<div class="container-fluid">
    <h1 class="mt-4">Seleção de Alunos</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="index.php?action=dashboard">Dashboard</a></li>
        <li class="breadcrumb-item active">Seleção de Alunos</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table mr-1"></i>
            Filtros
        </div>
        <div class="card-body">
            <form id="filtroForm" method="GET">
                <input type="hidden" name="action" value="selecao_alunos">
                <div class="form-row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="oferta_id">Selecionar Oferta</label>
                            <select name="oferta_id" id="oferta_id" class="form-control" onchange="this.form.submit()">
                                <option value="">Selecione uma oferta</option>
                                <?php foreach ($ofertas as $oferta) : ?>
                                    <option value="<?= $oferta->getId() ?>" <?= ($ofertaId == $oferta->getId()) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($oferta->getNome()) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="busca">Buscar por Nome</label>
                            <input type="text" name="busca" id="busca" class="form-control" placeholder="Nome do aluno..." value="<?= $_GET['busca'] ?? '' ?>">
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php if ($ofertaId) : ?>
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-users mr-1"></i>
                Alunos Inscritos
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Nome do Aluno</th>
                                <th>Email</th>
                                <th>WhatsApp</th>
                                <th>Oferta</th>
                                <th>Status da Seleção</th>
                                <!-- Adicione mais colunas conforme necessário -->
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($inscritos)) : ?>
                                <?php foreach ($inscritos as $inscrito) : ?>
                                    <tr>
                                        <td><?= htmlspecialchars($inscrito['nome_aluno']) ?></td>
                                        <td><?= htmlspecialchars($inscrito['email']) ?></td>
                                        <td><?= htmlspecialchars($inscrito['whatsapp']) ?></td>
                                        <td><?= htmlspecialchars($inscrito['nome_oferta']) ?></td>
                                        <td>
                                            <select class="form-control status-selecao" data-aluno-id="<?= $inscrito['alunos_id'] ?>" data-oferta-id="<?= $inscrito['oferta_id'] ?>">
                                                <option value="pendente" <?= ($inscrito['status_selecao'] == 'pendente') ? 'selected' : '' ?>>Pendente</option>
                                                <option value="aceito" <?= ($inscrito['status_selecao'] == 'aceito') ? 'selected' : '' ?>>Aceito</option>
                                            </select>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="5" class="text-center">Nenhum aluno inscrito para esta oferta.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const buscaInput = document.getElementById('busca');
        const tableRows = document.querySelectorAll('#dataTable tbody tr');

        buscaInput.addEventListener('keyup', function() {
            const buscaTermo = this.value.toLowerCase();

            tableRows.forEach(row => {
                const nomeAluno = row.querySelector('td').textContent.toLowerCase();
                if (nomeAluno.includes(buscaTermo)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        document.querySelectorAll('.status-selecao').forEach(select => {
            select.addEventListener('change', function() {
                const alunoId = this.dataset.alunoId;
                const ofertaId = this.dataset.ofertaId;
                const status = this.value;

                fetch('index.php?action=updateStatusAjax', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            aluno_id: alunoId,
                            oferta_id: ofertaId,
                            status: status
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (!data.success) {
                            alert('Falha ao atualizar o status.');
                            // Reverter a seleção em caso de falha
                            this.value = this.querySelector('option[selected]').value;
                        } else {
                            // Opcional: mostrar uma notificação de sucesso
                            console.log('Status atualizado com sucesso!');
                        }
                    })
                    .catch(error => {
                        console.error('Erro:', error);
                        alert('Ocorreu um erro na requisição.');
                    });
            });
        });
    });
</script>

<?php
require_once __DIR__ . '/../layouts/admin_footer.php';
?>
