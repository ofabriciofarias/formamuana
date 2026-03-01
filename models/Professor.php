<?php

class Professor {
    private $id;
    private $usuarios_id;
    private $oferta_id;

    public function getId() {
        return $this->id;
    }

    public function getUsuariosId() {
        return $this->usuarios_id;
    }

    public function setUsuariosId($usuarios_id) {
        $this->usuarios_id = $usuarios_id;
    }

    public function getOfertaId() {
        return $this->oferta_id;
    }

    public function setOfertaId($oferta_id) {
        $this->oferta_id = $oferta_id;
    }
}
