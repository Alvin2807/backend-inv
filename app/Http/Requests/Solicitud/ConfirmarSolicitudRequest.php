<?php

namespace App\Http\Requests\Solicitud;

use Illuminate\Foundation\Http\FormRequest;

class ConfirmarSolicitudRequest extends FormRequest
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
            'id_solicitud' =>'required|integer',
            'usuario'      =>'required|string',
            'fk_tipo_solicitud' =>'required|integer',
            'fk_tipo_entrada' =>'required|integer',
            'fk_despacho' =>'required|integer',
            'num_solicitud' =>'required|string',
            'mes'=>'required|string',
            'fecha_entrada' =>'required|date',
            'entregado_por' =>'required|string',
            'articulos'     =>'sometimes|array|min:1',
            'articulos.*.id_detalle'  =>'required|integer',
            'articulos.*.no_item'  =>'required|integer',
            'articulos.*.fk_articulo' =>'required|integer',
            'articulos.*.cantidad_solicitada' =>'required|integer'
        ];
    }
}
