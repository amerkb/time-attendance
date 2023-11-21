<?php

namespace App\Http\Requests\Posts;

use Illuminate\Foundation\Http\FormRequest;


class CreateSharePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'post_id' => 'required|exists:posts,id',
            'content' => 'nullable|string|max:500',
        ];
    }
}
