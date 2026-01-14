<?php

namespace Modules\Content\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:contents,slug'],
            'summary' => ['nullable', 'string', 'max:1000'],
            'body' => ['nullable', 'string'],
            'video_url' => ['nullable', 'string', 'url'],
            'thumbnail_url' => ['nullable', 'string', 'url'],
            'type' => ['required', 'in:article,video'],
            'access_level' => ['required', 'in:free,premium'],
            'status' => ['required', 'in:draft,published,archived'],
            'published_at' => ['nullable', 'date'],
            'duration_minutes' => ['nullable', 'integer', 'min:1'],
            'read_time_minutes' => ['nullable', 'integer', 'min:1'],
            'metadata' => ['nullable', 'array'],
            'topic_ids' => ['nullable', 'array'],
            'topic_ids.*' => ['exists:topics,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Content title is required.',
            'type.required' => 'Content type is required.',
            'type.in' => 'Content type must be either article or video.',
            'access_level.required' => 'Access level is required.',
            'access_level.in' => 'Access level must be either free or premium.',
            'status.required' => 'Status is required.',
            'topic_ids.*.exists' => 'One or more selected topics do not exist.',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Set author_id to current user if not provided
        if (!$this->has('author_id')) {
            $this->merge([
                'author_id' => $this->user()->id,
            ]);
        }
    }
}
