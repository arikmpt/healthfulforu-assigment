<?php

namespace Modules\Subscription\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Subscription\Models\SubscriptionPlan;
use Illuminate\Support\Str;

class SubscriptionPlanFactory extends Factory
{
    protected $model = SubscriptionPlan::class;

    public function definition(): array
    {
        $billingPeriod = $this->faker->randomElement(['monthly', 'quarterly', 'yearly']);
        $billingCycleDays = match($billingPeriod) {
            'monthly' => 30,
            'quarterly' => 90,
            'yearly' => 365,
        };

        return [
            'uuid' => (string) Str::uuid(),
            'name' => $this->faker->words(2, true) . ' Plan',
            'slug' => $this->faker->unique()->slug(),
            'description' => $this->faker->sentence(),
            'price' => $this->faker->randomFloat(2, 0, 99.99),
            'currency' => 'USD',
            'billing_period' => $billingPeriod,
            'billing_cycle_days' => $billingCycleDays,
            'is_active' => true,
            'features' => [
                $this->faker->sentence(),
                $this->faker->sentence(),
                $this->faker->sentence(),
            ],
            'sort_order' => $this->faker->numberBetween(1, 10),
        ];
    }

    public function free(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Free Plan',
            'slug' => 'free',
            'price' => 0.00,
            'features' => ['Basic features', 'Free content access'],
        ]);
    }

    public function premium(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Premium Plan',
            'slug' => 'premium',
            'price' => 19.99,
            'features' => ['All features', 'Premium content access', 'Priority support'],
        ]);
    }
}
