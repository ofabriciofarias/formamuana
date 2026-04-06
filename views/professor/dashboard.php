<div class="container mt-4">
    <h2>Minhas Turmas</h2>
    <?php if (isset($turmas) && !empty($turmas)): ?>
        <div class="list-group">
            <?php foreach ($turmas as $turma): ?>
                <div class="list-group-item list-group-item-action flex-column align-items-start">
                    <div class="d-flex w-100 justify-content-between">
                        <h5 class="mb-1"><?= htmlspecialchars($turma['nome']) ?></h5>
                        <small>ID da Oferta: <?= $turma['id'] ?></small>
                    </div>
                    <p class="mb-1"><?= htmlspecialchars($turma['descricao']) ?></p>
                    <div class="mt-2">
                        <a href="index.php?action=lancar_frequencia&oferta_id=<?= $turma['id'] ?>" class="btn btn-primary btn-sm">Lançar Frequência</a>
                        <a href="index.php?action=consultar_frequencia&oferta_id=<?= $turma['id'] ?>" class="btn btn-secondary btn-sm">Consultar Frequência</a>
                        <a href="index.php?action=notas&oferta_id=<?= $turma['id'] ?>" class="btn btn-info btn-sm">Cadastrar Nota</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>Nenhuma turma vinculada a você no momento.</p>
    <?php endif; ?>
</div>
