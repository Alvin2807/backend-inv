<?php

namespace App\Http\Requests\Solicitud;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'tipo_accion' =>'required|string',
            'usuario'     =>'required|string',
            'fk_despacho' =>'required|integer',
            'fk_tipo_entrada' =>'required|integer',
            'fecha_entrada' =>'required|date',
            /* 'preparado_por' =>'nullable|string', */
            'num_solicitud' =>'required|string',
            'fk_tipo_solicitud' =>'required|integer',
            'detalles' =>'sometimes|array|min:1',
            'detalles.*.fk_articulo' =>'required|integer',
            'detalles.*.cantidad_solicitada' =>'required|integer'
        ];
    }
}
