<?php

namespace App\Http\Requests\File;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Upload File Request
 * ONLY handles input validation and format checks
 * Middleware handles: logging, authorization, security, rate limiting
 */
class UploadFileRequest extends FormRequest
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
        // Validation is kept self-contained (no service dependency).
        // Laravel's "max" rule expects KB.
        $maxSizeKb = (20 * 1024 * 1024) / 1024; // 20MB
        $allowedExtensions = 'pdf,png,jpg,jpeg';

        return [
            'file' => [
                'required',
                'file',
                'max:' . $maxSizeKb,
                'mimes:' . $allowedExtensions,
            ],
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        $maxSizeMB = 20;
        $allowedTypes = 'pdf, png, jpg, jpeg';

        return [
            'file.required' => 'No file provided',
            'file.file' => 'The uploaded file is not valid',
            'file.max' => "File size exceeds maximum allowed size of {$maxSizeMB}MB",
            'file.mimes' => "File type not allowed. Only {$allowedTypes} files are allowed",
        ];
    }
}