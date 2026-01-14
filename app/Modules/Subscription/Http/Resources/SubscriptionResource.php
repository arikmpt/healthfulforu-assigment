<?php

namespace Modules\Subscription\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'user_id' => $this->user_id,
            'plan' => new SubscriptionPlanResource($this->whenLoaded('plan')),
            'status' => $this->status,
            'starts_at' => $this->starts_at->toISOString(),
            'expires_at' => $this->expires_at->toISOString(),
            'cancelled_at' => $this->cancelled_at?->toISOString(),
            'payment_method' => $this->payment_method,
            'auto_renew' => $this->auto_renew,
            'is_active' => $this->isActive(),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
