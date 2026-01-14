<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\Content\Models\Content;
use Modules\Subscription\Models\SubscriptionPlan;
use Modules\Subscription\Models\Subscription;

class ContentApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'Modules\\Auth\\Database\\Seeders\\RoleSeeder']);
    }

    public function test_can_list_published_content(): void
    {
        $user = User::factory()->create();

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
                'data',
                'meta',
                'links',
            ])
            ->assertJson([
                'success' => true,
            ]);
    }

    public function test_free_user_cannot_access_premium_content(): void
    {
        $author = User::factory()->create();
        $user = User::factory()->create();

        $content = Content::factory()->create([
            'author_id' => $author->id,
            'access_level' => 'premium',
            'status' => 'published',
            'published_at' => now()->subDays(1),
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson("/api/v1/content/contents/{$content->slug}");

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'code' => 'PREMIUM_REQUIRED',
            ]);
    }

    public function test_subscribed_user_can_access_premium_content(): void
    {
        $author = User::factory()->create();
        $user = User::factory()->create();

        $plan = SubscriptionPlan::create([
            'name' => 'Premium',
            'slug' => 'premium',
            'price' => 19.99,
            'currency' => 'USD',
            'billing_period' => 'monthly',
            'billing_cycle_days' => 30,
            'is_active' => true,
        ]);

        Subscription::create([
            'user_id' => $user->id,
            'subscription_plan_id' => $plan->id,
            'status' => 'active',
            'starts_at' => now(),
            'expires_at' => now()->addMonth(),
            'payment_method' => 'mock',
            'payment_reference' => 'TEST_REF',
        ]);

        $content = Content::factory()->create([
            'author_id' => $author->id,
            'access_level' => 'premium',
            'status' => 'published',
            'published_at' => now()->subDays(1),
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson("/api/v1/content/contents/{$content->slug}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }

    public function test_user_can_like_content(): void
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
    }
}
