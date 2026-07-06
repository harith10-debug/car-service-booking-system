<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreServicePackageRequest;
use App\Http\Requests\UpdateServicePackageRequest;
use App\Models\ServicePackage;
use Illuminate\Http\Request;

class ServicePackageController extends Controller
{
    public function index(Request $request)
    {
        $packages = ServicePackage::when($request->filled('search'), function ($query) use ($request) {
                $query->where('package_name', 'like', '%' . $request->search . '%');
            })
            ->when($request->filled('status'), fn($query) => $query->where('status', $request->status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.service_packages.index', compact('packages'));
    }

    public function create()
    {
        return view('admin.service_packages.create');
    }

    public function store(StoreServicePackageRequest $request)
    {
        ServicePackage::create($request->validated());
        return redirect()->route('admin.service-packages.index')->with('success', 'Service package created successfully.');
    }

    public function edit(ServicePackage $servicePackage)
    {
        return view('admin.service_packages.edit', compact('servicePackage'));
    }

    public function update(UpdateServicePackageRequest $request, ServicePackage $servicePackage)
    {
        $servicePackage->update($request->validated());
        return redirect()->route('admin.service-packages.index')->with('success', 'Service package updated successfully.');
    }

    public function destroy(ServicePackage $servicePackage)
    {
        if ($servicePackage->bookings()->exists()) {
            return back()->with('error', 'Package cannot be deleted because it has booking records. Change status to Inactive instead.');
        }

        $servicePackage->delete();
        return back()->with('success', 'Service package deleted successfully.');
    }
}
