<?php

namespace App\Http\Requests\Service;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Read Service Request
 * ONLY handles input validation and format checks
 * Middleware handles: logging, authorization, existence checks, rate limiting
 */
class ReadServiceRequest extends FormRequest
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
            'service_id' => 'required|string|min:10',
            'download_pdf' => 'nullable|boolean',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'service_id.required' => 'Service ID is required',
            'service_id.min' => 'Invalid service ID format',
            'download_pdf.boolean' => 'download_pdf must be a boolean value',
        ];
    }

    /**
     * Get all of the input and files for the request.
     */
    public function all($keys = null)
    {
        $input = parent::all($keys);
        
        // For GET requests, check query string as well
        if ($this->isMethod('GET')) {
            $query = $this->query();
            if (isset($query['service_id'])) {
                $input['service_id'] = $query['service_id'];
            }
            if (isset($query['download_pdf'])) {
                $input['download_pdf'] = $query['download_pdf'];
            }
        }
        
        return $input;
    }
}