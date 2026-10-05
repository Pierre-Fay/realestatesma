<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PropertyBrowseRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'q' => ['nullable', 'string', 'max:100'],
            'type' => ['nullable', 'integer', 'min:1'],
            'area' => ['nullable', 'integer', 'min:1'],
            'bedrooms' => ['nullable', 'integer', 'min:0', 'max:20'],
            'price_min' => ['nullable', 'numeric', 'min:0'],
            'price_max' => ['nullable', 'numeric', 'min:0'],
            'sort' => ['nullable', 'in:newest,price_asc,price_desc'],
        ];
    }
}
