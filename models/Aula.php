<?php

class Aula {
    private $id;
    private $oferta_id;
    private $data_aula;
    private $conteudo_ministrado;
    private $carga_horaria_aula;

    public function getId() {
        return $this->id;
    }

    public function getOfertaId() {
        return $this->oferta_id;
    }

    public function setOfertaId($oferta_id) {
        $this->oferta_id = $oferta_id;
    }

    public function getDataAula() {
        return $this->data_aula;
    }

    public function setDataAula($data_aula) {
        $this->data_aula = $data_aula;
    }

    public function getConteudoMinistrado() {
        return $this->conteudo_ministrado;
    }

    public function setConteudoMinistrado($conteudo_ministrado) {
        $this->conteudo_ministrado = $conteudo_ministrado;
    }

    public function getCargaHorariaAula() {
        return $this->carga_horaria_aula;
    }

    public function setCargaHorariaAula($carga_horaria_aula) {
        $this->carga_horaria_aula = $carga_horaria_aula;
    }
}
