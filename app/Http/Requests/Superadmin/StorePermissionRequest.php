<?php

namespace App\Http\Requests\Superadmin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:permissions',
            'label' => 'required|string|max:255',
            'group' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ];
    }
}