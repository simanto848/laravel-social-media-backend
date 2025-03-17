<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->id === (int) $this->input('user_id');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "first_name" => ['required', 'string', 'max:255'],
            "last_name" => ['required', 'string', 'max:255'],
            "phone_number" => ['nullable', 'string', 'max:15', 'unique:profiles'],
            "bio" => ['nullable', 'string'],
            "gender" => ['required', 'in:male,female,other'],
            "dob" => ['required', 'date', 'before:today'],
            'user_id' => ['required', 'exists:users,id']
        ];
    }

    /**
     * Get custom messages for validation errors.
     *
     * @return array
     */
    public function messages(): array {
        return [
            "first_name.required" => "First name is required",
            "last_name.required" => "Last name is required",
            "phone_number.required" => "Phone number already required",
            "phone_number.unique" => "Phone number is already in use",
            "phone_number.max" => "Phone number should not be more than 15 characters",
            'gender.required' => 'Gender is required.',
            'gender.in' => 'Gender must be male, female, or other.',
            'dob.required' => 'Date of birth is required.',
            'dob.before' => 'Date of birth must be a past date.',
            'user_id.required' => 'User ID is required.',
            'user_id.exists' => 'The specified user does not exist.',
        ];
    }
}
