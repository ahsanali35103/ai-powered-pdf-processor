<?php

namespace App\Http\Requests\Service;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Service;

/**
 * List Service Request
 * ONLY handles input validation and format checks
 * Middleware handles: logging, authorization, pagination, rate limiting
 */
class ListServiceRequest extends FormRequest
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
            // No validation rules - handled in controller
        ];
    }
}