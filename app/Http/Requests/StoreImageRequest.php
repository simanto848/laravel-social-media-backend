<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreImageRequest extends FormRequest
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
            'image' => ['required', 'image', 'max:10240'],
            'imageable_id' => ['required', 'integer'],
            'imageable_type' => ['required', 'string', 'in:App\Models\Profile'], // Type of the model that the image belongs to
            // 'imageable_type' => ['required', 'string', 'in:App\Models\Profile,App\Models\Post'],
        ];
    }
}
