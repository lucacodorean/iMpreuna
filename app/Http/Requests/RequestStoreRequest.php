<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RequestStoreRequest extends FormRequest {

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "description" => ["required", "string"],
            "tags"        => ["nullable", "array"],
            "tags.*"      => ["nullable", "exists:tags,key"],
        ];
    }
}
