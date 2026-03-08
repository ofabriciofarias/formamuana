<?php

class Oferta {
    private $id;
    private $nome;
    private $descricao;
    private $eixo_tecnologico;
    private $foto;
    private $inicio_insc;
    private $final_insc;
    private $status_oferta;
    private $usuarios_id;
    private $local;
    private $turno;
    private $numero_vagas;
    private $publico_alvo;
    private $modalidade;
    private $carga_horaria;
    private $upload_relatorio;

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

    public function getEixoTecnologico() {
        return $this->eixo_tecnologico;
    }

    public function setEixoTecnologico($eixo_tecnologico) {
        $this->eixo_tecnologico = $eixo_tecnologico;
    }

    public function getFoto() {
        return $this->foto;
    }

    public function setFoto($foto) {
        $this->foto = $foto;
    }

    public function getInicioInsc() {
        return $this->inicio_insc;
    }

    public function setInicioInsc($inicio_insc) {
        $this->inicio_insc = $inicio_insc;
    }

    public function getFinalInsc() {
        return $this->final_insc;
    }

    public function setFinalInsc($final_insc) {
        $this->final_insc = $final_insc;
    }

    public function getStatusOferta() {
        return $this->status_oferta;
    }

    public function setStatusOferta($status_oferta) {
        $this->status_oferta = $status_oferta;
    }

    public function getUsuariosId() {
        return $this->usuarios_id;
    }

    public function setUsuariosId($usuarios_id) {
        $this->usuarios_id = $usuarios_id;
    }

    public function getLocal() {
        return $this->local;
    }

    public function setLocal($local) {
        $this->local = $local;
    }

    public function getTurno() {
        return $this->turno;
    }

    public function setTurno($turno) {
        $this->turno = $turno;
    }

    public function getNumeroVagas() {
        return $this->numero_vagas;
    }

    public function setNumeroVagas($numero_vagas) {
        $this->numero_vagas = $numero_vagas;
    }

    public function getPublicoAlvo() {
        return $this->publico_alvo;
    }

    public function setPublicoAlvo($publico_alvo) {
        $this->publico_alvo = $publico_alvo;
    }

    public function getModalidade() {
        return $this->modalidade;
    }

    public function setModalidade($modalidade) {
        $this->modalidade = $modalidade;
    }

    public function getCargaHoraria() {
        return $this->carga_horaria;
    }

    public function setCargaHoraria($carga_horaria) {
        $this->carga_horaria = $carga_horaria;
    }

    public function getUploadRelatorio() {
        return $this->upload_relatorio;
    }

    public function setUploadRelatorio($upload_relatorio) {
        $this->upload_relatorio = $upload_relatorio;
    }

    public function save() {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        $sql = "INSERT INTO oferta (nome, descricao, eixo_tecnologico, foto, inicio_insc, final_insc, status_oferta, usuarios_id, local, turno, numero_vagas, publico_alvo, modalidade, carga_horaria, upload_relatorio) 
                VALUES (:nome, :descricao, :eixo_tecnologico, :foto, :inicio_insc, :final_insc, :status_oferta, :usuarios_id, :local, :turno, :numero_vagas, :publico_alvo, :modalidade, :carga_horaria, :upload_relatorio)";
        
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':nome', $this->nome);
        $stmt->bindParam(':descricao', $this->descricao);
        $stmt->bindParam(':eixo_tecnologico', $this->eixo_tecnologico);
        $stmt->bindParam(':foto', $this->foto);
        $stmt->bindParam(':inicio_insc', $this->inicio_insc);
        $stmt->bindParam(':final_insc', $this->final_insc);
        $stmt->bindParam(':status_oferta', $this->status_oferta);
        $stmt->bindParam(':usuarios_id', $this->usuarios_id);
        $stmt->bindParam(':local', $this->local);
        $stmt->bindParam(':turno', $this->turno);
        $stmt->bindParam(':numero_vagas', $this->numero_vagas);
        $stmt->bindParam(':publico_alvo', $this->publico_alvo);
        $stmt->bindParam(':modalidade', $this->modalidade);
        $stmt->bindParam(':carga_horaria', $this->carga_horaria);
        $stmt->bindParam(':upload_relatorio', $this->upload_relatorio);

        return $stmt->execute();
    }

    public static function getAllUsers() {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        $sql = "SELECT id, nome FROM usuarios";
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function findAll() {
        $db = Database::getInstance()->getConnection();
        try {
            $stmt = $db->prepare("SELECT * FROM oferta ORDER BY inicio_insc DESC");
            $stmt->execute();
            
            // Retorna um array de objetos da classe Oferta
            return $stmt->fetchAll(PDO::FETCH_CLASS, 'Oferta');
        } catch (PDOException $e) {
            error_log("Erro ao buscar todas as ofertas: " . $e->getMessage());
            return [];
        }
    }

    public static function findById($id) {
        $db = Database::getInstance()->getConnection();
        try {
            $stmt = $db->prepare("SELECT * FROM oferta WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            // Retorna um objeto da classe Oferta
            return $stmt->fetchObject('Oferta');
        } catch (PDOException $e) {
            error_log("Erro ao buscar a oferta por ID: " . $e->getMessage());
            return null;
        }
    }
}
