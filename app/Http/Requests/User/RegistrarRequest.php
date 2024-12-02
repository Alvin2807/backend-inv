<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class RegistrarRequest extends FormRequest
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
            'usuario'     =>'required|string',
            'name'        =>'required|string',
            'apellido'    =>'required|string',
            'email'       =>'nullable|email',
            'password'    =>'required|string',
            'fk_despacho' =>'required|integer',
            'fk_rol'      =>'required|integer'
        ];
    }
}
