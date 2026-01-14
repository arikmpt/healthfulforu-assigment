<?php

namespace Modules\Content\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserPreferenceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'topic_id' => ['required', 'integer', 'exists:topics,id'],
            'interest_level' => ['nullable', 'integer', 'min:1', 'max:10'],
        ];
    }

    public function messages(): array
    {
        return [
            'topic_id.required' => 'Topic is required.',
            'topic_id.exists' => 'Selected topic does not exist.',
            'interest_level.min' => 'Interest level must be between 1 and 10.',
            'interest_level.max' => 'Interest level must be between 1 and 10.',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Set default interest level if not provided
        if (!$this->has('interest_level')) {
            $this->merge([
                'interest_level' => 5,
            ]);
        }

        // Set user_id to current user
        $this->merge([
            'user_id' => $this->user()->id,
        ]);
    }
}
