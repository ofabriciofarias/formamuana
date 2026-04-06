<h2>Cadastro de Usuário</h2>

<?php
// Inicia a sessão se ainda não foi iniciada para poder acessar as flash messages
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Exibe mensagens de erro ou sucesso, se houver
if (isset($_SESSION['flash_error'])): ?>
    <div class="alert-info" style="background-color: #f8d7da; color: #721c24; border-color: #f5c6cb; padding: 1rem; margin-bottom: 1rem; border-radius: .25rem;"><?= $_SESSION['flash_error'] ?></div>
    <?php unset($_SESSION['flash_error']); ?>
<?php endif; ?>
<?php if (isset($_SESSION['flash_success'])): ?>
    <div class="alert-info" style="background-color: #d4edda; color: #155724; border-color: #c3e6cb; padding: 1rem; margin-bottom: 1rem; border-radius: .25rem;"><?= $_SESSION['flash_success'] ?></div>
    <?php unset($_SESSION['flash_success']); ?>
<?php endif; ?>
<form action="index.php?action=register" method="POST" enctype="multipart/form-data">
    <div class="form-group">
        <label for="nome">Nome Completo</label>
        <input type="text" id="nome" name="nome" required>
    </div>
    <div class="form-group">
        <label for="login">Login</label>
        <input type="text" id="login" name="login" required>
    </div>
    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required>
    </div>
    <div class="form-group">
        <label for="senha">Senha</label>
        <input type="password" id="senha" name="senha" required>
    </div>
    <div class="form-group">
        <label for="confirmar_senha">Confirmar Senha</label>
        <input type="password" id="confirmar_senha" name="confirmar_senha" required>
    </div>
    <div class="form-group">
        <label for="telefone">Telefone</label>
        <input type="text" id="telefone" name="telefone">
    </div>
    <div class="form-group">
        <label for="tipo_usuario_id">Tipo de Usuário</label>
        <?php 
        // Verifica se é um cadastro público (não é admin e só tem um tipo de usuário, que é Aluno)
        $is_public_aluno_registration = (isset($tipos_usuario) && count($tipos_usuario) === 1 && !is_admin());
        ?>

        <?php if ($is_public_aluno_registration): ?>
            <?php $aluno_tipo = $tipos_usuario[0]; ?>
            <input type="text" value="<?= htmlspecialchars($aluno_tipo['nome']) ?>" disabled>
            <input type="hidden" name="tipo_usuario_id" value="<?= htmlspecialchars($aluno_tipo['id']) ?>">
        <?php else: ?>
            <select id="tipo_usuario_id" name="tipo_usuario_id" required>
                <option value="">Selecione...</option>
                <?php if (isset($tipos_usuario) && !empty($tipos_usuario)): ?>
                    <?php foreach ($tipos_usuario as $tipo): ?>
                        <option value="<?= htmlspecialchars($tipo['id']) ?>"><?= htmlspecialchars($tipo['nome']) ?></option>
                    <?php endforeach; ?>
                <?php else: ?>
                    <option value="" disabled>Não foi possível carregar os tipos de usuário</option>
                <?php endif; ?>
            </select>
        <?php endif; ?>
    </div>

    <?php if (is_admin()): ?>
    <fieldset>
        <legend>Documentos para Upload</legend>
        <div class="form-group">
            <label for="upload_rg">RG</label>
            <input type="file" id="upload_rg" name="upload_rg">
        </div>
        <div class="form-group">
            <label for="upload_cpf">CPF</label>
            <input type="file" id="upload_cpf" name="upload_cpf">
        </div>
        <div class="form-group">
            <label for="upload_plano">Plano</label>
            <input type="file" id="upload_plano" name="upload_plano">
        </div>
        <div class="form-group">
            <label for="upload_pis_ou_nis">PIS ou NIS</label>
            <input type="file" id="upload_pis_ou_nis" name="upload_pis_ou_nis">
        </div>
        <div class="form-group">
            <label for="upload_residencia">Comprovante de Residência</label>
            <input type="file" id="upload_residencia" name="upload_residencia">
        </div>
        <div class="form-group">
            <label for="upload_cart_trab">Carteira de Trabalho</label>
            <input type="file" id="upload_cart_trab" name="upload_cart_trab">
        </div>
        <div class="form-group">
            <label for="upload_declaracao_vinculo_matr">Declaração de Vínculo de Matrícula</label>
            <input type="file" id="upload_declaracao_vinculo_matr" name="upload_declaracao_vinculo_matr">
        </div>
    </fieldset>
    <?php endif; ?>

    <div class="form-group">
        <input type="checkbox" id="termo_aceite" name="termo_aceite" value="aceito" <?php if (!is_admin()) echo 'required'; ?>>
        <label for="termo_aceite">Eu li e aceito os termos de uso. (Obrigatório para cadastro público)</label>
    </div>

    <button type="submit" class="btn">Cadastrar</button>
</form>
