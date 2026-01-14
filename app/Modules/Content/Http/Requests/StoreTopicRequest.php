<?php

namespace Modules\Content\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTopicRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['nullable', 'string', 'max:120', 'unique:topics,slug'],
            'description' => ['nullable', 'string'],
            'icon_url' => ['nullable', 'string', 'url'],
            'type' => ['required', 'in:topic,category,condition'],
            'parent_id' => ['nullable', 'exists:topics,id'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Topic name is required.',
            'type.required' => 'Topic type is required.',
            'type.in' => 'Topic type must be topic, category, or condition.',
            'parent_id.exists' => 'Selected parent topic does not exist.',
        ];
    }
}
