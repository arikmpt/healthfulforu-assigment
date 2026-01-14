<?php

namespace Tests\Feature\Content;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\Content\Models\Content;
use Modules\Content\Models\Topic;

class ContentListTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'Modules\\Auth\\Database\\Seeders\\RoleSeeder']);
    }

    /** @test */
    public function it_can_list_published_content()
    {
        $user = User::factory()->create();
        $topic = Topic::create([
            'name' => 'Test Topic',
            'slug' => 'test-topic',
            'type' => 'topic',
            'is_active' => true,
        ]);

        Content::factory()->count(5)->create([
            'author_id' => $user->id,
            'status' => 'published',
            'published_at' => now()->subDays(1),
        ]);

        $response = $this->getJson('/api/v1/content/contents');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'uuid',
                        'title',
                        'slug',
                        'type',
                        'access_level',
                        'status',
                    ]
                ],
                'meta',
                'links',
            ])
            ->assertJson([
                'success' => true,
            ]);
    }

    /** @test */
    public function it_can_filter_content_by_type()
    {
        $user = User::factory()->create();

        Content::factory()->count(3)->create([
            'author_id' => $user->id,
            'type' => 'article',
            'status' => 'published',
            'published_at' => now()->subDays(1),
        ]);

        Content::factory()->count(2)->create([
            'author_id' => $user->id,
            'type' => 'video',
            'status' => 'published',
            'published_at' => now()->subDays(1),
        ]);

        $response = $this->getJson('/api/v1/content/contents?type=article');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    /** @test */
    public function it_can_filter_content_by_access_level()
    {
        $user = User::factory()->create();

        Content::factory()->count(3)->create([
            'author_id' => $user->id,
            'access_level' => 'free',
            'status' => 'published',
            'published_at' => now()->subDays(1),
        ]);

        Content::factory()->count(2)->create([
            'author_id' => $user->id,
            'access_level' => 'premium',
            'status' => 'published',
            'published_at' => now()->subDays(1),
        ]);

        $response = $this->getJson('/api/v1/content/contents?access_level=free');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    /** @test */
    public function it_only_shows_published_content()
    {
        $user = User::factory()->create();

        Content::factory()->create([
            'author_id' => $user->id,
            'status' => 'published',
            'published_at' => now()->subDays(1),
        ]);

        Content::factory()->create([
            'author_id' => $user->id,
            'status' => 'draft',
        ]);

        $response = $this->getJson('/api/v1/content/contents');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }
}
