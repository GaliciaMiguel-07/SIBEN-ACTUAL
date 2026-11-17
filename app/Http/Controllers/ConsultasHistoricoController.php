<?php

namespace App\Http\Controllers;

use App\Models\Entidad;
use App\Models\Escuela;
use App\Models\Escuela2;
use App\Models\Especialidad;
use App\Models\Historia;
use App\Models\Licenciatura;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConsultasHistoricoController extends sibenController
{
    public function adminFormulario(Request $request)
    {
        $entidades = Entidad::where('id', '<', 33)->orderBy('id')->pluck('entidad', 'id');

        $criterios = [
            'id_entidad' => $request->integer('id_entidad') ?: null,
            'id_escuela' => $request->integer('id_escuela') ?: null,
            'nid' => '',
            'apellido_paterno' => '',
            'apellido_materno' => '',
            'nombre' => '',
            'curp' => '',
        ];

        $escuelas = $this->escuelasDeEntidad($criterios['id_entidad']);

        return view('consultas_historico.admin_buscar', [
            'entidades' => $entidades,
            'escuelas' => $escuelas,
            'criterios' => $criterios,
            'resultados' => collect(),
            'mensaje' => null,
        ]);
    }

    public function adminBuscar(Request $request)
    {
        $entidades = Entidad::where('id', '<', 33)->orderBy('id')->pluck('entidad', 'id');

        $criterios = [
            'id_entidad' => $request->integer('id_entidad') ?: null,
            'id_escuela' => $request->integer('id_escuela') ?: null,
            'nid' => $this->normalizarTexto($request->input('nid')),
            'apellido_paterno' => $this->normalizarTexto($request->input('apellido_paterno')),
            'apellido_materno' => $this->normalizarTexto($request->input('apellido_materno')),
            'nombre' => $this->normalizarTexto($request->input('nombre')),
            'curp' => $this->normalizarTexto($request->input('curp')),
        ];

        $escuelas = $this->escuelasDeEntidad($criterios['id_entidad']);

        if (!$this->hayCriteriosAdmin($criterios)) {
            return view('consultas_historico.admin_buscar', [
                'entidades' => $entidades,
                'escuelas' => $escuelas,
                'criterios' => $criterios,
                'resultados' => collect(),
                'mensaje' => 'Ingrese al menos un criterio de búsqueda.',
            ]);
        }

        $query = Historia::query()
            ->select('nid', 'apellido_paterno', 'apellido_materno', 'nombre', 'curp')
            ->where('nid', '<>', '');

        if (!empty($criterios['id_escuela'])) {
            $query->where('id_escuela', $criterios['id_escuela']);
        } elseif (!empty($criterios['id_entidad'])) {
            $idsEscuelasEntidad = Escuela::where('id_entidad', $criterios['id_entidad'])->pluck('id');
            $query->whereIn('id_escuela', $idsEscuelasEntidad);
        }

        foreach (['nid', 'apellido_paterno', 'apellido_materno', 'nombre', 'curp'] as $campo) {
            if (!empty($criterios[$campo])) {
                $query->where($campo, $criterios[$campo]);
            }
        }

        $resultados = $query
            ->groupBy('nid', 'apellido_paterno', 'apellido_materno', 'nombre', 'curp')
            ->orderBy('apellido_paterno')
            ->orderBy('apellido_materno')
            ->orderBy('nombre')
            ->get();

        return view('consultas_historico.admin_buscar', [
            'entidades' => $entidades,
            'escuelas' => $escuelas,
            'criterios' => $criterios,
            'resultados' => $resultados,
            'mensaje' => null,
        ]);
    }

    public function adminEscuelasPorEntidad(int $id_entidad): JsonResponse
    {
        return response()->json($this->escuelasDeEntidad($id_entidad));
    }

    public function usuarioIndividualFormulario()
    {
        return view('consultas_historico.usuario_individual', [
            'criterios' => [
                'nombre' => '',
                'apellido_paterno' => '',
                'apellido_materno' => '',
                'curp' => '',
                'nid' => '',
            ],
            'mensaje' => null,
        ]);
    }

    public function usuarioIndividualBuscar(Request $request)
    {
        $ctx = $this->contexto();
        $idEscuela = (int) ($ctx['id_escuela'] ?? 0);

        $criterios = [
            'nombre' => $this->normalizarTexto($request->input('nombre')),
            'apellido_paterno' => $this->normalizarTexto($request->input('apellido_paterno')),
            'apellido_materno' => $this->normalizarTexto($request->input('apellido_materno')),
            'curp' => $this->normalizarTexto($request->input('curp')),
            'nid' => $this->normalizarTexto($request->input('nid')),
        ];

        $modo = $this->validarCriteriosIndividual($criterios);
        if ($modo === null) {
            return view('consultas_historico.usuario_individual', [
                'criterios' => $criterios,
                'mensaje' => 'Debe buscar por CURP, por NID o por nombre completo (nombre y ambos apellidos).',
            ]);
        }

        $query = Historia::query()
            ->select('nid', 'apellido_paterno', 'apellido_materno', 'nombre', 'curp')
            ->where('id_escuela', $idEscuela)
            ->where('nid', '<>', '');

        if ($modo === 'nid') {
            $query->where('nid', $criterios['nid']);
        } elseif ($modo === 'curp') {
            $query->where('curp', $criterios['curp']);
        } else {
            $query->where('nombre', $criterios['nombre'])
                ->where('apellido_paterno', $criterios['apellido_paterno'])
                ->where('apellido_materno', $criterios['apellido_materno']);
        }

        $coincidencias = $query
            ->groupBy('nid', 'apellido_paterno', 'apellido_materno', 'nombre', 'curp')
            ->orderBy('apellido_paterno')
            ->orderBy('apellido_materno')
            ->orderBy('nombre')
            ->get();

        if ($coincidencias->count() === 0) {
            return view('consultas_historico.usuario_individual', [
                'criterios' => $criterios,
                'mensaje' => 'No se encontró un alumno con esos datos en su escuela.',
            ]);
        }

        if ($coincidencias->count() > 1) {
            return view('consultas_historico.usuario_individual', [
                'criterios' => $criterios,
                'mensaje' => 'La búsqueda devuelve más de un alumno. Complete más datos para identificar un único registro.',
            ]);
        }

        $nid = (string) $coincidencias->first()->nid;

        return $this->renderDetalle($nid, route('consultas_historico.usuario.individual.form'), $idEscuela);
    }

    public function usuarioGlobalFormulario()
    {
        return view('consultas_historico.usuario_global', [
            'criterios' => [
                'nombre' => '',
                'apellido_paterno' => '',
                'apellido_materno' => '',
                'curp' => '',
                'nid' => '',
            ],
            'resultados' => collect(),
            'mensaje' => null,
        ]);
    }

    public function usuarioGlobalBuscar(Request $request)
    {
        $ctx = $this->contexto();
        $idEscuela = (int) ($ctx['id_escuela'] ?? 0);

        $criterios = [
            'nombre' => $this->normalizarTexto($request->input('nombre')),
            'apellido_paterno' => $this->normalizarTexto($request->input('apellido_paterno')),
            'apellido_materno' => $this->normalizarTexto($request->input('apellido_materno')),
            'curp' => $this->normalizarTexto($request->input('curp')),
            'nid' => $this->normalizarTexto($request->input('nid')),
        ];

        $hayCriterio = collect($criterios)->contains(fn ($valor) => $valor !== '');
        if (!$hayCriterio) {
            return view('consultas_historico.usuario_global', [
                'criterios' => $criterios,
                'resultados' => collect(),
                'mensaje' => 'Ingrese al menos un criterio de búsqueda.',
            ]);
        }

        $query = Historia::query()
            ->select('nid', 'apellido_paterno', 'apellido_materno', 'nombre', 'curp')
            ->where('id_escuela', $idEscuela)
            ->where('nid', '<>', '');

        foreach (['nid', 'apellido_paterno', 'apellido_materno', 'nombre', 'curp'] as $campo) {
            if ($criterios[$campo] !== '') {
                $query->where($campo, $criterios[$campo]);
            }
        }

        $resultados = $query
            ->groupBy('nid', 'apellido_paterno', 'apellido_materno', 'nombre', 'curp')
            ->orderBy('apellido_paterno')
            ->orderBy('apellido_materno')
            ->orderBy('nombre')
            ->get();

        return view('consultas_historico.usuario_global', [
            'criterios' => $criterios,
            'resultados' => $resultados,
            'mensaje' => null,
        ]);
    }

    public function detalle(Request $request, ?string $nid = null)
    {
        if (empty($nid)) {
            $nid = (string) (
                $request->input('data.Historia.nid')
                ?? $request->input('data.Alumno2.nid')
                ?? $request->input('nid')
                ?? ''
            );
        }

        if ($nid === '') {
            return redirect()->route('dashboard')->with('error', 'No se recibió el NID del alumno a consultar.');
        }

        $ctx = $this->contexto();
        $nivel = (int) ($ctx['nivel'] ?? 0);

        $regresar = $request->query('regresar', route('dashboard'));

        $idEscuelaRestriccion = null;
        if (in_array($nivel, [2, 3, 4, 6], true)) {
            $idEscuelaRestriccion = (int) ($ctx['id_escuela'] ?? 0);
        }

        return $this->renderDetalle($nid, $regresar, $idEscuelaRestriccion);
    }

    private function renderDetalle(string $nid, string $regresar, ?int $idEscuelaRestriccion = null)
    {
        $historia = Historia::query()
            ->select([
                'nid', 'nombre', 'apellido_paterno', 'apellido_materno', 'curp', 'id_escuela',
                'modalidad', 'semestre', 'status', 'captura', 'id_licenciatura', 'id_especialidad', 'matricula',
            ])
            ->where('nid', $nid)
            ->when($idEscuelaRestriccion !== null && $idEscuelaRestriccion > 0, function ($query) use ($idEscuelaRestriccion) {
                $query->where('id_escuela', $idEscuelaRestriccion);
            })
            ->orderBy('id_escuela')
            ->orderByDesc('captura')
            ->get();

        if ($historia->isEmpty()) {
            return redirect($regresar)->with('error', 'No existe histórico disponible para ese alumno con las restricciones actuales.');
        }

        $idsEscuela = $historia->pluck('id_escuela')->unique()->values();
        $idsLic = $historia->pluck('id_licenciatura')->unique()->values();
        $idsEsp = $historia->pluck('id_especialidad')->unique()->values();

        $escuelas = Escuela::whereIn('id', $idsEscuela)->get(['id', 'escuela', 'id_entidad'])->keyBy('id');
        $entidades = Entidad::whereIn('id', $escuelas->pluck('id_entidad')->unique()->values())
            ->pluck('entidad', 'id');
        $licenciaturas = Licenciatura::whereIn('id', $idsLic)->pluck('licenciatura', 'id');
        $especialidades = Especialidad::whereIn('id', $idsEsp)->pluck('especialidad', 'id');

        $statusTexto = [
            'i' => 'Inscrito',
            'x' => 'No Inscrito',
            't' => 'Temporal',
            'd' => 'Definitiva',
        ];

        return view('consultas_historico.detalle_alumno', [
            'historia' => $historia,
            'escuelas' => $escuelas,
            'entidades' => $entidades,
            'licenciaturas' => $licenciaturas,
            'especialidades' => $especialidades,
            'statusTexto' => $statusTexto,
            'regresar' => $regresar,
        ]);
    }

    private function escuelasDeEntidad(?int $id_entidad): array
    {
        if (empty($id_entidad)) {
            return [];
        }

        return Escuela2::query()
            ->where('id_entidad', $id_entidad)
            ->where('status', 'a')
            ->where('unica', '<', 2)
            ->where('modulo', 'l')
            ->orderBy('escuela')
            ->pluck('escuela', 'id')
            ->toArray();
    }

    private function normalizarTexto(?string $valor): string
    {
        return mb_strtoupper(trim((string) $valor));
    }

    private function hayCriteriosAdmin(array $criterios): bool
    {
        return (bool) (
            !empty($criterios['id_entidad']) ||
            !empty($criterios['id_escuela']) ||
            $criterios['nid'] !== '' ||
            $criterios['apellido_paterno'] !== '' ||
            $criterios['apellido_materno'] !== '' ||
            $criterios['nombre'] !== '' ||
            $criterios['curp'] !== ''
        );
    }

    private function validarCriteriosIndividual(array $criterios): ?string
    {
        if ($criterios['nid'] !== '') {
            return 'nid';
        }

        if ($criterios['curp'] !== '') {
            return 'curp';
        }

        if (
            $criterios['nombre'] !== '' &&
            $criterios['apellido_paterno'] !== '' &&
            $criterios['apellido_materno'] !== ''
        ) {
            return 'nombre_completo';
        }

        return null;
    }
}
