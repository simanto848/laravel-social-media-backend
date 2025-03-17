<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
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
            'first_name' => ['sometimes', 'required', 'string', 'max:255'],
            'last_name' => ['sometimes', 'required', 'string', 'max:255'],
            'phone_number' => ['sometimes', 'required', 'string', 'max:20', 'unique:profiles,phone_number,' . $this->profile->id],
            'bio' => ['nullable', 'string'],
            'gender' => ['sometimes', 'required', 'in:male,female,other'],
            'dob' => ['sometimes', 'required', 'date', 'before:today'],
        ];
    }

    /**
     * Get custom messages for validation errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'first_name.required' => 'First name is required.',
            'last_name.required' => 'Last name is required.',
            'phone_number.required' => 'Phone number is required.',
            'phone_number.unique' => 'This phone number is already in use.',
            'gender.required' => 'Gender is required.',
            'gender.in' => 'Gender must be male, female, or other.',
            'dob.required' => 'Date of birth is required.',
            'dob.before' => 'Date of birth must be a past date.',
        ];
    }
}
