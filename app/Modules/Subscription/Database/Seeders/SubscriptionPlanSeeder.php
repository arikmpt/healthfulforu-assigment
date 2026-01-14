<?php

namespace Modules\Subscription\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Subscription\Models\SubscriptionPlan;

class SubscriptionPlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Free',
                'slug' => 'free',
                'description' => 'Access to free content only',
                'price' => 0.00,
                'currency' => 'USD',
                'billing_period' => 'monthly',
                'billing_cycle_days' => 30,
                'is_active' => true,
                'features' => [
                    'Access to free articles and videos',
                    'Basic health tips',
                    'Community support',
                ],
                'sort_order' => 1,
            ],
            [
                'name' => 'Basic',
                'slug' => 'basic',
                'description' => 'Essential health content for individuals',
                'price' => 9.99,
                'currency' => 'USD',
                'billing_period' => 'monthly',
                'billing_cycle_days' => 30,
                'is_active' => true,
                'features' => [
                    'All free content',
                    'Access to premium articles',
                    'Personalized recommendations',
                    'Email support',
                ],
                'sort_order' => 2,
            ],
            [
                'name' => 'Premium',
                'slug' => 'premium',
                'description' => 'Complete health and wellness package',
                'price' => 19.99,
                'currency' => 'USD',
                'billing_period' => 'monthly',
                'billing_cycle_days' => 30,
                'is_active' => true,
                'features' => [
                    'All basic features',
                    'Access to all premium content',
                    'Exclusive videos and webinars',
                    'Advanced personalization',
                    'Priority support',
                    'Downloadable resources',
                ],
                'sort_order' => 3,
            ],
            [
                'name' => 'Annual Premium',
                'slug' => 'annual-premium',
                'description' => 'Best value - Premium features with annual billing',
                'price' => 199.99,
                'currency' => 'USD',
                'billing_period' => 'yearly',
                'billing_cycle_days' => 365,
                'is_active' => true,
                'features' => [
                    'All premium features',
                    'Save $40 per year',
                    'Annual exclusive bonuses',
                    'Extended support',
                ],
                'sort_order' => 4,
            ],
        ];

        foreach ($plans as $planData) {
            SubscriptionPlan::create($planData);
        }

        $this->command->info('Subscription plans seeded successfully: ' . count($plans) . ' plans created.');
    }
}
