<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class AsignaturaDocente extends Model {
use HasFactory;
protected $table = 'asignaturas_docentes';
protected $primaryKey = 'id';
public $timestamps = false;
protected $fillable = ['id_asignatura','id_licenciatura','id_especialidad','id_docente','grupo','turno','horas','semestre','modalidad','curricular'];
public static function rules() { return ['grupo' => 'required|string']; }
public function asignatura() { return $this->belongsTo(Asignatura::class, 'id_asignatura'); }
public function docente() { return $this->belongsTo(Docente::class, 'id_docente'); }
}
