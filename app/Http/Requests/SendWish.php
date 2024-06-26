<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendWish extends FormRequest
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
            'minidesc' => 'required',
            'desc' => 'required',
            'importance' => 'required',
            'wishfiles.*' => 'mimes:jpeg,jpg,png,pdf'
        ];
    }
}
