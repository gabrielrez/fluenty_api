<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubscriptionRequest;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function plans()
    {
        return $this->respond([
            'plans' => [
                [
                    'name' => 'Mensal',
                    'price_id' => config('services.stripe.price_monthly'),
                    'amount' => 3790,
                    'interval' => 'month',
                ],
                [
                    'name' => 'Anual',
                    'price_id' => config('services.stripe.price_yearly'),
                    'amount' => 39790,
                    'interval' => 'year',
                ],
            ],
        ]);
    }

    public function checkout(SubscriptionRequest $request)
    {
        $user = $request->user();

        if ($user->subscribed('default')) {
            $this->failConflict('User already has an active subscription. Use the billing portal to manage your plan.');
        }

        $priceId = $request->input('price_id');

        $checkout = $user->newSubscription('default', $priceId)->checkout([
            'success_url' => $request->input('success_url'),
            'cancel_url' => $request->input('cancel_url'),
        ]);

        return $this->respond([
            'checkout_url' => $checkout->url,
        ]);
    }

    public function status(Request $request)
    {
        $user = $request->user();

        if (! $user->subscribed('default')) {
            return $this->respond([
                'subscribed' => false,
            ]);
        }

        $subscription = $user->subscription('default');

        return $this->respond([
            'subscribed' => true,
            'stripe_status' => $subscription->stripe_status,
            'stripe_price' => $subscription->stripe_price,
            'on_grace_period' => $subscription->onGracePeriod(),
            'ends_at' => $subscription->ends_at,
            'created_at' => $subscription->created_at,
        ]);
    }

    public function billingPortal(Request $request)
    {
        $user = $request->user();

        if (! $user->stripe_id) {
            $this->failBadRequest('No billing history found. Subscribe to a plan first.');
        }

        $url = $user->billingPortalUrl(
            $request->input('return_url', config('app.url'))
        );

        return $this->respond([
            'billing_portal_url' => $url,
        ]);
    }
}
