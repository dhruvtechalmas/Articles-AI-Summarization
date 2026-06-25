<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'title' => 'required|string|max:255',

            'url' => 'required|url|unique:articles,url',

        ];
    }

    public function messages(): array
    {
        return [

            'title.required' => 'Please enter article title.',

            'url.required' => 'Please enter article url.',

            'url.url' => 'Please enter valid url.',

            'url.unique' => 'This article already exists.',

        ];
    }
}