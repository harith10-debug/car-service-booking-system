<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $subscriptions = UserSubscription::with(['user', 'plan'])
            ->when($request->filled('customer'), function ($query) use ($request) {
                $query->whereHas('user', fn($q) => $q->where('name', 'like', '%' . $request->customer . '%'));
            })
            ->when($request->filled('status'), fn($query) => $query->where('status', $request->status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.subscriptions.index', [
            'subscriptions' => $subscriptions,
            'statuses' => UserSubscription::STATUSES,
            'activeSubscriptions' => UserSubscription::where('status', 'Active')->where('ends_at', '>=', now())->count(),
            'subscriptionRevenue' => UserSubscription::where('status', 'Active')->sum('amount_paid'),
            'activePlans' => SubscriptionPlan::where('status', 'Active')->count(),
            'totalPlans' => SubscriptionPlan::count(),
        ]);
    }
}
