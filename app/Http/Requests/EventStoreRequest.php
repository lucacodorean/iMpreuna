<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventStoreRequest extends FormRequest
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
            "name"          => ["required", "string", "min:3"],
            "description"   => ["required", "string"],
            "location"      => ["required", "string"],
            "banner"        => ["nullable", "string"],
            "starting_at"   => ["required", "date", "date_format:Y-m-d"],
            "ending_at"     => ["required", "date", "date_format:Y-m-d"],
            "organizations" => ["required", "array"],
            "organizations.*" => ["required", "exists:organizations,key"],
        ];
    }
}
