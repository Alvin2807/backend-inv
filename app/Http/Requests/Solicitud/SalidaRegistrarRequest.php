<?php

namespace App\Http\Requests\Solicitud;

use Illuminate\Foundation\Http\FormRequest;

class SalidaRegistrarRequest extends FormRequest
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
            'fecha_salida' =>'required|date',
            'fk_tipo_solicitud' =>'required|integer',
            'incidencia' =>'required|integer',
            'articulos' =>'sometimes|array|min:1',
            'articulos.*.fk_articulo' =>'required|integer',
            'articulos.*.cantidad_solicitada' =>'required|integer',
            'articulos.*.no_item' =>'required|integer',
            'articulos.*.fk_nomenclatura' =>'required|integer',
        ];
    }
}
