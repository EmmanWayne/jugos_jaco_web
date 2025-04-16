<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class ImageRequest
 *
 * @package App\Http\Requests
 *
 * @method bool filled(string|array $keys) Determine if the request contains a non-empty value for a given key
 * @method bool has(string|array $keys) Determine if the request contains a non-empty value for a given key
 * @method bool hasFile(string $key) Determine if the request contains a file for a given key
 * @method \Illuminate\Http\UploadedFile|null file(string $key) Get a file from the request
 * 
 * @property image $image
 **/
class ImageRequest extends FormRequest
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
            'image' => 'required|image|mimes:jpeg,png,jpg|max:512'
        ];
    }
}
