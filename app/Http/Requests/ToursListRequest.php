<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ToursListRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'priceFrom' => 'numeric',
            'priceTo' => 'numeric',
            'dateFrom' => 'date',
            'dateTo' => 'date',
            'sortBy' => Rule::in(['price']),
            'orderBy' => Rule::in(['asc', 'desc']),
        ];
    }

    public function messages(): array
    {
        return [
            'sortBy' => 'The "sortBy" field accepts only "price" value.',
            'orderBy' => 'The "orderBy" field accepts only "asc" or "desc" value.',
        ];
    }
}
