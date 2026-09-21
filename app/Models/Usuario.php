<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    // Apuntamos a la tabla exacta de tu base de datos antigua
    protected $table = 'aut_usuarios';

    // Desactivamos los timestamps por defecto de Laravel
    public $timestamps = false; 
}