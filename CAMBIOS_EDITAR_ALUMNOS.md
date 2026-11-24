# Cambios realizados: flujo **Editar Alumno** (niveles 2, 3 y 4)

## 1) Archivos creados

### `resources/views/alumnos/lista_escuelas.blade.php`
Pantalla de **Fase 0 (nivel 2)** para elegir escuela (nombre + clave + botón **Ir**). Conserva el flujo legacy por AJAX (`setIdEscuela`) y después navega a la búsqueda.

### `resources/views/alumnos/busqueda_alumnos.blade.php`
Pantalla de **Fase 1 + Fase 2** de edición:
- Formulario de búsqueda con campos `Busca[...]`.
- Resultado en tabla AJAX dentro de `#resultadosBusqueda`.
- Overlay legacy (`#overoverlay` + `#overlay`) para abrir edición sin recargar.
- Conserva contrato JS legacy (`consultaAjax`, `confirmacion`, `enviar`, `actualizarRenglon`).

### `resources/views/alumnos/actualizar_alumno.blade.php`
Fragmento AJAX de **Fase 3** (formulario de edición) para abrirse dentro del overlay:
- Información del alumno
- Información personal
- Información de bachillerato
- Discapacidad
- Información SIGED
- Domicilio
- Botón **Guardar** por AJAX

Mantiene nombres de campos legacy esperados por controlador:
`Alumno[...]`, `Registro[...]`, `AlumnoBachillerato[...]`, `Renglon[...]`.

### `resources/views/elements/tablaAjax.blade.php`
Tabla AJAX de resultados con formato legacy:
- `<table id="tablaAjax" class="datos2">`
- `id="row{id_registro}"` por renglón
- contador correlativo
- usa `{!! !!}` para columnas HTML (`status` y `acción`).

### `resources/views/elements/vacio.blade.php`
Vista vacía usada por respuestas AJAX sin contenido.

### `resources/views/elements/actualizarRenglon.blade.php`
Fragmento para refrescar un renglón de la tabla tras guardar edición por AJAX.

---

## 2) Archivos modificados

### `app/Http/Controllers/AlumnosController.php`
Se ajustó seguridad y resolución de escuela para el flujo de edición:

- **Nuevo método `resolverEscuelaTrabajo(...)`**
  - Prioridad solicitada: `id_escuela de ruta ?? contexto ?? sesión`.
  - Para **niveles 3 y 4** fuerza SIEMPRE la escuela del usuario autenticado.

- **Nuevo método `escuelaEnEntidadNivel2(...)`**
  - Valida que una escuela sí pertenezca a la entidad del usuario nivel 2.

- **`setIdEscuela()`**
  - Ahora valida que nivel 2 solo pueda seleccionar escuelas de su entidad.
  - Guarda `session('Usuario.id_escuela')` únicamente si pasa validación.

- **`busquedaAlumnos()`**
  - Usa resolución de escuela corregida.
  - Si nivel 2 no tiene escuela elegida, redirige a `alumnos.listaEscuelas`.
  - Si la escuela no corresponde a su entidad, responde 403.

- **`listasAlumnos()`**
  - Usa resolución de escuela corregida.
  - Aplica validación de entidad para nivel 2.

- **`actualizarAlumno()`**
  - Valida y toma `id_registro` primero.
  - Carga `registroBase` para conocer la escuela real del registro.
  - **Nivel 3/4**: obliga que el registro sea de su propia escuela.
  - **Nivel 2**: permite editar solo si la escuela del registro pertenece a su entidad.

### `routes/web.php`
Se agregó ruta faltante para refresco de renglón AJAX:
- `POST /alumnos/actualizaRenglon` → `alumnos.actualizaRenglon`

---

## 3) Cómo probar en tu XAMPP local (paso a paso)

> Asumiendo que tu proyecto queda servido desde `http://localhost/siben/public`.

### Nivel 2 (Supervisor Estatal)
1. Inicia sesión con usuario nivel 2.
2. Ve a: `http://localhost/siben/public/admin/alumnos`
3. En **Editar Alumno** pulsa **Acceder**.
4. Debe salir **Elegir escuela** (Fase 0).
5. Pulsa **Ir** en una escuela de tu entidad.
6. Debe abrir **Búsqueda de Alumnos Para la Edición** (Fase 1).
7. Busca y confirma tabla de resultados (Fase 2).
8. Pulsa **Editar** y verifica overlay con formulario completo (Fase 3).
9. Guarda y verifica respuesta AJAX sin recarga completa.

### Nivel 3 (Supervisor de Escuela)
1. Inicia sesión con usuario nivel 3.
2. Ve a: `http://localhost/siben/public/admin/alumnos`
3. Entra a **Editar Alumno**.
4. Debe ir directo a **Fase 1** (sin elegir escuela).
5. Buscar → tabla (Fase 2) → Editar en overlay (Fase 3).
6. Verifica que solo muestra alumnos de su escuela.

### Nivel 4 (Capturista)
1. Inicia sesión con usuario nivel 4.
2. Ve a: `http://localhost/siben/public/admin/alumnos`
3. Entra a **Editar Alumno**.
4. Igual que nivel 3: Fase 1 → Fase 2 → Fase 3.
5. Verifica que no pueda editar alumnos de otra escuela aunque cambie URL.

---

## 4) Validaciones técnicas ejecutadas

- `php artisan view:cache` ✅
- `php artisan view:clear` ✅
- `php artisan route:list --path=alumnos` ✅
- `php -l app/Http/Controllers/AlumnosController.php` ✅
- `php -l routes/web.php` ✅
- Render sintético sin BD real con `View::make(...)` de:
  - `alumnos.busqueda_alumnos`
  - `alumnos.actualizar_alumno`
  Resultado: ambos renderizaron con datos de prueba ✅

---

## 5) Pendiente fuera de alcance de esta tarea

No se tocó (intencionalmente) la lógica de:
- Reinscripción
- Bajas (temporal/definitiva)
- Altas de alumno
- Cambio de escuela
- Flujo del panel administrador (`AdminController` y `resources/views/admin/alumnos/*`)
