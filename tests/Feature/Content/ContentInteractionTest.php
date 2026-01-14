<?php

namespace Tests\Feature\Content;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\Content\Models\Content;
use Modules\Content\Models\ContentInteraction;

class ContentInteractionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'Modules\\Auth\\Database\\Seeders\\RoleSeeder']);
    }

    /** @test */
    public function user_can_like_content()
    {
        $author = User::factory()->create();
        $user = User::factory()->create();

        $content = Content::factory()->create([
            'author_id' => $author->id,
            'status' => 'published',
            'published_at' => now()->subDays(1),
            'likes_count' => 0,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson("/api/v1/content/contents/{$content->id}/like");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'is_liked' => true,
                    'likes_count' => 1,
                ]
            ]);

        $this->assertDatabaseHas('content_interactions', [
            'user_id' => $user->id,
            'content_id' => $content->id,
            'type' => 'like',
        ]);
    }

    /** @test */
    public function user_can_unlike_content()
    {
        $author = User::factory()->create();
        $user = User::factory()->create();

        $content = Content::factory()->create([
            'author_id' => $author->id,
            'status' => 'published',
            'published_at' => now()->subDays(1),
            'likes_count' => 1,
        ]);

        ContentInteraction::create([
            'user_id' => $user->id,
            'content_id' => $content->id,
            'type' => 'like',
            'interacted_at' => now(),
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson("/api/v1/content/contents/{$content->id}/like");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'is_liked' => false,
                    'likes_count' => 0,
                ]
            ]);

        $this->assertDatabaseMissing('content_interactions', [
            'user_id' => $user->id,
            'content_id' => $content->id,
            'type' => 'like',
        ]);
    }

    /** @test */
    public function user_can_bookmark_content()
    {
        $author = User::factory()->create();
        $user = User::factory()->create();

        $content = Content::factory()->create([
            'author_id' => $author->id,
            'status' => 'published',
            'published_at' => now()->subDays(1),
            'bookmarks_count' => 0,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson("/api/v1/content/contents/{$content->id}/bookmark");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'is_bookmarked' => true,
                    'bookmarks_count' => 1,
                ]
            ]);

        $this->assertDatabaseHas('content_interactions', [
            'user_id' => $user->id,
            'content_id' => $content->id,
            'type' => 'bookmark',
        ]);
    }

    /** @test */
    public function user_can_share_content()
    {
        $author = User::factory()->create();
        $user = User::factory()->create();

        $content = Content::factory()->create([
            'author_id' => $author->id,
            'status' => 'published',
            'published_at' => now()->subDays(1),
            'shares_count' => 0,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson("/api/v1/content/contents/{$content->id}/share", [
                'platform' => 'facebook',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('content_interactions', [
            'user_id' => $user->id,
            'content_id' => $content->id,
            'type' => 'share',
        ]);

        $this->assertEquals(1, $content->fresh()->shares_count);
    }

    /** @test */
    public function guest_cannot_interact_with_content()
    {
        $author = User::factory()->create();

        $content = Content::factory()->create([
            'author_id' => $author->id,
            'status' => 'published',
            'published_at' => now()->subDays(1),
        ]);

        $response = $this->postJson("/api/v1/content/contents/{$content->id}/like");
        $response->assertStatus(401);

        $response = $this->postJson("/api/v1/content/contents/{$content->id}/bookmark");
        $response->assertStatus(401);

        $response = $this->postJson("/api/v1/content/contents/{$content->id}/share");
        $response->assertStatus(401);
    }
}
