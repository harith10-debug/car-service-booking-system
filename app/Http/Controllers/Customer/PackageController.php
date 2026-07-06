<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\ServicePackage;

class PackageController extends Controller
{
    public function index()
    {
        $packages = ServicePackage::where('status', 'Active')->latest()->paginate(9);
        return view('customer.packages.index', compact('packages'));
    }
}
