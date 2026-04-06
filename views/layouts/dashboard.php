<div class="container">
    <h2>Minhas Turmas</h2>

    <?php if (empty($turmas)): ?>
        <div style="text-align: center; padding: 50px 0; border: 1px dashed #ccc; border-radius: 8px; background-color: #f9f9f9;">
            <p style="font-size: 1.1rem; color: #666;">Você ainda não tem turmas vinculadas.</p>
            <p style="color: #888;">Entre em contato com o administrador para ser vinculado a uma turma.</p>
        </div>
    <?php else: ?>
        <p>Selecione uma turma abaixo para lançar ou consultar a frequência dos alunos.</p>
        
        <div class="turmas-grid">
            <?php foreach ($turmas as $turma): ?>
                <div class="turma-card-professor">
                    <h3><?= htmlspecialchars($turma['nome']) ?></h3>
                    <p><strong>Período:</strong> <?= htmlspecialchars(date('d/m/Y', strtotime($turma['inicio_aulas']))) ?> a <?= htmlspecialchars(date('d/m/Y', strtotime($turma['fim_aulas']))) ?></p>
                    <p><strong>Local:</strong> <?= htmlspecialchars($turma['local']) ?></p>
                    <p><strong>Turno:</strong> <?= htmlspecialchars($turma['turno']) ?></p>
                    <div class="turma-actions">
                        <a href="index.php?action=lancar_frequencia&oferta_id=<?= $turma['id'] ?>" class="btn btn-primary" style="width: 100%; text-align:center; margin-bottom: 10px;">Lançar Frequência</a>
                        <a href="index.php?action=consultar_frequencia&oferta_id=<?= $turma['id'] ?>" class="btn btn-success" style="width: 100%; text-align:center;">Consultar Frequência</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
.turmas-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 25px;
    margin-top: 30px;
}
.turma-card-professor {
    border: 1px solid #e0e0e0;
    border-radius: 10px;
    padding: 25px;
    box-shadow: var(--shadow);
    background-color: var(--white);
}
</style>