<?php

namespace Modules\Subscription\Services;

use Modules\Subscription\Actions\CreateSubscriptionAction;
use Modules\Subscription\Actions\CancelSubscriptionAction;
use Modules\Subscription\Models\Subscription;
use Modules\Subscription\Models\SubscriptionPlan;

class SubscriptionService
{
    public function __construct(
        private readonly CreateSubscriptionAction $createAction,
        private readonly CancelSubscriptionAction $cancelAction,
        private readonly MockPaymentService $paymentService,
    ) {}

    public function getAvailablePlans()
    {
        return SubscriptionPlan::active()
            ->orderBy('sort_order')
            ->get();
    }

    public function subscribe(int $userId, int $planId, array $paymentData): Subscription
    {
        $plan = SubscriptionPlan::findOrFail($planId);

        $paymentResult = $this->paymentService->processPayment($userId, $plan, $paymentData);

        return $this->createAction->execute($userId, $plan, $paymentResult);
    }

    public function cancel(Subscription $subscription): bool
    {
        return $this->cancelAction->execute($subscription);
    }

    public function getUserActiveSubscription(int $userId): ?Subscription
    {
        return Subscription::where('user_id', $userId)
            ->active()
            ->with('plan')
            ->first();
    }

    public function getUserSubscriptionHistory(int $userId)
    {
        return Subscription::where('user_id', $userId)
            ->with('plan')
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
