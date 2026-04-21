<?php

use Illuminate\Database\Eloquent\Model;

class Aluno extends Model
{
    protected $table = 'alunos';
    public $timestamps = false;

    protected $fillable = [
        'nome', 'whatsapp', 'telefone', 'email', 'data_nascimento', 'sexo',
        'identidade_genero', 'possui_nome_social', 'nome_social', 'possui_deficiencia',
        'tipo_deficiencia', 'cor_raca', 'comunidade_tradicional', 'zona_residencia',
        'cidade_origem', 'cidade_atual', 'bairro_comunidade', 'estado_civil',
        'possui_filhos', 'quantidade_filhos', 'tipo_escola_ensino_medio',
        'nivel_escolaridade', 'beneficiario_programa_social', 'acesso_internet',
        'dispositivo_principal', 'usuarios_id',
    ];

    public static function findByEmail($email)
    {
        return static::where('email', $email)->first();
    }
}
