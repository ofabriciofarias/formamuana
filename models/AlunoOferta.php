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
}
