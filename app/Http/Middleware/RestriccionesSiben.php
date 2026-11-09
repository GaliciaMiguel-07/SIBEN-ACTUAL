<?php

namespace App\Http\Middleware;

use App\Models\DomicilioEscuela;
use App\Models\Entidad;
use App\Models\Escuela;
use App\Models\EscuelaLicenciatura;
use App\Models\EscuelaStatus;
use App\Models\Personal;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Replica las restricciones que AppController::beforeFilter() aplicaba
 * a Licenciaturas, niveles 3 y 4, en el CakePHP original.
 *
 * Se aplica exclusivamente a rutas autenticadas del área SIBEN. No sustituye
 * las autorizaciones específicas de módulos todavía no migrados.
 */
class RestriccionesSiben
{
    private const ACCIONES_ESTRUCTURA = [
        'datosEscuela', 'editaEscuela', 'editaEstructura', 'editaCoordinadores',
        'guardaEscuela', 'guardaEstructura', 'guardaCoordinadores', 'editaDatos',
        'index', 'logout', 'localidades', 'menu', 'agrega', 'agrega2', 'edita',
        'lista', 'disciplinas', 'contrataciones', 'guardaAgregacion',
        'guardaEdicion', 'guardaContratacion', 'guardaAsignatura',
    ];

    private const ACCIONES_CLAVES = [
        'index', 'logout', 'parametros', 'editaLicenciatura',
        'agregaLicenciatura', 'eliminaLicenciatura',
    ];

    private const ACCIONES_DIRECTOR = [
        'menu', 'agrega', 'agrega2', 'edita', 'index', 'logout', 'lista',
        'disciplinas', 'contrataciones', 'guardaAgregacion', 'guardaEdicion',
        'guardaContratacion', 'guardaAsignatura',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $usuario = $request->user();

        // El grupo padre ya exige auth; estas condiciones replican el switch
        // de AppController: solo Licenciaturas y niveles 3/4.
        if (!$usuario || $usuario->modulo !== 'l' || !in_array((int) $usuario->nivel, [3, 4], true)) {
            return $next($request);
        }

        $idEscuela = $usuario->id_escuela;
        if (!$idEscuela) {
            return $next($request);
        }

        $status = EscuelaStatus::query()
            ->where('id_escuela', $idEscuela)
            ->where('modulo', $usuario->modulo)
            ->first();

        // El código CakePHP no imponía restricciones si no encontraba
        // el registro correspondiente en escuelas_status.
        if (!$status) {
            return $next($request);
        }

        $cuentaClaves = EscuelaLicenciatura::query()
            ->where('id_escuela', $idEscuela)
            ->where('clave', '')
            ->count();

        $cuentaDirector = Personal::query()
            ->where('id_escuela', $idEscuela)
            ->where('id_funcion', 1)
            ->where('status', 'a')
            ->count();

        $cuentaDomicilio = DomicilioEscuela::query()
            ->where('id_escuela', $idEscuela)
            ->count();

        // Compatibilidad para los métodos que aún leen session('Usuario.*').
        $escuela = Escuela::query()->select('id', 'escuela', 'id_entidad')->find($idEscuela);
        $entidad = $escuela?->id_entidad
            ? Entidad::query()->where('id', $escuela->id_entidad)->value('entidad')
            : null;

        session()->put([
            'Usuario.id'               => $usuario->getKey(),
            'Usuario.id_usuario'       => $usuario->id_usuario,
            'Usuario.nombre'           => $usuario->nombre,
            'Usuario.nivel'            => (int) $usuario->nivel,
            'Usuario.modulo'           => $usuario->modulo,
            'Usuario.id_escuela'       => $idEscuela,
            'Usuario.escuela'          => $escuela?->escuela,
            'Usuario.id_entidad'       => $escuela?->id_entidad,
            'Usuario.entidad'          => $entidad,
            'Usuario.status'           => $status->status,
            'Usuario.estructura'       => $status->estructura,
            'Usuario.captura'          => $status->captura,
            'Usuario.cuenta_clave'     => $cuentaClaves,
            'Usuario.cuenta_director'  => $cuentaDirector,
            'Usuario.cuenta_domicilio' => $cuentaDomicilio,
        ]);

        $accion = $request->route()?->getActionMethod() ?? '';
        $nivel = (int) $usuario->nivel;

        // Se conserva el orden y las condiciones del beforeFilter original:
        // estructura (solo nivel 4) -> claves -> director (solo nivel 4).
        if ((string) $status->estructura === '0' && $nivel === 4) {
            if (!in_array($accion, self::ACCIONES_ESTRUCTURA, true)) {
                return redirect('/siben/');
            }
        } elseif ($cuentaClaves > 0) {
            if (!in_array($accion, self::ACCIONES_CLAVES, true)) {
                return redirect($nivel === 3 ? '/siben/parametros' : '/siben/');
            }
        } elseif ($cuentaDirector === 0 && $nivel === 4) {
            if (!in_array($accion, self::ACCIONES_DIRECTOR, true)) {
                return redirect('/siben/');
            }
        }

        return $next($request);
    }
}
