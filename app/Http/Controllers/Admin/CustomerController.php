<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $customers = User::where('role', 'customer')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->withCount(['vehicles', 'bookings'])
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.customers.index', compact('customers'));
    }

    public function destroy(User $customer)
    {
        abort_if($customer->role !== 'customer', 403);

        if ($customer->bookings()->exists() || $customer->vehicles()->exists()) {
            return back()->with('error', 'Customer cannot be deleted because related records exist.');
        }

        $customer->delete();
        return back()->with('success', 'Customer deleted successfully.');
    }
}
