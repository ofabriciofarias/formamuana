<?php

require_once __DIR__ . '/../core/Database.php';

class AlunoOferta {
    private $alunos_id;
    private $oferta_id;
    private $status_selecao;
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
    private $termo_aceite;

    // Setters
    public function setAlunosId($id) { $this->alunos_id = $id; }
    public function setOfertaId($id) { $this->oferta_id = $id; }
    public function setStatusSelecao($status) { $this->status_selecao = $status; }
    public function setExerceAtividadeRemunerada($val) { $this->exerce_atividade_remunerada = $val; }
    public function setVinculoAtividade($val) { $this->vinculo_atividade = $val; }
    public function setAfinidadeAtividadeCurso($val) { $this->afinidade_atividade_curso = $val; }
    public function setParticipacaoEconomicaFamiliar($val) { $this->participacao_economica_familiar = $val; }
    public function setNumeroMembrosFamilia($val) { $this->numero_membros_familia = $val; }
    public function setRendaFamiliar($val) { $this->renda_familiar = $val; }
    public function setMotivoEscolhaCurso($val) { $this->motivo_escolha_curso = $val; }
    public function setOQueMaisEsperaDoCurso($val) { $this->o_que_mais_espera_do_curso = $val; }
    public function setExpectativasRelacaoCurso($val) { $this->expectativas_relacao_curso = $val; }
    public function setExpectativasProfissionaisPosCurso($val) { $this->expectativas_profissionais_pos_curso = $val; }
    public function setCanalInformacaoCurso($val) { $this->canal_informacao_curso = $val; }
    public function setTurnoPreferencia($val) { $this->turno_preferencia = $val; }
    public function setTermoAceite($val) { $this->termo_aceite = $val; }

    public function save() {
        try {
            $db = Database::getInstance()->getConnection();
            
            // Verifica se já existe inscrição
            $check = $db->prepare("SELECT * FROM alunos_has_oferta WHERE alunos_id = :aid AND oferta_id = :oid");
            $check->execute([':aid' => $this->alunos_id, ':oid' => $this->oferta_id]);
            
            if ($check->rowCount() > 0) {
                // Já inscrito
                return false; 
            }

            $sql = "INSERT INTO alunos_has_oferta (
                        alunos_id, oferta_id, status_selecao, 
                        exerce_atividade_remunerada, vinculo_atividade, afinidade_atividade_curso,
                        participacao_economica_familiar, numero_membros_familia, renda_familiar,
                        motivo_escolha_curso, o_que_mais_espera_do_curso, expectativas_relacao_curso,
                        expectativas_profissionais_pos_curso, canal_informacao_curso, turno_preferencia,
                        termo_aceite
                    ) VALUES (
                        :alunos_id, :oferta_id, :status_selecao,
                        :exerce_atividade_remunerada, :vinculo_atividade, :afinidade_atividade_curso,
                        :participacao_economica_familiar, :numero_membros_familia, :renda_familiar,
                        :motivo_escolha_curso, :o_que_mais_espera_do_curso, :expectativas_relacao_curso,
                        :expectativas_profissionais_pos_curso, :canal_informacao_curso, :turno_preferencia,
                        :termo_aceite
                    )";
            
            $stmt = $db->prepare($sql);
            
            $stmt->bindValue(':alunos_id', $this->alunos_id);
            $stmt->bindValue(':oferta_id', $this->oferta_id);
            $stmt->bindValue(':status_selecao', $this->status_selecao);
            $stmt->bindValue(':exerce_atividade_remunerada', $this->exerce_atividade_remunerada);
            $stmt->bindValue(':vinculo_atividade', $this->vinculo_atividade);
            $stmt->bindValue(':afinidade_atividade_curso', $this->afinidade_atividade_curso);
            $stmt->bindValue(':participacao_economica_familiar', $this->participacao_economica_familiar);
            $stmt->bindValue(':numero_membros_familia', $this->numero_membros_familia);
            $stmt->bindValue(':renda_familiar', $this->renda_familiar);
            $stmt->bindValue(':motivo_escolha_curso', $this->motivo_escolha_curso);
            $stmt->bindValue(':o_que_mais_espera_do_curso', $this->o_que_mais_espera_do_curso);
            $stmt->bindValue(':expectativas_relacao_curso', $this->expectativas_relacao_curso);
            $stmt->bindValue(':expectativas_profissionais_pos_curso', $this->expectativas_profissionais_pos_curso);
            $stmt->bindValue(':canal_informacao_curso', $this->canal_informacao_curso);
            $stmt->bindValue(':turno_preferencia', $this->turno_preferencia);
            $stmt->bindValue(':termo_aceite', $this->termo_aceite);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao salvar inscrição: " . $e->getMessage());
            return false;
        }
    }
}