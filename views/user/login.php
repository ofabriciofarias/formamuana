<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="login-container">
    <h2>Login</h2>
    
    <?php if(isset($_GET['error'])): ?>
        <p class="error-message">Login ou senha inválidos.</p>
    <?php endif; ?>

    <form action="index.php?action=do_login" method="POST">
        <div class="form-group">
            <label for="login">Login</label>
            <input type="text" id="login" name="login" required>
        </div>
        <div class="form-group">
            <label for="senha">Senha</label>
            <input type="password" id="senha" name="senha" required>
        </div>
        <button type="submit" class="btn">Entrar</button>
    </form>
    <div class="register-link">
        <p>Não tem uma conta? <a href="index.php?action=register">Cadastre-se</a></p>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
