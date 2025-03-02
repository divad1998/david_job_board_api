<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateJobRequest extends FormRequest
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
            'title'          => 'required|string|max:255',
            'description'    => 'required|string|max:1000',
            'location'       => 'required|string|max:255',
            'category'       => 'required|string|max:255',
            'benefits'       => 'required|string|max:1000',
            'salary'         => 'required|string|max:255',
            'type'           => 'required|string|max:255', 
            'work_condition' => 'required|string|max:255',
        ];
    }
}
