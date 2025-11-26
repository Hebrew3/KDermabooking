<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Display a listing of services for clients.
     */
    public function index(Request $request)
    {
        $query = Service::active();

        // Filter by category
        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        // Search functionality
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter by price range
        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }

        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }

        $services = $query->orderBy('sort_order')
                         ->orderBy('name')
                         ->paginate(12);

        // Get available categories
        $categories = Service::active()
            ->select('category')
            ->distinct()
            ->whereNotNull('category')
            ->pluck('category')
            ->sort();

        // Get featured services
        $featuredServices = Service::active()
            ->featured()
            ->orderBy('sort_order')
            ->limit(6)
            ->get();

        return view('client.services.index', compact('services', 'categories', 'featuredServices'));
    }

    /**
     * Display the specified service.
     */
    public function show(Service $service)
    {
        // Ensure service is active
        if (!$service->is_active) {
            abort(404);
        }

        // Get related services (same category)
        $relatedServices = Service::active()
            ->where('category', $service->category)
            ->where('id', '!=', $service->id)
            ->limit(4)
            ->get();

        return view('client.services.show', compact('service', 'relatedServices'));
    }
}
