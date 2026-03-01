<?php

class ListaFrequencia {
    private $id;
    private $aula_id;
    private $alunos_id;
    private $presenca;
    private $justificativa_falta;

    public function getId() {
        return $this->id;
    }

    public function getAulaId() {
        return $this->aula_id;
    }

    public function setAulaId($aula_id) {
        $this->aula_id = $aula_id;
    }

    public function getAlunosId() {
        return $this->alunos_id;
    }

    public function setAlunosId($alunos_id) {
        $this->alunos_id = $alunos_id;
    }

    public function getPresenca() {
        return $this->presenca;
    }

    public function setPresenca($presenca) {
        $this->presenca = $presenca;
    }

    public function getJustificativaFalta() {
        return $this->justificativa_falta;
    }

    public function setJustificativaFalta($justificativa_falta) {
        $this->justificativa_falta = $justificativa_falta;
    }
}
