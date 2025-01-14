<?php

namespace App\Http\Requests\Articulos;

use Illuminate\Foundation\Http\FormRequest;

class EditarRequest extends FormRequest
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
            'fk_marca'        =>'required|integer',
            'fk_modelo'       =>'required|integer',
            'fk_categoria'    =>'required|integer',
            'fk_color'        =>'nullable|integer',
            'codigo'          =>'required|string',
            'usuario'         =>'required|string',
            'id_articulo'     =>'required|integer'
        ];
    }
}
