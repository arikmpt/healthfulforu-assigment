<?php

namespace Modules\Content\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTopicRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $topicId = $this->route('topic')->id ?? null;

        return [
            'name' => ['sometimes', 'string', 'max:100'],
            'slug' => ['sometimes', 'string', 'max:120', Rule::unique('topics', 'slug')->ignore($topicId)],
            'description' => ['nullable', 'string'],
            'icon_url' => ['nullable', 'string', 'url'],
            'type' => ['sometimes', 'in:topic,category,condition'],
            'parent_id' => ['nullable', 'exists:topics,id'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'type.in' => 'Topic type must be topic, category, or condition.',
            'parent_id.exists' => 'Selected parent topic does not exist.',
        ];
    }
}
