<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ServiceRequest;
use App\Models\Service;
use App\Models\InventoryItem;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    /**
     * Display a listing of services with search and filter functionality.
     */
    public function index(Request $request): View
    {
        $query = Service::query();

        // Search functionality
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Filter by featured
        if ($request->filled('featured')) {
            $query->where('is_featured', $request->featured === '1');
        }

        // Sort by specified column
        $sortBy = $request->get('sort_by', 'sort_order');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        $services = $query->paginate(10)->withQueryString();

        // Get unique categories for filter dropdown
        $categories = Service::distinct()->pluck('category')->filter()->sort()->values();

        return view('admin.services.index', compact('services', 'categories'));
    }

    /**
     * Show the form for creating a new service.
     */
    public function create(): View
    {
        $inventoryItems = InventoryItem::active()->orderBy('name')->get();
        $inventoryItemsJson = $inventoryItems->map(function($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'category' => $item->category,
                'stock' => $item->current_stock,
                'unit' => $item->unit
            ];
        });
        return view('admin.services.create', compact('inventoryItems', 'inventoryItemsJson'));
    }

    /**
     * Store a newly created service in storage.
     */
    public function store(ServiceRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // Handle main image upload
        if ($request->hasFile('image')) {
            /** @var UploadedFile $file */
            $file = $request->file('image');
            $validated['image'] = $file->store('services', 'public');
        }

        // Handle gallery images upload
        if ($request->hasFile('gallery_images')) {
            $galleryImages = [];
            foreach ($request->file('gallery_images') as $file) {
                /** @var UploadedFile $file */
                $galleryImages[] = $file->store('services/gallery', 'public');
            }
            $validated['gallery_images'] = $galleryImages;
        }

        // Convert tags string to array
        if (isset($validated['tags'])) {
            $validated['tags'] = array_filter(array_map('trim', explode(',', $validated['tags'])));
        }

        $service = Service::create($validated);

        // Sync treatment products
        if ($request->has('treatment_products')) {
            $treatmentProducts = [];
            foreach ($request->treatment_products as $product) {
                if (isset($product['product_id']) && isset($product['quantity']) && !empty($product['product_id']) && $product['quantity'] > 0) {
                    $pivotData = ['quantity' => (float)$product['quantity']]; // Support decimal quantities
                    if (isset($product['volume_used_per_service']) && $product['volume_used_per_service'] !== '' && $product['volume_used_per_service'] !== null) {
                        $pivotData['volume_used_per_service'] = (float)$product['volume_used_per_service'];
                    }
                    $treatmentProducts[$product['product_id']] = $pivotData;
                }
            }
            $service->treatmentProducts()->sync($treatmentProducts);
        }

        return redirect()
            ->route('admin.services.show', $service)
            ->with('success', 'Service created successfully!');
    }

    /**
     * Display the specified service.
     */
    public function show(Service $service): View
    {
        return view('admin.services.show', compact('service'));
    }

    /**
     * Show the form for editing the specified service.
     */
    public function edit(Service $service): View
    {
        $service->load('treatmentProducts');
        $inventoryItems = InventoryItem::active()->orderBy('name')->get();
        $inventoryItemsJson = $inventoryItems->map(function($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'category' => $item->category,
                'stock' => $item->current_stock,
                'unit' => $item->unit
            ];
        });
        return view('admin.services.edit', compact('service', 'inventoryItems', 'inventoryItemsJson'));
    }

    /**
     * Update the specified service in storage.
     */
    public function update(ServiceRequest $request, Service $service): RedirectResponse
    {
        $validated = $request->validated();

        // Handle main image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($service->image) {
                Storage::disk('public')->delete($service->image);
            }

            /** @var UploadedFile $file */
            $file = $request->file('image');
            $validated['image'] = $file->store('services', 'public');
        }

        // Handle gallery images upload
        if ($request->hasFile('gallery_images')) {
            // Delete old gallery images if they exist
            if ($service->gallery_images) {
                foreach ($service->gallery_images as $oldImage) {
                    Storage::disk('public')->delete($oldImage);
                }
            }

            $galleryImages = [];
            foreach ($request->file('gallery_images') as $file) {
                /** @var UploadedFile $file */
                $galleryImages[] = $file->store('services/gallery', 'public');
            }
            $validated['gallery_images'] = $galleryImages;
        }

        // Convert tags string to array
        if (isset($validated['tags'])) {
            $validated['tags'] = array_filter(array_map('trim', explode(',', $validated['tags'])));
        }

        $service->update($validated);

        // Sync treatment products
        if ($request->has('treatment_products')) {
            $treatmentProducts = [];
            foreach ($request->treatment_products as $product) {
                if (isset($product['product_id']) && isset($product['quantity']) && !empty($product['product_id']) && $product['quantity'] > 0) {
                    $pivotData = ['quantity' => (float)$product['quantity']]; // Support decimal quantities
                    if (isset($product['volume_used_per_service']) && $product['volume_used_per_service'] !== '' && $product['volume_used_per_service'] !== null) {
                        $pivotData['volume_used_per_service'] = (float)$product['volume_used_per_service'];
                    }
                    $treatmentProducts[$product['product_id']] = $pivotData;
                }
            }
            $service->treatmentProducts()->sync($treatmentProducts);
        } else {
            // If no treatment products submitted, remove all associations
            $service->treatmentProducts()->sync([]);
        }

        return redirect()
            ->route('admin.services.show', $service)
            ->with('success', 'Service updated successfully!');
    }

    /**
     * Remove the specified service from storage.
     */
    public function destroy(Service $service): RedirectResponse
    {
        // Delete main image if exists
        if ($service->image) {
            Storage::disk('public')->delete($service->image);
        }

        // Delete gallery images if they exist
        if ($service->gallery_images) {
            foreach ($service->gallery_images as $galleryImage) {
                Storage::disk('public')->delete($galleryImage);
            }
        }

        $service->delete();

        return redirect()
            ->route('admin.services.index')
            ->with('success', 'Service deleted successfully!');
    }

    /**
     * Toggle service status (activate/deactivate).
     */
    public function toggleStatus(Service $service): RedirectResponse
    {
        $service->is_active = !$service->is_active;
        $service->save();

        $status = $service->is_active ? 'activated' : 'deactivated';

        return redirect()
            ->route('admin.services.index')
            ->with('success', "Service {$status} successfully!");
    }

    /**
     * Toggle service featured status.
     */
    public function toggleFeatured(Service $service): RedirectResponse
    {
        $service->is_featured = !$service->is_featured;
        $service->save();

        $status = $service->is_featured ? 'featured' : 'unfeatured';

        return redirect()
            ->route('admin.services.index')
            ->with('success', "Service {$status} successfully!");
    }

    /**
     * Export services to CSV.
     */
    public function export(Request $request)
    {
        $query = Service::query();

        // Apply same filters as index
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $services = $query->get();

        $filename = 'services_export_' . now()->format('Y_m_d_H_i_s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($services) {
            $file = fopen('php://output', 'w');

            // CSV header
            fputcsv($file, [
                'ID', 'Name', 'Description', 'Price', 'Duration (minutes)', 'Category',
                'Is Active', 'Is Featured', 'Sort Order', 'Created At'
            ]);

            // CSV data
            foreach ($services as $service) {
                fputcsv($file, [
                    $service->id,
                    $service->name,
                    $service->description,
                    $service->price,
                    $service->duration,
                    $service->category,
                    $service->is_active ? 'Yes' : 'No',
                    $service->is_featured ? 'Yes' : 'No',
                    $service->sort_order,
                    $service->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
