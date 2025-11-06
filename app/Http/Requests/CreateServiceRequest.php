<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateServiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'price' => ['required', 'numeric', 'min:0', 'max:9999.99'],
            'duration_minutes' => ['required', 'integer', 'min:5', 'max:480'],
            'image' => ['nullable', 'string', 'max:255'],
            'service_type' => ['required', Rule::in(['haircut', 'beard', 'hair_and_beard', 'kids', 'spa', 'other'])],
            'is_active' => ['nullable', 'boolean'],
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
            'name.required' => 'Service name is required.',
            'price.required' => 'Service price is required.',
            'price.min' => 'Price must be at least 0.',
            'duration_minutes.required' => 'Service duration is required.',
            'duration_minutes.min' => 'Duration must be at least 5 minutes.',
            'duration_minutes.max' => 'Duration cannot exceed 480 minutes (8 hours).',
            'service_type.required' => 'Service type is required.',
        ];
    }
}
