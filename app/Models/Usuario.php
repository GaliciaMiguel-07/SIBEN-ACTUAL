<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;

class Usuario extends Authenticatable
{
    // Tabla real con los 2,436 usuarios del sistema
    protected $table      = 'usuarios';

    // La PK es el id auto-incremental (entero), NO id_usuario (que es el login/varchar)
    protected $primaryKey = 'id';
    public    $timestamps = false;

    protected $fillable = [
        'id_usuario',   // login del sistema (varchar)
        'contrasenia',  // contraseña (sha1 o bcrypt)
        'google2fa_secret',
        'nivel',
        'modulo',
        'id_escuela',
        'nombre',
        'status',
        'id_renapo',
        'correo_electronico',
    ];

    // Ocultar la contraseña al serializar (ej. en JSON o logs)
    protected $hidden = ['contrasenia','google2fa_secret'];

    // ─── Autenticación (Laravel Auth) ────────────────────────────────────────

    /**
     * Laravel Auth necesita saber cuál campo es la contraseña.
     * La columna real se llama `contrasenia`, no `password`.
     */
    public function getAuthPassword(): string
    {
        return $this->contrasenia;
    }

    // ─── Mutator: al asignar contraseña nueva, siempre se guarda en bcrypt ───

    public function setContraseniaAttribute(string $valor): void
    {
        // Si ya viene hasheado (bcrypt comienza con $2y$), no hashear de nuevo
        if (!str_starts_with($valor, '$2y$') && !str_starts_with($valor, '$argon')) {
            $this->attributes['contrasenia'] = Hash::make($valor);
        } else {
            $this->attributes['contrasenia'] = $valor;
        }
    }

    // ─── Relaciones ──────────────────────────────────────────────────────────

    /**
     * Escuela asignada al usuario (niveles 3, 4, 5).
     * Clave foránea: usuarios.id_escuela → escuelas.id
     */
    public function escuela()
    {
        return $this->belongsTo(Escuela::class, 'id_escuela');
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    public function getNombreNivel(): string
    {
        return match((int) $this->nivel) {
            1 => 'DGESPE (Admin Nacional)',
            2 => 'Enlace / Supervisor Estatal',
            3 => 'Supervisor de Escuela',
            4 => 'Capturista',
            5 => 'Consulta Externa',
            6 => 'Observador Estatal',
            7 => 'Búsqueda',
            default => 'Desconocido',
        };
    }

    public function estaActivo(): bool
    {
        return $this->status === 'a';
    }
}
