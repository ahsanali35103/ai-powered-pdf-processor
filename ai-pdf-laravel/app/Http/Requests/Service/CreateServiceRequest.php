<?php

namespace App\Http\Requests\Service;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Service;

/**
 * Create Service Request
 * ONLY handles input validation and format checks
 * Middleware handles: logging, authorization, quota checks, rate limiting
 */
class CreateServiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled by middleware
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'file_id' => 'required|string|min:10',
            'type' => 'required|string|in:' . implode(',', Service::VALID_TYPES),
            'target_language' => 'required_if:type,translation|nullable|string|min:2|max:5',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'file_id.required' => 'File ID is required',
            'file_id.min' => 'Invalid file ID format',
            'type.required' => 'Service type is required',
            'type.in' => 'Invalid service type. Allowed types: ' . implode(', ', Service::VALID_TYPES),
            'target_language.required_if' => 'Target language is required for translation service',
            'target_language.min' => 'Invalid target language code format',
            'target_language.max' => 'Invalid target language code format',
        ];
    }
}