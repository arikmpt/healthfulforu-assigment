<?php

namespace Modules\Subscription\Actions;

use Modules\Subscription\Models\Subscription;

class CancelSubscriptionAction
{
    public function execute(Subscription $subscription): bool
    {
        $subscription->cancel();
        return true;
    }
}
