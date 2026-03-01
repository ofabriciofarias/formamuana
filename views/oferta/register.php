<?php
require_once 'views/layouts/header.php';
?>

<div class="container">
    <h2>Cadastro de Oferta</h2>
    <form action="index.php?action=store-oferta" method="POST">
        <div class="form-group">
            <label for="nome">Nome:</label>
            <input type="text" name="nome" id="nome" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="descricao">Descrição:</label>
            <textarea name="descricao" id="descricao" class="form-control" required></textarea>
        </div>
        <div class="form-group">
            <label for="preco">Preço:</label>
            <input type="text" name="preco" id="preco" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="vagas">Vagas:</label>
            <input type="number" name="vagas" id="vagas" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Cadastrar</button>
    </form>
</div>

<?php
require_once 'views/layouts/footer.php';
?>
