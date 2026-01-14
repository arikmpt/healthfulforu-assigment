<?php

namespace Modules\Subscription\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Subscription\Services\SubscriptionService;
use Modules\Subscription\Http\Resources\SubscriptionPlanResource;
use Illuminate\Http\JsonResponse;

class SubscriptionPlanController extends Controller
{
    public function __construct(
        private readonly SubscriptionService $subscriptionService,
    ) {}

    /**
     * List available subscription plans
     * 
     * @group Subscription
     */
    public function index(): JsonResponse
    {
        $plans = $this->subscriptionService->getAvailablePlans();

        return $this->success(
            SubscriptionPlanResource::collection($plans),
            'Subscription plans retrieved successfully'
        );
    }
}
