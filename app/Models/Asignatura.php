<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Asignatura extends Model {
use HasFactory;
protected $table = 'cat_asignaturas';
protected $primaryKey = 'id';
public $timestamps = false;
protected $fillable = ['asignatura','horas','creditos','semestre','id_licenciatura','id_especialidad','modalidad','curricular','evento'];
public static function rules() { return ['asignatura' => 'required|string']; }
public function licenciatura() { return $this->belongsTo(Licenciatura::class, 'id_licenciatura'); }
}
