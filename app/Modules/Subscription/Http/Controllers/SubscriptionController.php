<?php

namespace Modules\Subscription\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Subscription\Services\SubscriptionService;
use Modules\Subscription\Http\Requests\CreateSubscriptionRequest;
use Modules\Subscription\Http\Resources\SubscriptionResource;
use Modules\Subscription\Models\Subscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function __construct(
        private readonly SubscriptionService $subscriptionService,
    ) {}

    /**
     * Get current user's active subscription
     * 
     * @group Subscription
     */
    public function current(Request $request): JsonResponse
    {
        $subscription = $this->subscriptionService->getUserActiveSubscription($request->user()->id);

        if (!$subscription) {
            return $this->success(null, 'No active subscription found');
        }

        return $this->success(
            new SubscriptionResource($subscription),
            'Active subscription retrieved successfully'
        );
    }

    /**
     * Subscribe to a plan
     * 
     * @group Subscription
     */
    public function store(CreateSubscriptionRequest $request): JsonResponse
    {
        $subscription = $this->subscriptionService->subscribe(
            $request->user()->id,
            $request->input('plan_id'),
            $request->input('payment_data', [])
        );

        return $this->created(
            new SubscriptionResource($subscription->load('plan')),
            'Subscription created successfully'
        );
    }

    /**
     * Cancel subscription
     * 
     * @group Subscription
     */
    public function cancel(Subscription $subscription, Request $request): JsonResponse
    {
        // Check if subscription belongs to user
        if ($subscription->user_id !== $request->user()->id) {
            return $this->forbidden('You cannot cancel this subscription');
        }

        $this->subscriptionService->cancel($subscription);

        return $this->success(
            new SubscriptionResource($subscription->fresh()),
            'Subscription cancelled successfully'
        );
    }

    /**
     * Get subscription history
     * 
     * @group Subscription
     */
    public function history(Request $request): JsonResponse
    {
        $subscriptions = $this->subscriptionService->getUserSubscriptionHistory($request->user()->id);

        return $this->success(
            SubscriptionResource::collection($subscriptions),
            'Subscription history retrieved successfully'
        );
    }

    /**
     * Subscribe a user to a plan (Admin only)
     * Useful for testing and manual subscription management
     * 
     * @group Subscription
     */
    public function assignSubscription(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'plan_id' => ['required', 'exists:subscription_plans,id'],
        ]);

        $subscription = $this->subscriptionService->subscribe(
            $request->input('user_id'),
            $request->input('plan_id'),
            []
        );

        return $this->created(
            new SubscriptionResource($subscription->load('plan')),
            'User subscribed successfully'
        );
    }
}
