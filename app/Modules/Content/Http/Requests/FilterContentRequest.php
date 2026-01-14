<?php

namespace Modules\Content\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FilterContentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['nullable', 'in:article,video'],
            'access_level' => ['nullable', 'in:free,premium'],
            'topic_id' => ['nullable', 'exists:topics,id'],
            'search' => ['nullable', 'string', 'max:255'],
            'sort_by' => ['nullable', 'in:published_at,views_count,likes_count,created_at,title'],
            'sort_order' => ['nullable', 'in:asc,desc'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'type.in' => 'Type must be either article or video.',
            'access_level.in' => 'Access level must be either free or premium.',
            'topic_id.exists' => 'Selected topic does not exist.',
            'sort_by.in' => 'Invalid sort field.',
            'sort_order.in' => 'Sort order must be asc or desc.',
        ];
    }
}
