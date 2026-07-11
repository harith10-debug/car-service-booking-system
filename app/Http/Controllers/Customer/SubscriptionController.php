<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SubscriptionController extends Controller
{
    public function index()
    {
        $plans = SubscriptionPlan::where('status', 'Active')->orderBy('monthly_price')->get();
        $activeSubscription = auth()->user()->activeSubscription()->with('plan')->first();
        $subscriptionHistory = auth()->user()->subscriptions()->with('plan')->latest()->take(8)->get();

        return view('customer.subscriptions.index', compact('plans', 'activeSubscription', 'subscriptionHistory'));
    }

    public function store(Request $request, SubscriptionPlan $subscriptionPlan)
    {
        abort_if($subscriptionPlan->status !== 'Active', 404);

        $data = $request->validate([
            'payment_method' => ['required', Rule::in(Payment::METHODS)],
            'auto_renew' => ['nullable', 'boolean'],
        ]);

        auth()->user()->subscriptions()
            ->where('status', 'Active')
            ->where('ends_at', '>=', now())
            ->update(['status' => 'Expired']);

        UserSubscription::create([
            'user_id' => auth()->id(),
            'subscription_plan_id' => $subscriptionPlan->id,
            'subscription_reference' => $this->makeReference(),
            'starts_at' => now(),
            'ends_at' => $subscriptionPlan->billing_cycle === 'Yearly' ? now()->addYear() : now()->addMonth(),
            'status' => 'Active',
            'amount_paid' => $subscriptionPlan->billing_cycle === 'Yearly' ? ((float) $subscriptionPlan->monthly_price * 12) : $subscriptionPlan->monthly_price,
            'payment_method' => $data['payment_method'],
            'auto_renew' => $request->boolean('auto_renew'),
        ]);

        return redirect()->route('customer.subscriptions.index')
            ->with('success', 'Subscription activated successfully. Benefits will be applied to your next payment.');
    }

    public function cancel(UserSubscription $userSubscription)
    {
        abort_if($userSubscription->user_id !== auth()->id(), 403);
        abort_if($userSubscription->status !== 'Active', 403);

        $userSubscription->update([
            'status' => 'Cancelled',
            'ends_at' => now(),
            'auto_renew' => false,
        ]);

        return redirect()->route('customer.subscriptions.index')->with('success', 'Subscription cancelled successfully.');
    }

    private function makeReference(): string
    {
        do {
            $reference = 'SUB-' . now()->format('Ymd') . '-' . strtoupper(Str::random(8));
        } while (UserSubscription::where('subscription_reference', $reference)->exists());

        return $reference;
    }
}
