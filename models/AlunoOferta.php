<?php

use Illuminate\Database\Eloquent\Model;

class AlunoOferta extends Model
{
    protected $table = 'alunos_has_oferta';
    public $timestamps = false;
    protected $primaryKey = null;
    public $incrementing = false;

    protected $fillable = [
        'alunos_id', 'oferta_id', 'status_selecao', 'termo_aceite',
        'exerce_atividade_remunerada', 'vinculo_atividade', 'afinidade_atividade_curso',
        'participacao_economica_familiar', 'numero_membros_familia', 'renda_familiar',
        'motivo_escolha_curso', 'o_que_mais_espera_do_curso', 'expectativas_relacao_curso',
        'expectativas_profissionais_pos_curso', 'canal_informacao_curso', 'turno_preferencia',
        'concluiu_curso', 'nota_final', 'motivo_evasao',
    ];

    public static function getRelatorio($ofertaId = null, $buscaNome = null)
    {
        $query = static::query()
            ->select('alunos_has_oferta.*', 'alunos.*', 'oferta.nome as nome_oferta',
                'oferta.numero_vagas', 'oferta.final_insc as data_fim_inscricao')
            ->join('alunos', 'alunos_has_oferta.alunos_id', '=', 'alunos.id')
            ->join('oferta', 'alunos_has_oferta.oferta_id', '=', 'oferta.id');

        if (!empty($ofertaId)) {
            $query->where('alunos_has_oferta.oferta_id', $ofertaId);
        }

        if (!empty($buscaNome)) {
            $query->where('alunos.nome', 'like', '%' . $buscaNome . '%');
        }

        return $query->orderBy('alunos.nome')->get()->toArray();
    }

    public static function findByAlunoAndOferta($alunoId, $ofertaId)
    {
        return static::where('alunos_id', $alunoId)
            ->where('oferta_id', $ofertaId)
            ->first();
    }

    public static function findAllByAluno($alunoId)
    {
        return static::where('alunos_id', $alunoId)->get()->toArray();
    }

    public static function findInscricoesByAlunoId($alunoId)
    {
        return static::query()
            ->select(
                'oferta.id', 'oferta.nome', 'oferta.descricao', 'oferta.eixo_tecnologico',
                'oferta.foto', 'oferta.inicio_insc', 'oferta.final_insc', 'oferta.status_oferta',
                'oferta.local', 'oferta.turno', 'oferta.numero_vagas', 'oferta.publico_alvo',
                'oferta.modalidade', 'oferta.carga_horaria', 'oferta.inicio_aulas',
                'oferta.fim_aulas', 'oferta.inicio_hora_aula', 'oferta.fim_hora_aula',
                'alunos_has_oferta.status_selecao', 'alunos_has_oferta.data_insc'
            )
            ->join('oferta', 'alunos_has_oferta.oferta_id', '=', 'oferta.id')
            ->where('alunos_has_oferta.alunos_id', $alunoId)
            ->orderBy('oferta.nome')
            ->get()
            ->toArray();
    }

    public static function updateStatus($alunoId, $ofertaId, $status)
    {
        return static::where('alunos_id', $alunoId)
            ->where('oferta_id', $ofertaId)
            ->update(['status_selecao' => $status]);
    }
}
