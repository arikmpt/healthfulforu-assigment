<?php

namespace Tests\Feature\Content;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\Content\Models\Content;
use Modules\Subscription\Models\SubscriptionPlan;
use Modules\Subscription\Models\Subscription;

class PremiumAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'Modules\\Auth\\Database\\Seeders\\RoleSeeder']);
    }

    /** @test */
    public function free_user_can_access_free_content()
    {
        $author = User::factory()->create();
        $user = User::factory()->create();

        $content = Content::factory()->create([
            'author_id' => $author->id,
            'access_level' => 'free',
            'status' => 'published',
            'published_at' => now()->subDays(1),
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson("/api/v1/content/contents/{$content->slug}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'slug' => $content->slug,
                    'access_level' => 'free',
                ]
            ]);
    }

    /** @test */
    public function free_user_cannot_access_premium_content()
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

    /** @test */
    public function subscribed_user_can_access_premium_content()
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
                'data' => [
                    'slug' => $content->slug,
                    'access_level' => 'premium',
                ]
            ]);
    }

    /** @test */
    public function expired_subscription_cannot_access_premium_content()
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
            'status' => 'expired',
            'starts_at' => now()->subMonths(2),
            'expires_at' => now()->subMonth(),
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

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'code' => 'PREMIUM_REQUIRED',
            ]);
    }

    /** @test */
    public function guest_cannot_access_any_content()
    {
        $author = User::factory()->create();

        $content = Content::factory()->create([
            'author_id' => $author->id,
            'access_level' => 'free',
            'status' => 'published',
            'published_at' => now()->subDays(1),
        ]);

        $response = $this->getJson("/api/v1/content/contents/{$content->slug}");

        $response->assertStatus(401);
    }
}
