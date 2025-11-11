<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'course' => ['nullable', 'string', 'max:100'],
            'year' => ['nullable', 'integer', 'min:1', 'max:6'],
        ];
    }

    /**
     * Get custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'course.string' => 'The course must be a valid text.',
            'course.max' => 'The course may not be greater than 100 characters.',
            'year.integer' => 'The year level must be a number.',
            'year.min' => 'The year level must be at least 1.',
            'year.max' => 'The year level may not be greater than 6.',
        ];
    }
}
