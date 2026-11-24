@php
    /**
     * Adaptador de datos para mantener el contrato del .ctp legacy.
     */
    $alumno = [
        'Registro' => [
            'id' => $infoAlumno->id ?? '',
            'id_licenciatura' => $infoAlumno->id_licenciatura ?? '',
            'id_especialidad' => $infoAlumno->id_especialidad ?? '',
            'modalidad' => $infoAlumno->modalidad ?? '',
            'turno' => $infoAlumno->turno ?? '',
            'semestre' => $infoAlumno->semestre ?? '',
            'grupo' => $infoAlumno->grupo ?? '',
            'matricula' => $infoAlumno->matricula ?? '',
            'status' => $infoAlumno->status ?? '',
        ],
        'Alumno' => [
            'numero_identificador' => $infoAlumno->nid ?? '',
            'apellido_paterno' => $infoAlumno->alumno->apellido_paterno ?? '',
            'apellido_materno' => $infoAlumno->alumno->apellido_materno ?? '',
            'nombre' => $infoAlumno->alumno->nombre ?? '',
            'curp' => $infoAlumno->alumno->curp ?? '',
            'genero' => $infoAlumno->alumno->genero ?? '',
            'edad' => $infoAlumno->alumno->edad ?? '',
            'entidad_nacimiento' => $infoAlumno->alumno->entidad_nacimiento ?? '',
            'nacionalidad' => $infoAlumno->alumno->nacionalidad ?? '',
            'id_documento' => $infoAlumno->alumno->id_documento ?? '',
            'municipio_nacimiento' => $infoAlumno->alumno->municipio_nacimiento ?? '',
            'discapacidad' => $infoAlumno->alumno->discapacidad ?? 'n',
            'tipo_discapacidad' => $infoAlumno->alumno->tipo_discapacidad ?? '',
            'correo' => $infoAlumno->alumno->correo ?? '',
            'telefono' => $infoAlumno->alumno->telefono ?? '',
            'pais' => $infoAlumno->alumno->pais ?? '',
            'entidad_nal' => $infoAlumno->alumno->entidad_nal ?? '',
            'pais_procedencia' => $infoAlumno->alumno->pais_procedencia ?? '',
            'entidad_procedencia' => $infoAlumno->alumno->entidad_procedencia ?? '',
            'entidad_domicilio' => $infoAlumno->alumno->entidad_domicilio ?? '',
            'municipio_domicilio' => $infoAlumno->alumno->municipio_domicilio ?? '',
            'cp' => $infoAlumno->alumno->cp ?? '',
            'segunda_lengua' => $infoAlumno->alumno->segunda_lengua ?? '',
            'antecedente' => $infoAlumno->alumno->antecedente ?? '',
            'fecha' => $infoAlumno->alumno->fecha ?? '',
            'origen' => $infoAlumno->alumno->origen ?? '',
        ],
    ];

    $alumno['AlumnoBachillerato'] = [
        'id_entidad' => $infoBachillerato->id_entidad ?? '',
        'id_municipio' => $infoBachillerato->id_municipio ?? '',
        'id_bachillerato' => $infoBachillerato->id_bachillerato ?? 0,
        'promedio' => $infoBachillerato->promedio ?? '',
        'otro' => $infoBachillerato->otro ?? '',
    ];

    $permisos = $permiso ?? [];
    $generos = ['M' => 'Mujer', 'H' => 'Hombre'];
    $nacionalidadMap = ['m' => 'Mexicano', 'e' => 'Extranjero'];
    $sino = $sino ?? ['s' => 'Sí', 'n' => 'No'];
@endphp

<script type="text/javascript" language="javascript">
if ($('AlumnoDiscapacidad') && $('AlumnoDiscapacidad').value != 's') {
  $$('div.discapacidad').each(function (e) {
    e.hide();
  });
}

if ($('AlumnoBachilleratoIdBachillerato') && $('AlumnoBachilleratoIdBachillerato').value != 0 ) {
  $$('div.otro').each(function (e) {
    e.hide();
  });
}

if ($('AlumnoCorreo')) {
  $('AlumnoCorreo').writeAttribute('voluntario', true);
}

if ($('AlumnoTelefono')) {
  $('AlumnoTelefono').writeAttribute('voluntario', true);
}

if ($('RegistroSemestre') && $('RegistroSemestre').getValue() <= 2) {
  if ($('RegistroMatricula')) {
    $('RegistroMatricula').writeAttribute('voluntario', true);
    $('RegistroMatricula').writeAttribute('maxlength', '12');
  }
}
if ($('RegistroSemestre') && $('RegistroSemestre').getValue() >= 13) {
  if ($('RegistroMatricula')) {
    $('RegistroMatricula').writeAttribute('maxlength', '8');
  }
}

if ($('AlumnoApellidoMaterno')) {
  $('AlumnoApellidoMaterno').writeAttribute('voluntario', true);
}

if ($('RegistroSemestre')) {
  Event.observe('RegistroSemestre', 'change', function (event) {
    var semestre = $('RegistroSemestre').getValue();
    if (semestre >= 7 && semestre <= 8) {
      muestra2('becados');
    } else {
      oculta2('becados');
    }

    if ($('RegistroMatricula') && semestre <= 2) {
      $('RegistroMatricula').writeAttribute('voluntario', true);
    }

    if (semestre >= 4 || semestre < 4) {
      if ($('RegistroIdLicenciatura') && $('RegistroIdLicenciatura').getValue() == 7) {
        $('RegistroIdLicenciatura').value = '';
        if ($('RegistroIdEspecialidad')) $('RegistroIdEspecialidad').value = '';
        if ($('RegistroModalidad')) $('RegistroModalidad').value = '';
        if ($('RegistroTurno')) $('RegistroTurno').value = '';
        alert('Actualize las siguientes opciones:\n - Licenciatura\n - Especialidad\n - Modalida\n - Turno');
      }
    }
  });
}
</script>

<form id="ActualizarInfoAlumno" name="ActualizarInfoAlumno" method="post" action="javascript:void(0);">
  @csrf

  <table class="datos">
    <tr><th colspan="8">Información del Alumno</th></tr>
    <tr>
      <td>Licenciatura:</td>
      <td>
        @if(($permisos['id_licenciatura'] ?? 1) == 0)
          {{ $licenciatura[$alumno['Registro']['id_licenciatura']] ?? '' }}
          <input type="hidden" name="Registro[id_licenciatura]" id="RegistroIdLicenciatura" value="{{ $alumno['Registro']['id_licenciatura'] }}" />
        @else
          <select name="Registro[id_licenciatura]" id="RegistroIdLicenciatura" nombre="Licenciatura">
            <option value="">Seleccione</option>
            @foreach($licenciatura as $valor => $texto)
              <option value="{{ $valor }}" @selected((string)$alumno['Registro']['id_licenciatura']===(string)$valor)>{{ $texto }}</option>
            @endforeach
          </select>
        @endif
      </td>

      <td>Especialidad:</td>
      <td>
        @if(($permisos['id_especialidad'] ?? 1) == 0)
          {{ $especialidad[$alumno['Registro']['id_especialidad']] ?? '' }}
          <input type="hidden" name="Registro[id_especialidad]" id="RegistroIdEspecialidad" value="{{ $alumno['Registro']['id_especialidad'] }}" />
        @else
          <select name="Registro[id_especialidad]" id="RegistroIdEspecialidad" nombre="Especialidad">
            <option value="">Seleccione</option>
            @foreach($especialidad as $valor => $texto)
              <option value="{{ $valor }}" @selected((string)$alumno['Registro']['id_especialidad']===(string)$valor)>{{ $texto }}</option>
            @endforeach
          </select>
          <div id="especialidad" style="display:none;">{{-- spinner --}}<img src="{{ asset('img/spinner.gif') }}" alt="cargando" /></div>
        @endif
      </td>

      <td>Modalidad:</td>
      <td>
        @if(($permisos['modalidad'] ?? 1) == 0)
          {{ $modalidad[$alumno['Registro']['modalidad']] ?? '' }}
          <input type="hidden" name="Registro[modalidad]" id="RegistroModalidad" value="{{ $alumno['Registro']['modalidad'] }}" />
        @else
          <select name="Registro[modalidad]" id="RegistroModalidad" nombre="Modalidad">
            <option value="">Seleccione</option>
            @foreach($modalidad as $valor => $texto)
              <option value="{{ $valor }}" @selected((string)$alumno['Registro']['modalidad']===(string)$valor)>{{ $texto }}</option>
            @endforeach
          </select>
          <div id="modalidad" style="display:none;"><img src="{{ asset('img/spinner.gif') }}" alt="cargando" /></div>
        @endif
      </td>

      <td>Turno:</td>
      <td>
        @if(($permisos['turno'] ?? 1) == 0)
          {{ $turnos[$alumno['Registro']['turno']] ?? '' }}
          <input type="hidden" name="Registro[turno]" id="RegistroTurno" value="{{ $alumno['Registro']['turno'] }}" />
        @else
          <select name="Registro[turno]" id="RegistroTurno" nombre="Turno">
            <option value="">Seleccione</option>
            @foreach($turnos as $valor => $texto)
              <option value="{{ $valor }}" @selected((string)$alumno['Registro']['turno']===(string)$valor)>{{ $texto }}</option>
            @endforeach
          </select>
          <div id="turno" style="display:none;"><img src="{{ asset('img/spinner.gif') }}" alt="cargando" /></div>
        @endif
      </td>
    </tr>

    <tr>
      <td>Semestre:</td>
      <td>
        @if(($permisos['semestre'] ?? 1) == 0)
          {{ $semestre[$alumno['Registro']['semestre']] ?? $alumno['Registro']['semestre'] }}
          <input type="hidden" name="Registro[semestre]" id="RegistroSemestre" value="{{ $alumno['Registro']['semestre'] }}" />
        @else
          <select name="Registro[semestre]" id="RegistroSemestre" nombre="Semestre">
            <option value="">Seleccione</option>
            @foreach($semestre as $valor => $texto)
              <option value="{{ $valor }}" @selected((string)$alumno['Registro']['semestre']===(string)$valor)>{{ $texto }}</option>
            @endforeach
          </select>
        @endif
      </td>

      <td>Grupo:</td>
      <td>
        @if(($permisos['grupo'] ?? 1) == 0)
          {{ $alumno['Registro']['grupo'] }}
          <input type="hidden" name="Registro[grupo]" id="RegistroGrupo" value="{{ $alumno['Registro']['grupo'] }}" />
        @else
          <input type="text" name="Registro[grupo]" id="RegistroGrupo" value="{{ $alumno['Registro']['grupo'] }}" size="10" nombre="Grupo" onblur="this.value=this.value.trim().toUpperCase();" />
        @endif
      </td>

      <td>Matricula:</td>
      <td colspan="3">
        @if(($permisos['matricula'] ?? 1) == 0)
          {{ $alumno['Registro']['matricula'] }}
          <input type="hidden" name="Registro[matricula]" id="RegistroMatricula" value="{{ $alumno['Registro']['matricula'] }}" />
        @else
          <input type="text" name="Registro[matricula]" id="RegistroMatricula" value="{{ $alumno['Registro']['matricula'] }}" size="14" nombre="matricula" onblur="this.value=this.value.trim().toUpperCase();testMat(this.id)" />
        @endif
      </td>
    </tr>
  </table>

  <br /><br />

  <table class="datos">
    <tr><th colspan="6">Información Personal</th></tr>
    <tr>
      <td>Primer Apellido:</td>
      <td><b>{{ $alumno['Alumno']['apellido_paterno'] }}<input type="hidden" name="Alumno[apellido_paterno]" id="AlumnoApellidoPaterno" value="{{ $alumno['Alumno']['apellido_paterno'] }}" /></b></td>
      <td>Segundo Apellido:</td>
      <td><b>{{ $alumno['Alumno']['apellido_materno'] }}<input type="hidden" name="Alumno[apellido_materno]" id="AlumnoApellidoMaterno" value="{{ $alumno['Alumno']['apellido_materno'] }}" /></b></td>
      <td>Nombre:</td>
      <td><b>{{ $alumno['Alumno']['nombre'] }}<input type="hidden" name="Alumno[nombre]" id="AlumnoNombre" value="{{ $alumno['Alumno']['nombre'] }}" /></b></td>
    </tr>
    <tr>
      <td>Curp:</td>
      <td><b>{{ $alumno['Alumno']['curp'] }}<input type="hidden" name="Alumno[curp]" id="AlumnoCurp" value="{{ $alumno['Alumno']['curp'] }}" /></b></td>
      <td>Genero:</td>
      <td><b>{{ $generos[$alumno['Alumno']['genero']] ?? '' }}</b></td>
      <td>Edad:</td>
      <td><b>{{ $alumno['Alumno']['edad'] }}</b></td>
    </tr>
    <tr>
      <td>Entidad Nacimiento:</td>
      <td><b>{{ $entidad_nac[$alumno['Alumno']['entidad_nacimiento']] ?? '' }}</b></td>
      <td>Nacionalidad:</td>
      <td><b>{{ $nacionalidadMap[$alumno['Alumno']['nacionalidad']] ?? '' }}</b></td>
      <td>Documentación:</td>
      <td><b>{{ $documentos[$alumno['Alumno']['id_documento']] ?? '' }}</b></td>
    </tr>
    <tr>
      <td><b>Municipio Nacimiento:</b></td>
      <td>
        <select name="Alumno[municipio_nacimiento]" id="AlumnoMunicipioNacimiento" nombre="Municipio">
          <option value="">Seleccione</option>
          @foreach($municipios_nacimiento as $valor => $texto)
            <option value="{{ $valor }}" @selected((string)$alumno['Alumno']['municipio_nacimiento']===(string)$valor)>{{ $texto }}</option>
          @endforeach
        </select>
      </td>
    </tr>
  </table>

  <br />

  <table class="datos">
    <tr><th colspan="6">Información de Bachillerato</th></tr>
    <tr>
      <td><b>Entidad bachillerato:</b>
        <select name="AlumnoBachillerato[id_entidad]" id="AlumnoBachilleratoIdEntidad" nombre="Entidad">
          <option value="">Seleccione</option>
          @foreach($entidades as $valor => $texto)
            <option value="{{ $valor }}" @selected((string)$alumno['AlumnoBachillerato']['id_entidad']===(string)$valor)>{{ $texto }}</option>
          @endforeach
        </select>
      </td>
      <td><b>Municipio Bachillerato:</b>
        <select name="AlumnoBachillerato[id_municipio]" id="AlumnoBachilleratoIdMunicipio" nombre="Municipio">
          <option value="">Seleccione</option>
          @foreach($municipios_bach as $valor => $texto)
            <option value="{{ $valor }}" @selected((string)$alumno['AlumnoBachillerato']['id_municipio']===(string)$valor)>{{ $texto }}</option>
          @endforeach
        </select>
      </td>
    </tr>
    <tr>
      <td><b>Nombre Bachilletaro:</b>
        <select name="AlumnoBachillerato[id_bachillerato]" id="AlumnoBachilleratoIdBachillerato" nombre="Alumno Bachilletaro" onchange="datosOcultos3(this.id,'otro')">
          <option value="">Seleccione</option>
          @foreach($bachilleratos as $valor => $texto)
            <option value="{{ $valor }}" @selected((string)$alumno['AlumnoBachillerato']['id_bachillerato']===(string)$valor)>{{ $texto }}</option>
          @endforeach
        </select>
      </td>
      <td><b>Promedio:</b></td>
      <td><input type="text" name="AlumnoBachillerato[promedio]" id="AlumnoBachilleratoPromedio" value="{{ $alumno['AlumnoBachillerato']['promedio'] }}" size="8" /></td>
    </tr>
    <tr>
      <td colspan="4">
        <div class="otro">
          <b>¿Cual?:</b>
          <input type="text" name="AlumnoBachillerato[otro]" id="AlumnoBachilleratoOtro" value="{{ $alumno['AlumnoBachillerato']['otro'] }}" nombre="otro" onblur="this.value=this.value.trim().toUpperCase();" size="60" />
        </div>
      </td>
    </tr>
  </table>

  <br />

  <table class="datos">
    <tr><th colspan="3">Discapacidad</th></tr>
    <tr>
      <td>¿El alumno sufre alguna discapacidad?</td>
      <td colspan="2">
        <select name="Alumno[discapacidad]" id="AlumnoDiscapacidad" nombre="Discapacidad" onchange="datosOcultos(this.id,'discapacidad',1,1)">
          <option value="">Seleccione</option>
          @foreach(['s' => 'Sí', 'n' => 'No'] as $valor => $texto)
            <option value="{{ $valor }}" @selected((string)$alumno['Alumno']['discapacidad']===(string)$valor)>{{ $texto }}</option>
          @endforeach
        </select>
      </td>
    </tr>
    <tr>
      <td colspan="3">
        <div class="discapacidad">
          ¿Cual?:&nbsp;
          <select name="Alumno[tipo_discapacidad]" id="AlumnoTipoDiscapacidad" nombre="Tipo Discapacidad">
            <option value="">Seleccione</option>
            @foreach($discapacidad as $valor => $texto)
              <option value="{{ $valor }}" @selected((string)$alumno['Alumno']['tipo_discapacidad']===(string)$valor)>{{ $texto }}</option>
            @endforeach
          </select>
        </div>
      </td>
    </tr>
  </table>

  <table class="datos">
    <tr><th colspan="4">Información SIGED</th></tr>
    <tr>
      <td>Correo electrónico:</td>
      <td><input type="text" name="Alumno[correo]" id="AlumnoCorreo" value="{{ $alumno['Alumno']['correo'] }}" nombre="Correo electrónico" /></td>
      <td>Número telefónico:</td>
      <td><input type="text" name="Alumno[telefono]" id="AlumnoTelefono" value="{{ $alumno['Alumno']['telefono'] }}" size="10" maxlength="10" minlength="10" nombre="Número telefónico" /></td>
    </tr>
    <tr>
      <td>Pais Nacimiento:</td>
      <td>
        <select name="Alumno[pais]" id="AlumnoPais" nombre="Pais Nacimiento">
          <option value="">Seleccione</option>
          @foreach($paises as $valor => $texto)
            <option value="{{ $valor }}" @selected((string)$alumno['Alumno']['pais']===(string)$valor)>{{ $texto }}</option>
          @endforeach
        </select>
      </td>
      <td>Entidad Nacimiento:</td>
      <td>
        <select name="Alumno[entidad_nal]" id="AlumnoEntidadNal" nombre="Entidad de Nacimiento">
          <option value="">Seleccione</option>
          @foreach($entidadesnal as $valor => $texto)
            <option value="{{ $valor }}" @selected((string)$alumno['Alumno']['entidad_nal']===(string)$valor)>{{ $texto }}</option>
          @endforeach
        </select>
      </td>
    </tr>
    <tr>
      <td>Pais procedencia</td>
      <td>
        <select name="Alumno[pais_procedencia]" id="AlumnoPaisProcedencia" nombre="Pais procedencia">
          <option value="">Seleccione</option>
          @foreach($paises as $valor => $texto)
            <option value="{{ $valor }}" @selected((string)$alumno['Alumno']['pais_procedencia']===(string)$valor)>{{ $texto }}</option>
          @endforeach
        </select>
      </td>
      <td>Entidad procedencia:</td>
      <td>
        <select name="Alumno[entidad_procedencia]" id="AlumnoEntidadProcedencia" nombre="Entidad procedencia">
          <option value="">Seleccione</option>
          @foreach($entidadesnal3 as $valor => $texto)
            <option value="{{ $valor }}" @selected((string)$alumno['Alumno']['entidad_procedencia']===(string)$valor)>{{ $texto }}</option>
          @endforeach
        </select>
      </td>
    </tr>
    <tr>
      <td coslpan="4"><b>Domicilio</b></td>
    </tr>
    <tr>
      <td>Entidad del domicilio del alumno:</td>
      <td>
        <select name="Alumno[entidad_domicilio]" id="AlumnoEntidadDomicilio" nombre="Entidad domicilio">
          <option value="">Seleccione</option>
          @foreach($entidades as $valor => $texto)
            <option value="{{ $valor }}" @selected((string)$alumno['Alumno']['entidad_domicilio']===(string)$valor)>{{ $texto }}</option>
          @endforeach
        </select>
      </td>
      <td>Municipio del domicilio del alumno:</td>
      <td>
        <select name="Alumno[municipio_domicilio]" id="AlumnoMunicipioDomicilio" nombre="Municipio domicilio">
          <option value="">Seleccione</option>
          @foreach($municipios2 as $valor => $texto)
            <option value="{{ $valor }}" @selected((string)$alumno['Alumno']['municipio_domicilio']===(string)$valor)>{{ $texto }}</option>
          @endforeach
        </select>
      </td>
    </tr>
    <tr>
      <td>CP del domicilio:</td>
      <td><input type="text" name="Alumno[cp]" id="AlumnoCp" value="{{ $alumno['Alumno']['cp'] }}" size="5" maxlength="5" minlength="5" nombre="Codigo Postal" /></td>
    </tr>
    <tr>
      <td>Segunda lengua:</td>
      <td>
        <select name="Alumno[segunda_lengua]" id="AlumnoSegundaLengua" nombre="Segunda lengua">
          <option value="">Seleccione</option>
          @foreach($lenguas as $valor => $texto)
            <option value="{{ $valor }}" @selected((string)$alumno['Alumno']['segunda_lengua']===(string)$valor)>{{ $texto }}</option>
          @endforeach
        </select>
      </td>
      <td>Antecedente academico:</td>
      <td>
        <select name="Alumno[antecedente]" id="AlumnoAntecedente" nombre="antecedente">
          <option value="">Seleccione</option>
          @foreach(['1' => 'Sí', '2' => 'No'] as $valor => $texto)
            <option value="{{ $valor }}" @selected((string)$alumno['Alumno']['antecedente']===(string)$valor)>{{ $texto }}</option>
          @endforeach
        </select>
      </td>
    </tr>
    <tr>
      <td>Fecha de ingreso a educación superior 1er semestre:</td>
      <td><input type="date" name="Alumno[fecha]" id="AlumnoFecha" value="{{ $alumno['Alumno']['fecha'] }}" nombre="Fecha de ingreso" min="1980-01-01" max="2025-12-31" /></td>
      <td>Origen de estudios precedentes:</td>
      <td>
        <select name="Alumno[origen]" id="AlumnoOrigen" nombre="origen">
          <option value="">Seleccione</option>
          @foreach(['1' => 'Nacionales', '2' => 'Extranjeros'] as $valor => $texto)
            <option value="{{ $valor }}" @selected((string)$alumno['Alumno']['origen']===(string)$valor)>{{ $texto }}</option>
          @endforeach
        </select>
      </td>
    </tr>
  </table>

  <br /><br />

  <div style="text-align: center;">
    <input type="hidden" name="Registro[id]" id="RegistroId" value="{{ $alumno['Registro']['id'] }}" />
    <input type="hidden" name="Alumno[numero_identificador]" id="AlumnoNumeroIdentificador" value="{{ $alumno['Alumno']['numero_identificador'] }}" />
    <input type="hidden" name="Renglon[status]" id="RenglonStatus" value="{{ $alumno['Registro']['status'] }}" />

    <input type="button" id="EnviarPeticion" value="Guardar" onclick="enviar('ActualizarInfoAlumno', '{{ route('alumnos.actualizarAlumno') }}');" />
  </div>

  <br /><br />
</form>

<script type="text/javascript" language="javascript">
@if(isset($actualiza))
Event.observe('RegistroIdLicenciatura', 'change', function () {
  new Ajax.Updater('RegistroIdEspecialidad', '{{ route('alumnos.especialidadOpciones') }}', {
    method: 'post',
    asynchronous: true,
    parameters: Form.serializeElements($('ActualizarInfoAlumno').getElements()),
    onLoading: function () { Element.show('especialidad'); },
    onComplete: function () { Element.hide('especialidad'); }
  });
});

Event.observe('RegistroIdEspecialidad', 'change', function () {
  new Ajax.Updater('RegistroModalidad', '{{ route('alumnos.modalidadOpciones') }}', {
    method: 'post',
    asynchronous: true,
    parameters: Form.serializeElements($('ActualizarInfoAlumno').getElements()),
    onLoading: function () { Element.show('modalidad'); },
    onComplete: function () { Element.hide('modalidad'); }
  });
});

Event.observe('RegistroModalidad', 'change', function () {
  new Ajax.Updater('RegistroTurno', '{{ route('alumnos.turnoOpciones') }}', {
    method: 'post',
    asynchronous: true,
    parameters: Form.serializeElements($('ActualizarInfoAlumno').getElements()),
    onLoading: function () { Element.show('turno'); },
    onComplete: function () { Element.hide('turno'); }
  });
});
@endif

if ($('AlumnoBachilleratoIdEntidad')) {
  Event.observe('AlumnoBachilleratoIdEntidad', 'change', function () {
    new Ajax.Updater('AlumnoBachilleratoIdMunicipio', '{{ route('alumnos.municipiosBachilleratos') }}', {
      method: 'post',
      asynchronous: true,
      parameters: Form.serializeElements($('ActualizarInfoAlumno').getElements()),
      onLoading: function () { muestraOcupado(); },
      onComplete: function () { ocultaOcupado(); }
    });
  });
}

if ($('AlumnoBachilleratoIdMunicipio')) {
  Event.observe('AlumnoBachilleratoIdMunicipio', 'change', function () {
    new Ajax.Updater('AlumnoBachilleratoIdBachillerato', '{{ route('alumnos.bachilleratoOpciones') }}', {
      method: 'post',
      asynchronous: true,
      parameters: Form.serializeElements($('ActualizarInfoAlumno').getElements()),
      onLoading: function () { muestraOcupado(); },
      onComplete: function () { ocultaOcupado(); }
    });
  });
}

if ($('AlumnoEntidadDomicilio')) {
  Event.observe('AlumnoEntidadDomicilio', 'change', function () {
    new Ajax.Updater('AlumnoMunicipioDomicilio', '{{ route('alumnos.municipiosNacimiento6') }}', {
      method: 'post',
      asynchronous: true,
      parameters: Form.serializeElements($('ActualizarInfoAlumno').getElements()),
      onLoading: function () { muestraOcupado(); },
      onComplete: function () { ocultaOcupado(); }
    });
  });
}

if ($('AlumnoPais')) {
  Event.observe('AlumnoPais', 'change', function () {
    new Ajax.Updater('AlumnoEntidadNal', '{{ route('alumnos.municipiosNacimiento3') }}', {
      method: 'post',
      asynchronous: true,
      parameters: Form.serializeElements($('ActualizarInfoAlumno').getElements()),
      onLoading: function () { muestraOcupado(); },
      onComplete: function () { ocultaOcupado(); }
    });
  });
}

if ($('AlumnoPaisProcedencia')) {
  Event.observe('AlumnoPaisProcedencia', 'change', function () {
    new Ajax.Updater('AlumnoEntidadProcedencia', '{{ route('alumnos.municipiosNacimiento5') }}', {
      method: 'post',
      asynchronous: true,
      parameters: Form.serializeElements($('ActualizarInfoAlumno').getElements()),
      onLoading: function () { muestraOcupado(); },
      onComplete: function () { ocultaOcupado(); }
    });
  });
}
</script>
