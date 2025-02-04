<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateJobRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title'          => 'nullable|string|max:255',
            'description'    => 'nullable|string|max:1000',
            'location'       => 'nullable|string|max:255',
            'category'       => 'nullable|string|max:255',
            'benefits'       => 'nullable|string|max:1000',
            'salary'         => 'nullable|string|max:255',
            'type'           => 'nullable|string|max:255', 
            'work_condition' => 'nullable|string|max:255',
        ];
    }
}
