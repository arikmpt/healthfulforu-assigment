<?php

namespace Modules\Content\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Content\Models\Content;
use Illuminate\Support\Str;

class ContentFactory extends Factory
{
    protected $model = Content::class;

    public function definition(): array
    {
        $type = $this->faker->randomElement(['article', 'video']);

        return [
            'uuid' => (string) Str::uuid(),
            'title' => $this->faker->sentence(),
            'slug' => $this->faker->unique()->slug(),
            'summary' => $this->faker->paragraph(),
            'body' => $type === 'article' ? $this->faker->paragraphs(5, true) : null,
            'video_url' => $type === 'video' ? 'https://www.youtube.com/watch?v=' . Str::random(11) : null,
            'thumbnail_url' => $this->faker->imageUrl(800, 450),
            'type' => $type,
            'access_level' => $this->faker->randomElement(['free', 'premium']),
            'status' => 'draft',
            'published_at' => null,
            'duration_minutes' => $type === 'video' ? $this->faker->numberBetween(5, 60) : null,
            'read_time_minutes' => $type === 'article' ? $this->faker->numberBetween(3, 15) : null,
            'views_count' => 0,
            'likes_count' => 0,
            'shares_count' => 0,
            'bookmarks_count' => 0,
            'metadata' => null,
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'published',
            'published_at' => now()->subDays(rand(1, 30)),
        ]);
    }

    public function free(): static
    {
        return $this->state(fn (array $attributes) => [
            'access_level' => 'free',
        ]);
    }

    public function premium(): static
    {
        return $this->state(fn (array $attributes) => [
            'access_level' => 'premium',
        ]);
    }

    public function article(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'article',
            'video_url' => null,
            'duration_minutes' => null,
            'body' => $this->faker->paragraphs(5, true),
            'read_time_minutes' => $this->faker->numberBetween(3, 15),
        ]);
    }

    public function video(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'video',
            'body' => null,
            'read_time_minutes' => null,
            'video_url' => 'https://www.youtube.com/watch?v=' . Str::random(11),
            'duration_minutes' => $this->faker->numberBetween(5, 60),
        ]);
    }
}
