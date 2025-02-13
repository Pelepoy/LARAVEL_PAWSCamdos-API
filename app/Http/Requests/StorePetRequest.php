<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePetRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'breed' => 'required|string|max:255',
            'color' => 'required|string|max:255',
            'age' => 'required|integer|min:0',
            'weight' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'gender' => 'required|in:male,female',
            'date_of_birth' => 'nullable|date',
            'microchip_id' => 'nullable|string|unique:dogs_info,microchip_id',
            'insurance_policy_number' => 'nullable|string|max:255',
            'is_vaccinated' => 'required|boolean',
            'is_neutered' => 'required|boolean',
            'profile_image_url' => 'nullable|image|mimes:png,jpg,gif,jpeg,webp|max:2048',
        ];
    }

    // public function messages()
    // {
    //     return [
    //         'microchip_id.unique' => 'The microchip id must be unique -pelep.'
    //     ];
    // }

    // public function failedValidation(Validator $validator)
    // {
    //     throw new HttpResponseException(response()->json([
    //         'status' => 'error',
    //         'message' => 'Validation errors',
    //         'errors' => $validator->errors()
    //     ], 422));
    // }
}
