<?php

class Usuario {
    private $id;
    private $nome;
    private $login;
    private $senha;
    private $email;
    private $telefone;
    private $tipo_usuario_id;
    private $data_cadastro;
    private $termo_aceite;
    private $upload_rg;
    private $upload_cpf;
    private $upload_plano;
    private $upload_pis_ou_nis;
    private $upload_residencia;
    private $upload_cart_trab;
    private $upload_declaracao_vinculo_matr;

    public function getId() {
        return $this->id;
    }

    public function getNome() {
        return $this->nome;
    }

    public function setNome($nome) {
        $this->nome = $nome;
    }

    public function getLogin() {
        return $this->login;
    }

    public function setLogin($login) {
        $this->login = $login;
    }

    public function getSenha() {
        return $this->senha;
    }

    public function setSenha($senha) {
        $this->senha = $senha;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function getTelefone() {
        return $this->telefone;
    }

    public function setTelefone($telefone) {
        $this->telefone = $telefone;
    }

    public function getTipoUsuarioId() {
        return $this->tipo_usuario_id;
    }

    public function setTipoUsuarioId($tipo_usuario_id) {
        $this->tipo_usuario_id = $tipo_usuario_id;
    }

    public function getDataCadastro() {
        return $this->data_cadastro;
    }

    public function getTermoAceite() {
        return $this->termo_aceite;
    }

    public function setTermoAceite($termo_aceite) {
        $this->termo_aceite = $termo_aceite;
    }

    public function getUploadRg() {
        return $this->upload_rg;
    }

    public function setUploadRg($upload_rg) {
        $this->upload_rg = $upload_rg;
    }

    public function getUploadCpf() {
        return $this->upload_cpf;
    }

    public function setUploadCpf($upload_cpf) {
        $this->upload_cpf = $upload_cpf;
    }

    public function getUploadPlano() {
        return $this->upload_plano;
    }

    public function setUploadPlano($upload_plano) {
        $this->upload_plano = $upload_plano;
    }

    public function getUploadPisOuNis() {
        return $this->upload_pis_ou_nis;
    }

    public function setUploadPisOuNis($upload_pis_ou_nis) {
        $this->upload_pis_ou_nis = $upload_pis_ou_nis;
    }

    public function getUploadResidencia() {
        return $this->upload_residencia;
    }

    public function setUploadResidencia($upload_residencia) {
        $this->upload_residencia = $upload_residencia;
    }

    public function getUploadCartTrab() {
        return $this->upload_cart_trab;
    }

    public function setUploadCartTrab($upload_cart_trab) {
        $this->upload_cart_trab = $upload_cart_trab;
    }

    public function getUploadDeclaracaoVinculoMatr() {
        return $this->upload_declaracao_vinculo_matr;
    }

    public function setUploadDeclaracaoVinculoMatr($upload_declaracao_vinculo_matr) {
        $this->upload_declaracao_vinculo_matr = $upload_declaracao_vinculo_matr;
    }
}
