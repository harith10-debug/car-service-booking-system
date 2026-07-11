<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SubscriptionPlanController extends Controller
{
    public function index(Request $request)
    {
        $plans = SubscriptionPlan::withCount('subscriptions')
            ->when($request->filled('search'), fn($query) => $query->where('plan_name', 'like', '%' . $request->search . '%'))
            ->when($request->filled('status'), fn($query) => $query->where('status', $request->status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.subscription_plans.index', compact('plans'));
    }

    public function create()
    {
        return view('admin.subscription_plans.create');
    }

    public function store(Request $request)
    {
        SubscriptionPlan::create($this->validatedData($request));

        return redirect()->route('admin.subscription-plans.index')->with('success', 'Subscription plan created successfully.');
    }

    public function edit(SubscriptionPlan $subscriptionPlan)
    {
        return view('admin.subscription_plans.edit', compact('subscriptionPlan'));
    }

    public function update(Request $request, SubscriptionPlan $subscriptionPlan)
    {
        $subscriptionPlan->update($this->validatedData($request));

        return redirect()->route('admin.subscription-plans.index')->with('success', 'Subscription plan updated successfully.');
    }

    public function destroy(SubscriptionPlan $subscriptionPlan)
    {
        if ($subscriptionPlan->subscriptions()->exists()) {
            return back()->with('error', 'Subscription plan cannot be deleted because subscribers exist. Set it to Inactive instead.');
        }

        $subscriptionPlan->delete();
        return back()->with('success', 'Subscription plan deleted successfully.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'plan_name' => ['required', 'string', 'max:120', Rule::unique('subscription_plans', 'plan_name')->ignore($request->route('subscriptionPlan'))],
            'description' => ['nullable', 'string', 'max:2000'],
            'monthly_price' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'billing_cycle' => ['required', 'in:Monthly,Yearly'],
            'discount_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'priority_level' => ['required', 'integer', 'min:1', 'max:10'],
            'benefits' => ['nullable', 'string', 'max:3000'],
            'status' => ['required', 'in:Active,Inactive'],
        ]);
    }
}
