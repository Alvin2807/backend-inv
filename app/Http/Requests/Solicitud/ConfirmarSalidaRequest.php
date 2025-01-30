<?php

namespace App\Http\Requests\Solicitud;

use Illuminate\Foundation\Http\FormRequest;

class ConfirmarSalidaRequest extends FormRequest
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
            'usuario' =>'required|string',
            'detalles.*.id_detalle' =>'required|integer',
            'detalles.*.fk_articulo' =>'required|integer',
            'detalles.*.cantidad_solicitada' =>'required|integer'
        ];
    }
}
