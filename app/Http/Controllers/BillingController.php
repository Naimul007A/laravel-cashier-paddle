<?php
namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Laravel\Paddle\Cashier;

class BillingController extends Controller {
    public function plans(Request $request) {
        $plans               = Plan::where('slug', '!=', 'free')->get();
        $workspace           = Auth::user()->workspace;
        $subscription        = $workspace->subscription('default');
        $currentPlanPaddleId = null;
        $portalUrl           = null;
        $response            = Cashier::api(
            method: 'POST',
            uri: "customers/{$workspace->customer->paddle_id}/portal-sessions"
        );

        $url = $response['data']['urls']['general']['overview'] ?? null;

        if ($workspace->subscribed('default')) {
            $subscription        = $workspace->subscription('default');
            $currentPlanPaddleId = $subscription->items->first()?->price_id;
            $portalUrl           = $url;
        }

        return Inertia::render('Billing/Plans', [
            'plans'               => $plans,
            'currentPlanPaddleId' => $currentPlanPaddleId,
            'portalUrl'           => $portalUrl,
        ]);
    }
    public function checkout(Request $request) {
        $validated = $request->validate([
            'plan_id'       => 'required|exists:plans,id',
            'billing_cycle' => 'required|in:monthly,yearly',
        ]);

        $plan     = Plan::findOrFail($validated['plan_id']);
        $price_id = $plan->paddle_monthly_price_id;
        if ($validated['billing_cycle'] === 'yearly') {
            $price_id = $plan->paddle_yearly_price_id;
        }
        $workspace = Auth::user()->workspace;
        $checkout  = $workspace->subscribe($price_id, 'default')
            ->returnTo(route('home'));
        return response()->json([
            'checkout'     => $checkout,
            'client_token' => env('PADDLE_CLIENT_SIDE_TOKEN'),
        ]);
    }

}
