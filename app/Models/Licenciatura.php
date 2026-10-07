<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Licenciatura extends Model
{
    use HasFactory;

    // Nombre de la tabla en tu base de datos
    protected $table = 'cat_licenciaturas';

    // Desactivar timestamps automáticos si la tabla antigua no tiene created_at / updated_at
    public $timestamps = false;

    // Llave primaria (por defecto Laravel asume 'id', si tiene otro nombre especifícalo aquí)
    protected $primaryKey = 'id';

    // Campos permitidos para asignación masiva
    protected $fillable = [
        'licenciatura',
        'status',
    ];

    /**
     * Relación Muchos a Muchos con Escuela (tabla pivote: escuelas_licenciaturas)
     */
    public function escuelas()
    {
        return $this->belongsToMany(
            Escuela::class,
            'escuelas_licenciaturas',
            'id_licenciatura', // Llave pivote correspondiente a Licenciatura
            'id_escuela'       // Llave pivote correspondiente a Escuela
        );
    }

    /**
     * Relación Uno a Muchos con Alumno
     */
    public function alumnos()
    {
        return $this->hasMany(Alumno::class, 'id_licenciatura');
    }

    /**
     * Relación Uno a Muchos con Especialidad
     */
    public function especialidades()
    {
        return $this->hasMany(Especialidad::class, 'id_licenciatura');
    }
}