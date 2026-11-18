<?php

namespace App\Http\Controllers;

use App\Models\AreaConocimiento;
use App\Models\Docente;
use App\Models\Entidad;
use App\Models\Escuela;
use App\Models\Escuela2;
use App\Models\Funcion;
use App\Models\LenguaDocente;
use App\Models\LenguasIndigenas;
use App\Models\NivelEstudio;
use App\Models\NivelSni;
use App\Models\Personal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConsultaPersonalController extends sibenController
{
    private const STATUS = [
        'a' => 'Activo',
        'x' => 'Inactivo',
        'j' => 'Jubilado',
        'r' => 'Renuncia',
        'b' => 'Baja administrativa',
        'd' => 'Defunción',
    ];

    public function formulario(Request $request)
    {
        $ctx = $this->contexto();
        $nivel = (int) ($ctx['nivel'] ?? 0);

        $idEntidadContexto = $this->resolverIdEntidadContexto($ctx);
        $idEscuelaContexto = (int) ($ctx['id_escuela'] ?? 0);

        $entidades = $nivel === 1
            ? Entidad::where('id', '<', 33)->orderBy('id')->pluck('entidad', 'id')
            : Entidad::where('id', $idEntidadContexto)->pluck('entidad', 'id');

        $criterios = [
            'id_entidad' => $nivel === 1
                ? ($request->integer('id_entidad') ?: null)
                : ($idEntidadContexto ?: null),
            'id_escuela' => in_array($nivel, [3, 4], true)
                ? ($idEscuelaContexto ?: null)
                : ($request->integer('id_escuela') ?: null),
            'id_funcion' => $request->integer('id_funcion') ?: null,
            'status' => (string) $request->input('status', ''),
            'apellido_paterno' => '',
            'apellido_materno' => '',
            'nombre' => '',
            'curp' => '',
        ];

        if ($nivel === 2) {
            $criterios['id_entidad'] = $idEntidadContexto ?: null;
        }

        $escuelas = $this->escuelasSegunNivel($nivel, $criterios['id_entidad'], $ctx);

        return view('consultas_personal.formulario', [
            'nivel' => $nivel,
            'entidades' => $entidades,
            'escuelas' => $escuelas,
            'funciones' => Funcion::orderBy('funcion')->pluck('funcion', 'id'),
            'statusCatalogo' => self::STATUS,
            'criterios' => $criterios,
            'resultados' => collect(),
            'mensaje' => null,
            'entidadFija' => $entidades[$criterios['id_entidad']] ?? null,
            'escuelaFija' => $this->nombreEscuela($criterios['id_escuela']),
        ]);
    }

    public function buscar(Request $request)
    {
        $ctx = $this->contexto();
        $nivel = (int) ($ctx['nivel'] ?? 0);

        $idEntidadContexto = $this->resolverIdEntidadContexto($ctx);
        $idEscuelaContexto = (int) ($ctx['id_escuela'] ?? 0);

        $criterios = [
            'id_entidad' => $nivel === 1
                ? ($request->integer('id_entidad') ?: null)
                : ($idEntidadContexto ?: null),
            'id_escuela' => in_array($nivel, [3, 4], true)
                ? ($idEscuelaContexto ?: null)
                : ($request->integer('id_escuela') ?: null),
            'id_funcion' => $request->integer('id_funcion') ?: null,
            'status' => (string) $request->input('status', ''),
            'apellido_paterno' => $this->normalizarTexto($request->input('apellido_paterno')),
            'apellido_materno' => $this->normalizarTexto($request->input('apellido_materno')),
            'nombre' => $this->normalizarTexto($request->input('nombre')),
            'curp' => $this->normalizarTexto($request->input('curp')),
        ];

        if ($nivel === 2) {
            $criterios['id_entidad'] = $idEntidadContexto ?: null;
        }

        $entidades = $nivel === 1
            ? Entidad::where('id', '<', 33)->orderBy('id')->pluck('entidad', 'id')
            : Entidad::where('id', $idEntidadContexto)->pluck('entidad', 'id');

        $escuelas = $this->escuelasSegunNivel($nivel, $criterios['id_entidad'], $ctx);

        if (!$this->hayCriteriosBusqueda($criterios, $nivel)) {
            return view('consultas_personal.formulario', [
                'nivel' => $nivel,
                'entidades' => $entidades,
                'escuelas' => $escuelas,
                'funciones' => Funcion::orderBy('funcion')->pluck('funcion', 'id'),
                'statusCatalogo' => self::STATUS,
                'criterios' => $criterios,
                'resultados' => collect(),
                'mensaje' => 'Ingrese al menos un criterio de búsqueda.',
                'entidadFija' => $entidades[$criterios['id_entidad']] ?? null,
                'escuelaFija' => $this->nombreEscuela($criterios['id_escuela']),
            ]);
        }

        $query = Personal::query()->select([
            'id',
            'apellido_paterno',
            'apellido_materno',
            'nombre',
            'curp',
            'id_escuela',
            'id_funcion',
            'status',
        ]);

        $this->aplicarAlcancePorNivel($query, $nivel, $criterios, $ctx);

        if (!empty($criterios['id_funcion'])) {
            $query->where('id_funcion', $criterios['id_funcion']);
        }

        if ($criterios['status'] !== '' && array_key_exists($criterios['status'], self::STATUS)) {
            $query->where('status', $criterios['status']);
        }

        foreach (['apellido_paterno', 'apellido_materno', 'nombre', 'curp'] as $campo) {
            if ($criterios[$campo] !== '') {
                $query->where($campo, 'like', '%' . $criterios[$campo] . '%');
            }
        }

        $resultados = $query
            ->orderBy('apellido_paterno')
            ->orderBy('apellido_materno')
            ->orderBy('nombre')
            ->get();

        return view('consultas_personal.formulario', [
            'nivel' => $nivel,
            'entidades' => $entidades,
            'escuelas' => $escuelas,
            'funciones' => Funcion::orderBy('funcion')->pluck('funcion', 'id'),
            'statusCatalogo' => self::STATUS,
            'criterios' => $criterios,
            'resultados' => $resultados,
            'mensaje' => null,
            'entidadFija' => $entidades[$criterios['id_entidad']] ?? null,
            'escuelaFija' => $this->nombreEscuela($criterios['id_escuela']),
        ]);
    }

    public function escuelasPorEntidad(int $id_entidad): JsonResponse
    {
        return response()->json($this->escuelasDeEntidad($id_entidad));
    }

    public function detalle(Request $request, int $id)
    {
        $ctx = $this->contexto();
        $nivel = (int) ($ctx['nivel'] ?? 0);

        $personal = Personal::with(['escuela', 'funcion', 'docente'])->find($id);

        if (!$personal) {
            return redirect()->route('consultas_personal.formulario')
                ->with('error', 'No se encontró el personal solicitado.');
        }

        if (!$this->puedeVerPersonal($personal, $nivel, $ctx)) {
            abort(403, 'No tienes permiso para consultar este registro.');
        }

        $docente = $personal->docente;

        $nivelEstudio = null;
        $nivelSni = null;
        $area = null;
        if ($docente instanceof Docente) {
            $nivelEstudio = NivelEstudio::find($docente->id_nivel_estudio);
            $nivelSni = $docente->id_nivel_sni ? NivelSni::find($docente->id_nivel_sni) : null;
            $area = $docente->id_area_conocimiento ? AreaConocimiento::find($docente->id_area_conocimiento) : null;
        }

        $lenguas = [];
        if (!empty($personal->curp)) {
            $idsLengua = LenguaDocente::where('usuario', $personal->curp)
                ->pluck('id_lengua')
                ->filter()
                ->unique()
                ->values();

            if ($idsLengua->isNotEmpty()) {
                $lenguas = LenguasIndigenas::whereIn('id', $idsLengua)->pluck('lengua')->filter()->values()->all();
            }
        }

        if (empty($lenguas) && !empty($docente?->lengua_indigena)) {
            $lenguas = [trim((string) $docente->lengua_indigena)];
        }

        $entidadNombre = null;
        if (!empty($personal->escuela?->id_entidad)) {
            $entidadNombre = Entidad::where('id', $personal->escuela->id_entidad)->value('entidad');
        }

        $statusTexto = self::STATUS[$personal->status] ?? $personal->status;
        $generoTexto = [
            'M' => 'Mujer',
            'H' => 'Hombre',
            'm' => 'Mujer',
            'h' => 'Hombre',
        ][(string) $personal->genero] ?? (string) $personal->genero;

        $regresar = $request->query('regresar', route('consultas_personal.formulario'));

        return view('consultas_personal.detalle', [
            'entidadNombre' => $entidadNombre,
            'personal' => $personal,
            'docente' => $docente,
            'nivelEstudio' => $nivelEstudio,
            'nivelSni' => $nivelSni,
            'area' => $area,
            'lenguas' => $lenguas,
            'statusTexto' => $statusTexto,
            'generoTexto' => $generoTexto,
            'regresar' => $regresar,
        ]);
    }

    private function normalizarTexto(?string $valor): string
    {
        return mb_strtoupper(trim((string) $valor));
    }

    private function hayCriteriosBusqueda(array $criterios, int $nivel): bool
    {
        foreach (['apellido_paterno', 'apellido_materno', 'nombre', 'curp'] as $campo) {
            if ($criterios[$campo] !== '') {
                return true;
            }
        }

        if (!empty($criterios['id_funcion']) || $criterios['status'] !== '') {
            return true;
        }

        if ($nivel === 1 && (!empty($criterios['id_entidad']) || !empty($criterios['id_escuela']))) {
            return true;
        }

        if ($nivel === 2 && !empty($criterios['id_escuela'])) {
            return true;
        }

        return false;
    }

    private function aplicarAlcancePorNivel($query, int $nivel, array $criterios, array $ctx): void
    {
        if ($nivel === 1) {
            if (!empty($criterios['id_escuela'])) {
                $query->where('id_escuela', $criterios['id_escuela']);
                return;
            }

            if (!empty($criterios['id_entidad'])) {
                $idsEscuela = Escuela2::where('id_entidad', $criterios['id_entidad'])
                    ->where('status', 'a')
                    ->where('unica', '<', 2)
                    ->where('modulo', 'l')
                    ->pluck('id');
                $query->whereIn('id_escuela', $idsEscuela);
            }

            return;
        }

        if ($nivel === 2) {
            $idEntidad = $this->resolverIdEntidadContexto($ctx);
            $idsEscuela = Escuela2::where('id_entidad', $idEntidad)
                ->where('status', 'a')
                ->where('unica', '<', 2)
                ->where('modulo', 'l')
                ->pluck('id');

            $query->whereIn('id_escuela', $idsEscuela);

            if (!empty($criterios['id_escuela'])) {
                $query->where('id_escuela', $criterios['id_escuela']);
            }

            return;
        }

        if (in_array($nivel, [3, 4], true)) {
            $query->where('id_escuela', (int) ($ctx['id_escuela'] ?? 0));
        }
    }

    private function puedeVerPersonal(Personal $personal, int $nivel, array $ctx): bool
    {
        if ($nivel === 1) {
            return true;
        }

        $idEscuelaPersonal = (int) $personal->id_escuela;

        if (in_array($nivel, [3, 4], true)) {
            return $idEscuelaPersonal === (int) ($ctx['id_escuela'] ?? 0);
        }

        if ($nivel === 2) {
            $escuela = Escuela::find($idEscuelaPersonal);
            if (!$escuela) {
                return false;
            }

            return (int) $escuela->id_entidad === (int) $this->resolverIdEntidadContexto($ctx);
        }

        return false;
    }

    private function escuelasSegunNivel(int $nivel, ?int $idEntidadSeleccionada, array $ctx)
    {
        if ($nivel === 1) {
            return $this->escuelasDeEntidad($idEntidadSeleccionada);
        }

        if ($nivel === 2) {
            return $this->escuelasDeEntidad($this->resolverIdEntidadContexto($ctx));
        }

        if (in_array($nivel, [3, 4], true)) {
            $idEscuela = (int) ($ctx['id_escuela'] ?? 0);
            if ($idEscuela <= 0) {
                return collect();
            }

            return Escuela::where('id', $idEscuela)->pluck('escuela', 'id');
        }

        return collect();
    }

    private function escuelasDeEntidad(?int $idEntidad): array
    {
        if (empty($idEntidad)) {
            return [];
        }

        return Escuela2::where('id_entidad', $idEntidad)
            ->where('status', 'a')
            ->where('unica', '<', 2)
            ->where('modulo', 'l')
            ->orderBy('escuela')
            ->pluck('escuela', 'id')
            ->toArray();
    }

    private function resolverIdEntidadContexto(array $ctx): int
    {
        $idEntidad = (int) ($ctx['id_entidad'] ?? 0);

        if ($idEntidad > 0) {
            return $idEntidad;
        }

        $idEscuela = (int) ($ctx['id_escuela'] ?? 0);
        if ($idEscuela > 0) {
            $escuela = Escuela::find($idEscuela);
            return (int) ($escuela->id_entidad ?? 0);
        }

        return 0;
    }

    private function nombreEscuela(?int $idEscuela): ?string
    {
        if (empty($idEscuela)) {
            return null;
        }

        return Escuela::where('id', $idEscuela)->value('escuela');
    }
}
