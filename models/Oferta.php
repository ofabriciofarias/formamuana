<?php

use Illuminate\Database\Eloquent\Model;

class Oferta extends Model
{
    protected $table = 'oferta';
    public $timestamps = false;

    protected $fillable = [
        'nome', 'descricao', 'eixo_tecnologico', 'foto', 'inicio_insc', 'final_insc',
        'status_oferta', 'usuarios_id', 'local', 'turno', 'numero_vagas', 'publico_alvo',
        'modalidade', 'carga_horaria', 'upload_relatorio', 'inicio_aulas', 'fim_aulas',
        'inicio_hora_aula', 'fim_hora_aula',
    ];

    public static function findAll()
    {
        return static::orderBy('inicio_insc', 'desc')->get()->toArray();
    }

    public static function findById($id)
    {
        return static::find($id);
    }

    public static function getAllUsers()
    {
        return \Illuminate\Database\Capsule\Manager::table('usuarios')
            ->select('id', 'nome')
            ->get()
            ->toArray();
    }
}
