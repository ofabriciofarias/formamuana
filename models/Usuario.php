<?php

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    protected $table = 'usuarios';
    public $timestamps = false;
    protected $hidden = ['senha'];

    protected $fillable = [
        'nome', 'login', 'email', 'senha', 'telefone', 'tipo_usuario_id', 'termo_aceite',
        'upload_rg', 'upload_cpf', 'upload_plano', 'upload_pis_ou_nis',
        'upload_residencia', 'upload_cart_trab', 'upload_declaracao_vinculo_matr',
    ];

    public static function findById($id)
    {
        return static::find($id);
    }
}
