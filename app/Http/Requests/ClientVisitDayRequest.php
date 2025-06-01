<?php

namespace App\Http\Requests;

use App\Enums\VisitDayEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class ClientVisitDayRequest
 *
 * @package App\Http\Requests
 * 
 * @property int $position
 * @property string $visit_day
 * @property int $client_id
 */
class ClientVisitDayRequest extends FormRequest
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
            'visit_day' => ['required', 'string', Rule::in(VisitDayEnum::getAllowedDays())],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'position' => 'Posición',
            'visit_day' => 'Día de visita',
            'client_id' => 'Cliente',
        ];
    }
}