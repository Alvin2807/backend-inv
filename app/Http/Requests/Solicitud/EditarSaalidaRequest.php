<?php

namespace App\Http\Requests\Solicitud;

use Illuminate\Foundation\Http\FormRequest;

class EditarSaalidaRequest extends FormRequest
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
            'fk_despacho'   =>'required|integer',
            'fecha_salida' =>'required|date',
            'incidencia'   =>'required|integer',
            'usuario'       =>'required|string',
            'id_solicitud'  =>'required|integer',
            'fk_tipo_solicitud' =>'required|integer',
            'detalles'  =>'sometimes|array|min:1',
            'detalles.*.id_detalle'  =>'nullable|integer',
            'detalles.*.fk_articulo' =>'required|integer',
            'detalles.*.cantidad_solicitada' =>'required|integer',
            'detalles.*.no_item' =>'required|integer'
        ];
    }
}
