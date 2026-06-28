<?php

namespace App\Http\Requests\File;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Delete File Request
 * ONLY handles input validation and format checks
 * Middleware handles: logging, authorization, audit trail, existence checks
 */
class DeleteFileRequest extends FormRequest
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
        ];
    }
}