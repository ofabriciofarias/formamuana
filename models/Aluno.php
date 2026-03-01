<?php

class Aluno {
    private $id;
    private $nome;
    private $whatsapp;
    private $telefone;
    private $email;
    private $data_nascimento;
    private $sexo;
    private $identidade_genero;
    private $possui_nome_social;
    private $nome_social;
    private $possui_deficiencia;
    private $tipo_deficiencia;
    private $cor_raca;
    private $comunidade_tradicional;
    private $zona_residencia;
    private $cidade_origem;
    private $cidade_atual;
    private $bairro_comunidade;
    private $estado_civil;
    private $possui_filhos;
    private $quantidade_filhos;
    private $tipo_escola_ensino_medio;
    private $nivel_escolaridade;
    private $beneficiario_programa_social;
    private $acesso_internet;
    private $dispositivo_principal;

    public function getId() {
        return $this->id;
    }

    public function getNome() {
        return $this->nome;
    }

    public function setNome($nome) {
        $this->nome = $nome;
    }

    public function getWhatsapp() {
        return $this->whatsapp;
    }

    public function setWhatsapp($whatsapp) {
        $this->whatsapp = $whatsapp;
    }

    public function getTelefone() {
        return $this->telefone;
    }

    public function setTelefone($telefone) {
        $this->telefone = $telefone;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function getDataNascimento() {
        return $this->data_nascimento;
    }

    public function setDataNascimento($data_nascimento) {
        $this->data_nascimento = $data_nascimento;
    }

    public function getSexo() {
        return $this->sexo;
    }

    public function setSexo($sexo) {
        $this->sexo = $sexo;
    }

    public function getIdentidadeGenero() {
        return $this->identidade_genero;
    }

    public function setIdentidadeGenero($identidade_genero) {
        $this->identidade_genero = $identidade_genero;
    }

    public function getPossuiNomeSocial() {
        return $this->possui_nome_social;
    }

    public function setPossuiNomeSocial($possui_nome_social) {
        $this->possui_nome_social = $possui_nome_social;
    }

    public function getNomeSocial() {
        return $this->nome_social;
    }

    public function setNomeSocial($nome_social) {
        $this->nome_social = $nome_social;
    }

    public function getPossuiDeficiencia() {
        return $this->possui_deficiencia;
    }

    public function setPossuiDeficiencia($possui_deficiencia) {
        $this->possui_deficiencia = $possui_deficiencia;
    }

    public function getTipoDeficiencia() {
        return $this->tipo_deficiencia;
    }

    public function setTipoDeficiencia($tipo_deficiencia) {
        $this->tipo_deficiencia = $tipo_deficiencia;
    }

    public function getCorRaca() {
        return $this->cor_raca;
    }

    public function setCorRaca($cor_raca) {
        $this->cor_raca = $cor_raca;
    }

    public function getComunidadeTradicional() {
        return $this->comunidade_tradicional;
    }

    public function setComunidadeTradicional($comunidade_tradicional) {
        $this->comunidade_tradicional = $comunidade_tradicional;
    }

    public function getZonaResidencia() {
        return $this->zona_residencia;
    }

    public function setZonaResidencia($zona_residencia) {
        $this->zona_residencia = $zona_residencia;
    }

    public function getCidadeOrigem() {
        return $this->cidade_origem;
    }

    public function setCidadeOrigem($cidade_origem) {
        $this->cidade_origem = $cidade_origem;
    }

    public function getCidadeAtual() {
        return $this->cidade_atual;
    }

    public function setCidadeAtual($cidade_atual) {
        $this->cidade_atual = $cidade_atual;
    }

    public function getBairroComunidade() {
        return $this->bairro_comunidade;
    }

    public function setBairroComunidade($bairro_comunidade) {
        $this->bairro_comunidade = $bairro_comunidade;
    }

    public function getEstadoCivil() {
        return $this->estado_civil;
    }

    public function setEstadoCivil($estado_civil) {
        $this->estado_civil = $estado_civil;
    }

    public function getPossuiFilhos() {
        return $this->possui_filhos;
    }

    public function setPossuiFilhos($possui_filhos) {
        $this->possui_filhos = $possui_filhos;
    }

    public function getQuantidadeFilhos() {
        return $this->quantidade_filhos;
    }

    public function setQuantidadeFilhos($quantidade_filhos) {
        $this->quantidade_filhos = $quantidade_filhos;
    }

    public function getTipoEscolaEnsinoMedio() {
        return $this->tipo_escola_ensino_medio;
    }

    public function setTipoEscolaEnsinoMedio($tipo_escola_ensino_medio) {
        $this->tipo_escola_ensino_medio = $tipo_escola_ensino_medio;
    }

    public function getNivelEscolaridade() {
        return $this->nivel_escolaridade;
    }

    public function setNivelEscolaridade($nivel_escolaridade) {
        $this->nivel_escolaridade = $nivel_escolaridade;
    }

    public function getBeneficiarioProgramaSocial() {
        return $this->beneficiario_programa_social;
    }

    public function setBeneficiarioProgramaSocial($beneficiario_programa_social) {
        $this->beneficiario_programa_social = $beneficiario_programa_social;
    }

    public function getAcessoInternet() {
        return $this->acesso_internet;
    }

    public function setAcessoInternet($acesso_internet) {
        $this->acesso_internet = $acesso_internet;
    }

    public function getDispositivoPrincipal() {
        return $this->dispositivo_principal;
    }

    public function setDispositivoPrincipal($dispositivo_principal) {
        $this->dispositivo_principal = $dispositivo_principal;
    }
}
