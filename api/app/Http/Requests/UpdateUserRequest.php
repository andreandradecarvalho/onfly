<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Add authorization logic if needed, e.g., check if the authenticated user
        // can update the target user. For now, let's assume true.
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $userId = $this->route('id'); // Correctly fetches {id} from PUT /users/{id}

        return [
            'name' => 'sometimes|string|max:255',
            'email' => [
                'sometimes',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'password' => 'nullable|string|min:6', // Password is optional on update
            'company_id' => 'sometimes|integer|exists:companies,id',
            'position_company_id' => 'nullable|integer|exists:position_companies,id',
            'is_admin' => 'sometimes|boolean',
            'is_super_admin' => 'sometimes|boolean',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'name.string' => 'O campo nome deve ser um texto.',
            'name.max' => 'O campo nome não pode ter mais que 255 caracteres.',
            'email.string' => 'O campo email deve ser um texto.',
            'email.email' => 'O campo email deve ser um endereço de e-mail válido.',
            'email.max' => 'O campo email não pode ter mais que 255 caracteres.',
            'email.unique' => 'Este email já está em uso.',
            'password.string' => 'O campo senha deve ser um texto.',
            'password.min' => 'O campo senha deve ter no mínimo 6 caracteres.',
            'company_id.integer' => 'O campo ID da empresa deve ser um número inteiro.',
            'company_id.exists' => 'A empresa selecionada é inválida.',
            'position_company_id.integer' => 'O campo ID da posição deve ser um número inteiro.',
            'position_company_id.exists' => 'A posição selecionada é inválida.',
            'is_admin.boolean' => 'O campo is_admin deve ser verdadeiro ou falso.',
            'is_super_admin.boolean' => 'O campo is_super_admin deve ser verdadeiro ou falso.',
        ];
    }
}
