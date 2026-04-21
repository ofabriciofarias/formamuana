<?php

use Illuminate\Database\Eloquent\Model;

class Aula extends Model
{
    protected $table = 'aula';
    public $timestamps = false;

    protected $fillable = ['oferta_id', 'data_aula', 'conteudo_ministrado', 'carga_horaria_aula'];
}
