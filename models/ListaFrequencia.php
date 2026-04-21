<?php

use Illuminate\Database\Eloquent\Model;

class ListaFrequencia extends Model
{
    protected $table = 'lista_frequencia';
    public $timestamps = false;

    protected $fillable = ['aula_id', 'alunos_id', 'presenca', 'justificativa_falta'];
}
