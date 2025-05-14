<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrganizationUpdateRequest extends FormRequest
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
            "name"        => ["sometimes",   "string", "max:255", "unique:organizations,name"],
            "description" => ["sometimes",   "string", "max:255"],
            "address"     => ["sometimes",   "string", "max:255"],
            "tags"        => ["sometimes",   "array"],
            "tags.*"      => ["sometimes",   "string", "exists:tags,key"],
        ];
    }
}
