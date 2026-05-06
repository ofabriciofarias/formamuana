<?php

class AlunoOferta {
    private $alunos_id;
    private $oferta_id;
    private $status_selecao;
    private $data_insc;
    private $termo_aceite;
    private $exerce_atividade_remunerada;
    private $vinculo_atividade;
    private $afinidade_atividade_curso;
    private $participacao_economica_familiar;
    private $numero_membros_familia;
    private $renda_familiar;
    private $motivo_escolha_curso;
    private $o_que_mais_espera_do_curso;
    private $expectativas_relacao_curso;
    private $expectativas_profissionais_pos_curso;
    private $canal_informacao_curso;
    private $turno_preferencia;
    private $concluiu_curso;
    private $nota_final;
    private $url_certificado;
    private $motivo_evasao;
    

    public function getAlunosId() {
        return $this->alunos_id;
    }

    public function setAlunosId($alunos_id) {
        $this->alunos_id = $alunos_id;
    }

    public function getOfertaId() {
        return $this->oferta_id;
    }

    public function setOfertaId($oferta_id) {
        $this->oferta_id = $oferta_id;
    }

    public function getStatusSelecao() {
        return $this->status_selecao;
    }

    public function setStatusSelecao($status_selecao) {
        $this->status_selecao = $status_selecao;
    }

    public function getDataInsc() {
        return $this->data_insc;
    }

    public function getTermoAceite() {
        return $this->termo_aceite;
    }

    public function setTermoAceite($termo_aceite) {
        $this->termo_aceite = $termo_aceite;
    }

    public function getExerceAtividadeRemunerada() {
        return $this->exerce_atividade_remunerada;
    }

    public function setExerceAtividadeRemunerada($exerce_atividade_remunerada) {
        $this->exerce_atividade_remunerada = $exerce_atividade_remunerada;
    }

    public function getVinculoAtividade() {
        return $this->vinculo_atividade;
    }

    public function setVinculoAtividade($vinculo_atividade) {
        $this->vinculo_atividade = $vinculo_atividade;
    }

    public function getAfinidadeAtividadeCurso() {
        return $this->afinidade_atividade_curso;
    }

    public function setAfinidadeAtividadeCurso($afinidade_atividade_curso) {
        $this->afinidade_atividade_curso = $afinidade_atividade_curso;
    }

    public function getParticipacaoEconomicaFamiliar() {
        return $this->participacao_economica_familiar;
    }

    public function setParticipacaoEconomicaFamiliar($participacao_economica_familiar) {
        $this->participacao_economica_familiar = $participacao_economica_familiar;
    }

    public function getNumeroMembrosFamilia() {
        return $this->numero_membros_familia;
    }

    public function setNumeroMembrosFamilia($numero_membros_familia) {
        $this->numero_membros_familia = $numero_membros_familia;
    }

    public function getRendaFamiliar() {
        return $this->renda_familiar;
    }

    public function setRendaFamiliar($renda_familiar) {
        $this->renda_familiar = $renda_familiar;
    }

    public function getMotivoEscolhaCurso() {
        return $this->motivo_escolha_curso;
    }

    public function setMotivoEscolhaCurso($motivo_escolha_curso) {
        $this->motivo_escolha_curso = $motivo_escolha_curso;
    }

    public function getOQueMaisEsperaDoCurso() {
        return $this->o_que_mais_espera_do_curso;
    }

    public function setOQueMaisEsperaDoCurso($o_que_mais_espera_do_curso) {
        $this->o_que_mais_espera_do_curso = $o_que_mais_espera_do_curso;
    }

    public function getExpectativasRelacaoCurso() {
        return $this->expectativas_relacao_curso;
    }

    public function setExpectativasRelacaoCurso($expectativas_relacao_curso) {
        $this->expectativas_relacao_curso = $expectativas_relacao_curso;
    }

    public function getExpectativasProfissionaisPosCurso() {
        return $this->expectativas_profissionais_pos_curso;
    }

    public function setExpectativasProfissionaisPosCurso($expectativas_profissionais_pos_curso) {
        $this->expectativas_profissionais_pos_curso = $expectativas_profissionais_pos_curso;
    }

    public function getCanalInformacaoCurso() {
        return $this->canal_informacao_curso;
    }

    public function setCanalInformacaoCurso($canal_informacao_curso) {
        $this->canal_informacao_curso = $canal_informacao_curso;
    }

    public function getTurnoPreferencia() {
        return $this->turno_preferencia;
    }

    public function setTurnoPreferencia($turno_preferencia) {
        $this->turno_preferencia = $turno_preferencia;
    }

    public function getConcluiuCurso() {
        return $this->concluiu_curso;
    }

    public function setConcluiuCurso($concluiu_curso) {
        $this->concluiu_curso = $concluiu_curso;
    }

    public function getNotaFinal() {
        return $this->nota_final;
    }

    public function setNotaFinal($nota_final) {
        $this->nota_final = $nota_final;
    }

    public function getMotivoEvasao() {
        return $this->motivo_evasao;
    }

    public function setMotivoEvasao($motivo_evasao) {
        $this->motivo_evasao = $motivo_evasao;
    }

    public function getUrlCertificado() {
        return $this->url_certificado;
    }

    public function setUrlCertificado($url_certificado) {
        $this->url_certificado = $url_certificado;
    }

    public function save() {
        $db = Database::getInstance()->getConnection();

        $sql = "INSERT INTO alunos_has_oferta (
                    alunos_id, oferta_id, status_selecao, termo_aceite,
                    exerce_atividade_remunerada, vinculo_atividade, afinidade_atividade_curso,
                    participacao_economica_familiar, numero_membros_familia, renda_familiar,
                    motivo_escolha_curso, o_que_mais_espera_do_curso, expectativas_relacao_curso,
                    expectativas_profissionais_pos_curso, canal_informacao_curso, turno_preferencia
                ) VALUES (
                    :alunos_id, :oferta_id, :status_selecao, :termo_aceite,
                    :exerce_atividade_remunerada, :vinculo_atividade, :afinidade_atividade_curso,
                    :participacao_economica_familiar, :numero_membros_familia, :renda_familiar,
                    :motivo_escolha_curso, :o_que_mais_espera_do_curso, :expectativas_relacao_curso,
                    :expectativas_profissionais_pos_curso, :canal_informacao_curso, :turno_preferencia
                )";

        try {
            $stmt = $db->prepare($sql);

            $stmt->bindParam(':alunos_id', $this->alunos_id);
            $stmt->bindParam(':oferta_id', $this->oferta_id);
            $stmt->bindParam(':status_selecao', $this->status_selecao);
            $stmt->bindParam(':termo_aceite', $this->termo_aceite);
            $stmt->bindParam(':exerce_atividade_remunerada', $this->exerce_atividade_remunerada);
            $stmt->bindParam(':vinculo_atividade', $this->vinculo_atividade);
            $stmt->bindParam(':afinidade_atividade_curso', $this->afinidade_atividade_curso);
            $stmt->bindParam(':participacao_economica_familiar', $this->participacao_economica_familiar);
            $stmt->bindParam(':numero_membros_familia', $this->numero_membros_familia);
            $stmt->bindParam(':renda_familiar', $this->renda_familiar);
            $stmt->bindParam(':motivo_escolha_curso', $this->motivo_escolha_curso);
            $stmt->bindParam(':o_que_mais_espera_do_curso', $this->o_que_mais_espera_do_curso);
            $stmt->bindParam(':expectativas_relacao_curso', $this->expectativas_relacao_curso);
            $stmt->bindParam(':expectativas_profissionais_pos_curso', $this->expectativas_profissionais_pos_curso);
            $stmt->bindParam(':canal_informacao_curso', $this->canal_informacao_curso);
            $stmt->bindParam(':turno_preferencia', $this->turno_preferencia);

            return $stmt->execute();
        } catch (PDOException $e) {
            // Em um ambiente de produção, você poderia logar o erro em vez de usar die()
            die("Erro ao salvar inscrição: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Busca inscrições para o relatório geral com filtros.
     */
    public static function getRelatorio($ofertaId = null, $buscaNome = null) {
        $db = Database::getInstance()->getConnection();
        
        // MODIFICADO: Seleciona todos os campos de 'alunos' (a.*) e 'oferta' (o.numero_vagas)
        // para permitir o cálculo de fitness e determinar o número de vagas.
        $sql = "SELECT aho.*, a.*, o.nome as nome_oferta, o.numero_vagas, o.final_insc AS data_fim_inscricao
                FROM alunos_has_oferta aho
                JOIN alunos a ON aho.alunos_id = a.id
                JOIN oferta o ON aho.oferta_id = o.id
                WHERE 1=1";
        
        $params = [];

        if (!empty($ofertaId)) {
            $sql .= " AND aho.oferta_id = :ofertaId";
            $params[':ofertaId'] = $ofertaId;
        }

        if (!empty($buscaNome)) {
            $sql .= " AND a.nome LIKE :buscaNome";
            $params[':buscaNome'] = '%' . $buscaNome . '%';
        }

        $sql .= " ORDER BY a.nome ASC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function findByAlunoAndOferta($alunoId, $ofertaId) {
        $db = Database::getInstance()->getConnection();
        $sql = "SELECT * FROM alunos_has_oferta WHERE alunos_id = :alunoId AND oferta_id = :ofertaId";
        $stmt = $db->prepare($sql);
        $stmt->execute([':alunoId' => $alunoId, ':ofertaId' => $ofertaId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function findAllByAluno($alunoId) {
        $db = Database::getInstance()->getConnection();
        $sql = "SELECT * FROM alunos_has_oferta WHERE alunos_id = :alunoId";
        $stmt = $db->prepare($sql);
        $stmt->execute([':alunoId' => $alunoId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca todas as inscrições de um aluno, incluindo detalhes da oferta.
     * @param int $alunoId ID do aluno.
     * @return array Lista de inscrições com dados da oferta.
     */
    public static function findInscricoesByAlunoId($alunoId) {
        $db = Database::getInstance()->getConnection();
        $sql = "SELECT 
                    o.id,
                    o.nome,
                    o.descricao,
                    o.eixo_tecnologico,
                    o.foto,
                    o.inicio_insc,
                    o.final_insc,
                    o.status_oferta,
                    o.local,
                    o.turno,
                    o.numero_vagas,
                    o.publico_alvo,
                    o.modalidade,
                    o.carga_horaria,
                    o.inicio_aulas,
                    o.fim_aulas,
                    o.inicio_hora_aula,
                    o.fim_hora_aula,
                    aho.status_selecao,
                    aho.url_certificado,
                    aho.data_insc
                FROM alunos_has_oferta AS aho
                JOIN oferta AS o ON aho.oferta_id = o.id
                WHERE aho.alunos_id = :alunoId
                ORDER BY o.nome ASC";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([':alunoId' => $alunoId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function updateStatus($alunoId, $ofertaId, $status) {
        $db = Database::getInstance()->getConnection();
        $sql = "UPDATE alunos_has_oferta SET status_selecao = :status 
                WHERE alunos_id = :alunoId AND oferta_id = :ofertaId";
        $stmt = $db->prepare($sql);
        return $stmt->execute([':status' => $status, ':alunoId' => $alunoId, ':ofertaId' => $ofertaId]);
    }
}
