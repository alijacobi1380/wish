<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendProduct extends FormRequest
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
            'title' => 'required',
            'category' => 'required',
            'pics' => 'required',
            'desc' => 'required',
            'price' => 'required',
            'status' => 'required',
            'eancode' => 'required|digits:13|numeric',
        ];
    }
}
