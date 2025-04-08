<?php

namespace App\Http\Requests;

use App\Enums\VisitDayEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClientRequest extends FormRequest
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
            'first_name' => 'required|string|max:64',
            'last_name' => 'required|string|max:64',
            'address' => 'required|string|max:128',
            'phone_number' => 'required|string|max:8',
            'department' => 'required|string|max:32',
            'township' => 'required|string|max:32',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'business_name' => 'required|string|max:50',
            'position' => 'required|integer|min:1|max:999',
            'visit_day' => ['required', 'string', 'max:10', Rule::in(VisitDayEnum::getAllowedDays())],
        ];
    }
}
