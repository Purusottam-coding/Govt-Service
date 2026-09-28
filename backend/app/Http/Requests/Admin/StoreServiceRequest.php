<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'department_id' => 'required|exists:departments,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'fee' => 'required|numeric|min:0',
            'processing_days' => 'required|integer|min:1',
            'required_documents' => 'nullable|string',
            'status' => 'boolean',
        ];
    }
}
