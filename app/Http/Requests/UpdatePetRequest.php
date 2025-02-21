<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePetRequest extends FormRequest
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
            'species' => 'sometimes|string|max:255',
            'name' => 'sometimes|string|max:255',
            'breed' => 'sometimes|string|max:255',
            'color' => 'sometimes|string|max:255',
            'age' => 'sometimes|integer',
            'weight' => 'sometimes|numeric',
            'description' => 'sometimes|string|nullable',
            'gender' => 'sometimes|in:male,female,other',
            'date_of_birth' => 'sometimes|date|nullable',
            'microchip_id' => 'sometimes|string|nullable',
            'insurance_policy_number' => 'sometimes|string|nullable',
            'is_vaccinated' => 'sometimes|boolean',
            'is_neutered' => 'sometimes|boolean',
            'profile_image_url' => 'sometimes|image|mimes:jpg,jpeg,png|max:2048'
        ];
    }
}
