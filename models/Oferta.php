<?php

class Oferta {
    private $id;
    private $nome;
    private $descricao;
    private $preco;
    private $vagas;

    public function getId() {
        return $this->id;
    }

    public function getNome() {
        return $this->nome;
    }

    public function setNome($nome) {
        $this->nome = $nome;
    }

    public function getDescricao() {
        return $this->descricao;
    }

    public function setDescricao($descricao) {
        $this->descricao = $descricao;
    }

    public function getPreco() {
        return $this->preco;
    }

    public function setPreco($preco) {
        $this->preco = $preco;
    }

    public function getVagas() {
        return $this->vagas;
    }

    public function setVagas($vagas) {
        $this->vagas = $vagas;
    }

    public function save() {
        $db = new Database();
        $conn = $db->getConnection();

        $sql = "INSERT INTO ofertas (nome, descricao, preco, vagas) VALUES (:nome, :descricao, :preco, :vagas)";
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':nome', $this->nome);
        $stmt->bindParam(':descricao', $this->descricao);
        $stmt->bindParam(':preco', $this->preco);
        $stmt->bindParam(':vagas', $this->vagas);

        return $stmt->execute();
    }
}
