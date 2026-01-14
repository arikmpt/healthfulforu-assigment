<?php

namespace Modules\Subscription\Services;

use Modules\Subscription\Models\SubscriptionPlan;

class MockPaymentService
{
    public function processPayment(int $userId, SubscriptionPlan $plan, array $paymentData): array
    {
        return [
            'success' => true,
            'payment_reference' => 'MOCK_' . uniqid(),
            'payment_method' => 'mock',
            'amount' => $plan->price,
            'currency' => $plan->currency,
            'processed_at' => now(),
        ];
    }

    public function refundPayment(string $paymentReference): bool
    {
        return true;
    }
}
