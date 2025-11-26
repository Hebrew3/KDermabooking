<x-app-layout>
<x-mobile-header />
<x-admin-sidebar />

<div class="lg:ml-64">
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Staff Service Assignments</h1>
            <p class="text-gray-600 mt-2">Manage which services each staff member can perform</p>
        </div>
        <a href="{{ route('admin.staff-services.create') }}" 
           class="bg-pink-500 hover:bg-pink-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
            <i class="fas fa-plus mr-2"></i>Assign Service
        </a>
    </div>

    <!-- Staff List -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        @foreach($staff as $staffMember)
            <div class="border-b border-gray-200 last:border-b-0">
                <div class="p-6">
                    <!-- Staff Header -->
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-pink-100 rounded-full flex items-center justify-center">
                                <span class="text-pink-600 font-semibold text-lg">
                                    {{ substr($staffMember->first_name, 0, 1) }}{{ substr($staffMember->last_name, 0, 1) }}
                                </span>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">
                                    {{ $staffMember->first_name }} {{ $staffMember->last_name }}
                                </h3>
                                <p class="text-gray-600">{{ $staffMember->email }}</p>
                                <!-- Specializations -->
                                @if($staffMember->staffSpecializations->count() > 0)
                                    <div class="flex flex-wrap gap-1 mt-2">
                                        @foreach($staffMember->staffSpecializations as $spec)
                                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-medium">
                                                {{ ucwords(str_replace('_', ' ', $spec->specialization)) }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-bold text-pink-600">{{ $staffServices->get($staffMember->id, collect())->count() }}</div>
                            <div class="text-sm text-gray-600">Services Assigned</div>
                        </div>
                    </div>

                    <!-- Assigned Services -->
                    @php
                        $memberServices = $staffServices->get($staffMember->id, collect());
                    @endphp
                    @if($memberServices->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($memberServices as $staffService)
                                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                    <div class="flex items-start justify-between mb-2">
                                        <h4 class="font-medium text-gray-900 text-sm">{{ $staffService->service->name }}</h4>
                                        @if($staffService->is_primary)
                                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-medium">
                                                Primary
                                            </span>
                                        @endif
                                    </div>
                                    
                                    <div class="space-y-1 text-xs text-gray-600 mb-3">
                                        <div class="flex justify-between">
                                            <span>Base Price:</span>
                                            <span>{{ $staffService->service->formatted_price }}</span>
                                        </div>
                                        @if($staffService->custom_price)
                                            <div class="flex justify-between text-green-600 font-medium">
                                                <span>Custom Rate:</span>
                                                <span>₱{{ number_format($staffService->custom_price, 2) }}</span>
                                            </div>
                                        @endif
                                        <div class="flex justify-between">
                                            <span>Proficiency:</span>
                                            <span>Level {{ $staffService->proficiency_level }}</span>
                                        </div>
                                    </div>

                                    @if($staffService->notes)
                                        <p class="text-xs text-gray-600 mb-3 italic">{{ $staffService->notes }}</p>
                                    @endif

                                    <div class="flex space-x-2">
                                        <a href="{{ route('admin.staff-services.edit', $staffService->id) }}" 
                                           class="flex-1 bg-blue-100 hover:bg-blue-200 text-blue-800 px-2 py-1 rounded text-xs font-medium text-center transition-colors">
                                            Edit
                                        </a>
                                        <form action="{{ route('admin.staff-services.destroy', $staffService->id) }}" 
                                              method="POST" class="flex-1" onsubmit="return confirmRemoval(event)">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="w-full bg-red-100 hover:bg-red-200 text-red-800 px-2 py-1 rounded text-xs font-medium transition-colors">
                                                Remove
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <p class="mb-4">No services assigned to this staff member</p>
                            <a href="{{ route('admin.staff-services.create') }}?staff_id={{ $staffMember->id }}" 
                               class="bg-pink-500 hover:bg-pink-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                                Assign First Service
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <!-- Pagination -->
    @if($staff->hasPages())
        <div class="mt-6">
            {{ $staff->links() }}
        </div>
    @endif
</div>

<!-- JavaScript for confirmation -->
<script>
    function confirmRemoval(event) {
        event.preventDefault();
        
        Swal.fire({
            title: 'Remove Service Assignment?',
            text: 'This will remove the service assignment from this staff member.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ec4899',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, remove it!'
        }).then((result) => {
            if (result.isConfirmed) {
                event.target.submit();
            }
        });
        
        return false;
    }
</script>
</div>
</div>
</x-app-layout>
