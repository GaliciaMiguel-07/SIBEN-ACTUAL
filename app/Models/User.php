<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    protected $table = 'usuarios';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'id_usuario',
        'contrasenia',
        'id_renapo',
        'id_escuela',
        'nombre',
        'nivel',
        'correo_electronico',
        'status',
        'modulo',
    ];

    protected $hidden = [
        'contrasenia',
    ];

    public function getAuthPassword()
    {
        return $this->contrasenia;
    }
    public function escuela()
{
    return $this->belongsTo(Escuela::class, 'id_escuela', 'id');
}
}
