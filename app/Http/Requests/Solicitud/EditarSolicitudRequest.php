<?php

namespace App\Http\Requests\Solicitud;

use Illuminate\Foundation\Http\FormRequest;

class EditarSolicitudRequest extends FormRequest
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
            'fecha_entrada' =>'required|date',
            'id_solicitud'  =>'required|integer',
            'preparado_por' =>'required|string',
            'usuario'       =>'required|string',
            'num_solicitud' =>'required|string',
            'fk_tipo_solicitud' =>'required|integer',
            'articulos'    =>'sometimes|array|min:1',
            'articulos.*.id_detalle' =>'nullable|integer',
            'articulos.*.fk_articulo' =>'required|integer',
            'articulos.*.cantidad_solicitada' =>'required|integer',
            'articulos.*.no_item' =>'required|integer'
        ];
    }
}
