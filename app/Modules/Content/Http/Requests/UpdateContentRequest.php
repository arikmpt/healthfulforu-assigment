<?php

namespace Modules\Content\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateContentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $contentId = $this->route('content')->id ?? null;

        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'slug' => ['sometimes', 'string', 'max:255', Rule::unique('contents', 'slug')->ignore($contentId)],
            'summary' => ['nullable', 'string', 'max:1000'],
            'body' => ['nullable', 'string'],
            'video_url' => ['nullable', 'string', 'url'],
            'thumbnail_url' => ['nullable', 'string', 'url'],
            'type' => ['sometimes', 'in:article,video'],
            'access_level' => ['sometimes', 'in:free,premium'],
            'status' => ['sometimes', 'in:draft,published,archived'],
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
            'type.in' => 'Content type must be either article or video.',
            'access_level.in' => 'Access level must be either free or premium.',
            'status.in' => 'Status must be draft, published, or archived.',
            'topic_ids.*.exists' => 'One or more selected topics do not exist.',
        ];
    }
}
