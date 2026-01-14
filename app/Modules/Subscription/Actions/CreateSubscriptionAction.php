<?php

namespace Modules\Subscription\Actions;

use Modules\Subscription\Models\Subscription;
use Modules\Subscription\Models\SubscriptionPlan;
use Carbon\Carbon;

class CreateSubscriptionAction
{
    public function execute(int $userId, SubscriptionPlan $plan, array $paymentResult): Subscription
    {
        $startsAt = now();
        $expiresAt = $startsAt->copy()->addDays($plan->billing_cycle_days);

        return Subscription::create([
            'user_id' => $userId,
            'subscription_plan_id' => $plan->id,
            'status' => 'active',
            'starts_at' => $startsAt,
            'expires_at' => $expiresAt,
            'payment_method' => $paymentResult['payment_method'],
            'payment_reference' => $paymentResult['payment_reference'],
            'auto_renew' => true,
        ]);
    }
}
