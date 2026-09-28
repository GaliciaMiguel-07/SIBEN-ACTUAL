<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UsuarioRequest extends FormRequest
{
    /**
     * Solo el administrador nacional (nivel 1) puede gestionar usuarios.
     */
    public function authorize(): bool
    {
        return (int) auth()->user()?->nivel === 1;
    }

    public function rules(): array
    {
        // En rutas de edición, la ruta lleva el {usuario} (id entero)
        $id = $this->route('usuario');

        return [
            'nombre'     => 'required|string|max:100',

            // `id_usuario` es el nombre de columna real en la tabla `usuarios`
            'id_usuario' => [
                'required', 'string', 'max:50',
                Rule::unique('usuarios', 'id_usuario')->ignore($id),
            ],

            // `contrasenia` es la columna real (no `password`)
            // Al crear: obligatorio. Al editar: opcional (si viene vacío, no se toca)
            'contrasenia' => $id ? 'nullable|min:8' : 'required|min:8',

            'nivel'      => 'required|integer|between:1,7',
            'modulo'     => 'required|in:l,p',
            'id_escuela' => 'required|integer',
            'id_renapo'  => 'nullable|string|max:2',
            'status'     => 'required|in:a,x',
        ];
    }

    public function messages(): array
    {
        return [
            'id_usuario.unique'    => 'Ese nombre de usuario ya existe.',
            'id_usuario.required'  => 'El usuario es obligatorio.',
            'contrasenia.required' => 'La contraseña es obligatoria al crear un usuario.',
            'contrasenia.min'      => 'Mínimo 8 caracteres.',
            'id_escuela.required'  => 'Debes asignar una escuela.',
        ];
    }
}
