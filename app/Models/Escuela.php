<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Escuela extends Model
{
    protected $table = 'escuelas';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'escuela_key',
        'id_entidad',
        'escuela',
        'sostenimiento',
        'calle',
        'colonia',
        'id_municipio',
        'id_localidad',
        'cp',
        'telefono1',
        'telefono2',
        'telefono3',
        'fax',
        'lada',
        'correo_electronico',
        'pagina_web',
        'unica',
        'clave_escuela',
        'clave_institucion',
        'fecha_autorizacion',
        'fecha_inicio',
        'investigacion',
        'turno',
        'origen',
    ];

    // ─── Relaciones ──────────────────────────────────────────────────────────

    /**
     * Entidad a la que pertenece la escuela.
     * Clave foránea: escuelas.id_entidad → cat_entidades.id
     */
    public function entidad()
    {
        return $this->belongsTo(Entidad::class, 'id_entidad');
    }
    public function caracteristicaEscuela()
{
    return $this->hasOne(CaracteristicaEscuela::class, 'id_escuela', 'id');
}
}
