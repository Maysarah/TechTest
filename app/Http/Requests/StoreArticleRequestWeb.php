<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreArticleRequestWeb extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Adjust if you have authorization logic
    }

    public function rules(): array
    {
        return [
            'title' => 'required|max:100',
            'content' => 'required',
            'images.*' => 'nullable|image|mimes:jpg,png,jpeg,gif|max:2048',
        ];
    }

}
