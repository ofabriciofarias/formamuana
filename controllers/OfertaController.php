<?php

require_once 'models/Oferta.php';

class OfertaController {
    public function create() {
        require_once 'views/oferta/register.php';
    }

    public function store() {
        $oferta = new Oferta();
        $oferta->setNome($_POST['nome']);
        $oferta->setDescricao($_POST['descricao']);
        $oferta->setPreco($_POST['preco']);
        $oferta->setVagas($_POST['vagas']);

        if ($oferta->save()) {
            header('Location: index.php?action=registro-sucesso');
        } else {
            echo "Erro ao cadastrar a oferta.";
        }
    }
}
