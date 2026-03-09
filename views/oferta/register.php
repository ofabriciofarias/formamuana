<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'views/layouts/admin_header.php';

$errors = $_SESSION['errors'] ?? [];
$old_data = $_SESSION['old_data'] ?? [];
unset($_SESSION['errors'], $_SESSION['old_data']);
?>

<div class="container">
    <h2>Cadastro de Oferta</h2>

    <form action="index.php?action=store-oferta" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="nome">Nome:</label>
            <input type="text" name="nome" id="nome" class="form-control" required value="<?= htmlspecialchars($old_data['nome'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="descricao">Descrição:</label>
            <textarea name="descricao" id="descricao" class="form-control" required><?= htmlspecialchars($old_data['descricao'] ?? '') ?></textarea>
        </div>
        <div class="form-group">
            <label for="eixo_tecnologico">Eixo Tecnológico:</label>
            <input type="text" name="eixo_tecnologico" id="eixo_tecnologico" class="form-control" required value="<?= htmlspecialchars($old_data['eixo_tecnologico'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="foto">Foto:</label>
            <input type="file" name="foto" id="foto" class="form-control">
        </div>
        <div class="form-group">
            <label for="inicio_insc">Início das Inscrições:</label>
            <input type="date" name="inicio_insc" id="inicio_insc" class="form-control" required value="<?= htmlspecialchars($old_data['inicio_insc'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="final_insc">Final das Inscrições:</label>
            <input type="date" name="final_insc" id="final_insc" class="form-control" required value="<?= htmlspecialchars($old_data['final_insc'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="status_oferta">Status da Oferta:</label>
            <select name="status_oferta" id="status_oferta" class="form-control" required>
                <option value="Em Inscrição" <?= (isset($old_data['status_oferta']) && $old_data['status_oferta'] == 'Em Inscrição') ? 'selected' : '' ?>>Em Inscrição</option>
                <option value="Em Andamento" <?= (isset($old_data['status_oferta']) && $old_data['status_oferta'] == 'Em Andamento') ? 'selected' : '' ?>>Em Andamento</option>
                <option value="Concluída" <?= (isset($old_data['status_oferta']) && $old_data['status_oferta'] == 'Concluída') ? 'selected' : '' ?>>Concluída</option>
            </select>
        </div>
        <div class="form-group">
            <label for="usuarios_id">Usuário Responsável:</label>
            <select name="usuarios_id" id="usuarios_id" class="form-control" required>
                <?php foreach ($users as $user): ?>
                    <option value="<?php echo $user['id']; ?>" <?= (isset($old_data['usuarios_id']) && $old_data['usuarios_id'] == $user['id']) ? 'selected' : '' ?>><?php echo $user['nome']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="local">Local:</label>
            <input type="text" name="local" id="local" class="form-control" required value="<?= htmlspecialchars($old_data['local'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="turno">Turno:</label>
            <input type="text" name="turno" id="turno" class="form-control" required value="<?= htmlspecialchars($old_data['turno'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="numero_vagas">Número de Vagas:</label>
            <input type="number" name="numero_vagas" id="numero_vagas" class="form-control" required value="<?= htmlspecialchars($old_data['numero_vagas'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="publico_alvo">Público Alvo:</label>
            <input type="text" name="publico_alvo" id="publico_alvo" class="form-control" required value="<?= htmlspecialchars($old_data['publico_alvo'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="modalidade">Modalidade:</label>
            <input type="text" name="modalidade" id="modalidade" class="form-control" required value="<?= htmlspecialchars($old_data['modalidade'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="carga_horaria">Carga Horária:</label>
            <input type="number" name="carga_horaria" id="carga_horaria" class="form-control" required value="<?= htmlspecialchars($old_data['carga_horaria'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="inicio_aulas">Início das Aulas:</label>
            <input type="date" name="inicio_aulas" id="inicio_aulas" class="form-control" value="<?= htmlspecialchars($old_data['inicio_aulas'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="fim_aulas">Fim das Aulas:</label>
            <input type="date" name="fim_aulas" id="fim_aulas" class="form-control" value="<?= htmlspecialchars($old_data['fim_aulas'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="inicio_hora_aula">Horário de Início da Aula:</label>
            <input type="time" name="inicio_hora_aula" id="inicio_hora_aula" class="form-control" value="<?= htmlspecialchars($old_data['inicio_hora_aula'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="fim_hora_aula">Horário de Fim da Aula:</label>
            <input type="time" name="fim_hora_aula" id="fim_hora_aula" class="form-control" value="<?= htmlspecialchars($old_data['fim_hora_aula'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="upload_relatorio">Relatório:</label>
            <input type="file" name="upload_relatorio" id="upload_relatorio" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Cadastrar</button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form[action="index.php?action=store-oferta"]');
    const inicioAulas = document.getElementById('inicio_aulas');
    const fimAulas = document.getElementById('fim_aulas');
    const inicioHora = document.getElementById('inicio_hora_aula');
    const fimHora = document.getElementById('fim_hora_aula');

    form.addEventListener('submit', function(event) {
        // Validar datas: A data de fim deve ser maior que a de início
        if (inicioAulas.value && fimAulas.value && fimAulas.value <= inicioAulas.value) {
            alert('A data de fim das aulas deve ser posterior à data de início.');
            event.preventDefault(); // Impede o envio do formulário
            return;
        }

        // Validar horários: O horário de fim deve ser maior que o de início
        if (inicioHora.value && fimHora.value && fimHora.value <= inicioHora.value) {
            alert('O horário de fim da aula deve ser posterior ao horário de início.');
            event.preventDefault(); // Impede o envio do formulário
        }
    });
});
</script>

<?php
require_once 'views/layouts/admin_footer.php';
?>
