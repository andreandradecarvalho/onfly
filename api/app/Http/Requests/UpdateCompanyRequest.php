<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Authorization is handled by middleware or in the controller/service layer
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $companyId = $this->route('id'); // Get the company ID from the route

        return [
            'name' => 'sometimes|required|string|max:255',
            'email' => [
                'sometimes',
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('companies')->ignore($companyId),
            ],
            'document' => [
                'sometimes',
                'required',
                'string',
                'max:20', // Adjust max length as needed for CNPJ/CPF
                Rule::unique('companies')->ignore($companyId),
            ],
            'address' => 'nullable|string|max:255',
            'responsibility' => 'nullable|string|max:255',
            // Add other fields as needed, making them 'sometimes' or 'nullable'
            // if they are not strictly required for every update.
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'O nome da empresa é obrigatório.',
            'name.string' => 'O nome da empresa deve ser um texto.',
            'name.max' => 'O nome da empresa não pode ter mais que 255 caracteres.',
            'email.required' => 'O email da empresa é obrigatório.',
            'email.string' => 'O email da empresa deve ser um texto.',
            'email.email' => 'O email da empresa deve ser um endereço de email válido.',
            'email.max' => 'O email da empresa não pode ter mais que 255 caracteres.',
            'email.unique' => 'Este email já está em uso por outra empresa.',
            'document.required' => 'O documento da empresa é obrigatório.',
            'document.string' => 'O documento da empresa deve ser um texto.',
            'document.max' => 'O documento da empresa não pode ter mais que 20 caracteres.',
            'document.unique' => 'Este documento já está em uso por outra empresa.',
            'address.string' => 'O endereço deve ser um texto.',
            'address.max' => 'O endereço não pode ter mais que 255 caracteres.',
            'responsibility.string' => 'A responsabilidade deve ser um texto.',
            'responsibility.max' => 'A responsabilidade não pode ter mais que 255 caracteres.',
        ];
    }
}
