<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\Subscription\Models\SubscriptionPlan;
use Modules\Subscription\Models\Subscription;

class SubscriptionApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'Modules\\Auth\\Database\\Seeders\\RoleSeeder']);
    }

    public function test_can_list_subscription_plans(): void
    {
        SubscriptionPlan::factory()->count(3)->create(['is_active' => true]);

        $response = $this->getJson('/api/v1/subscription/plans');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'slug',
                        'price',
                        'billing_period',
                    ]
                ]
            ])
            ->assertJsonCount(3, 'data');
    }

    public function test_user_can_subscribe_to_plan(): void
    {
        $user = User::factory()->create();

        $plan = SubscriptionPlan::create([
            'name' => 'Premium',
            'slug' => 'premium',
            'price' => 19.99,
            'currency' => 'USD',
            'billing_period' => 'monthly',
            'billing_cycle_days' => 30,
            'is_active' => true,
            'features' => ['Feature 1', 'Feature 2'],
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/subscription/subscriptions', [
                'plan_id' => $plan->id,
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'user_id' => $user->id,
                    'status' => 'active',
                    'is_active' => true,
                ]
            ]);

        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $user->id,
            'subscription_plan_id' => $plan->id,
            'status' => 'active',
        ]);
    }

    public function test_user_can_cancel_subscription(): void
    {
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

        $subscription = Subscription::create([
            'user_id' => $user->id,
            'subscription_plan_id' => $plan->id,
            'status' => 'active',
            'starts_at' => now(),
            'expires_at' => now()->addMonth(),
            'payment_method' => 'mock',
            'payment_reference' => 'TEST_REF',
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson("/api/v1/subscription/subscriptions/{$subscription->id}/cancel");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'status' => 'cancelled',
                ]
            ]);

        $this->assertDatabaseHas('subscriptions', [
            'id' => $subscription->id,
            'status' => 'cancelled',
        ]);
    }

    public function test_can_assign_subscription_to_user(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

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

        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/v1/subscription/subscriptions/assign', [
                'user_id' => $user->id,
                'plan_id' => $plan->id,
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'user_id' => $user->id,
                    'status' => 'active',
                ]
            ]);

        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $user->id,
            'subscription_plan_id' => $plan->id,
            'status' => 'active',
        ]);
    }

    public function test_regular_user_cannot_assign_subscription(): void
    {
        $user = User::factory()->create();
        $targetUser = User::factory()->create();

        $plan = SubscriptionPlan::create([
            'name' => 'Premium',
            'slug' => 'premium',
            'price' => 19.99,
            'currency' => 'USD',
            'billing_period' => 'monthly',
            'billing_cycle_days' => 30,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/subscription/subscriptions/assign', [
                'user_id' => $targetUser->id,
                'plan_id' => $plan->id,
            ]);

        $response->assertStatus(403);
    }
}
