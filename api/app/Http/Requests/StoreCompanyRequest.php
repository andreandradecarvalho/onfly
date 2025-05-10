<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:companies,name',
            'email' => 'required|email|unique:companies,email',
            'document' => 'required|string|unique:companies,document',
            'address' => 'nullable|string|max:500',
            'responsibility' => 'nullable|string|max:20',
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'Nome da empresa já cadastrado.',
            'email.unique' => 'Email da empresa já cadastrado.',
            'document.unique' => 'Documento da empresa já cadastrado.',
        ];
    }
}
