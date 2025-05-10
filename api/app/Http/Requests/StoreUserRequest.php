<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Set to true if you want to authorize this request.
        // You can add logic here to check user permissions if needed.
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'company_id' => 'required|integer|exists:companies,id',
            'position_company_id' => 'nullable|integer|exists:position_companies,id', // Still assuming position_companies table and id column
            'is_admin' => 'nullable|boolean',
            'is_super_admin' => 'nullable|boolean',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'O campo nome é obrigatório.',
            'name.string' => 'O campo nome deve ser um texto.',
            'name.max' => 'O campo nome não pode ter mais que 255 caracteres.',
            'email.required' => 'O campo email é obrigatório.',
            'email.string' => 'O campo email deve ser um texto.',
            'email.email' => 'O campo email deve ser um endereço de e-mail válido.',
            'email.max' => 'O campo email não pode ter mais que 255 caracteres.',
            'email.unique' => 'Este email já está em uso.',
            'password.required' => 'O campo senha é obrigatório.',
            'password.string' => 'O campo senha deve ser um texto.',
            'password.min' => 'O campo senha deve ter no mínimo 8 caracteres.',
            'company_id.required' => 'O campo ID da empresa é obrigatório.',
            'company_id.integer' => 'O campo ID da empresa deve ser um número inteiro.',
            'company_id.exists' => 'A empresa selecionada é inválida.',
            'position_company_id.integer' => 'O campo ID da posição deve ser um número inteiro.',
            'position_company_id.exists' => 'A posição selecionada é inválida.',
            'is_admin.boolean' => 'O campo is_admin deve ser verdadeiro ou falso.',
            'is_super_admin.boolean' => 'O campo is_super_admin deve ser verdadeiro ou falso.',
        ];
    }
}
