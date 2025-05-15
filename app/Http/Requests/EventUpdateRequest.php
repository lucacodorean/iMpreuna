<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventUpdateRequest extends FormRequest
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
            "name"          => ["sometimes", "string", "min:3"],
            "description"   => ["sometimes", "string"],
            "location"      => ["sometimes", "string"],
            "banner"        => ["sometimes", "string"],
            "starting_date" => ["sometimes", "date", "date_format:Y-m-d"],
            "end_date"      => ["sometimes", "date", "date_format:Y-m-d"],
            "organizations" => ["sometimes", "array"],
            "organizations.*" => ["sometimes", "exists:organizations,key"],
        ];
    }
}
