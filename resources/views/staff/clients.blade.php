@php
    use Illuminate\Support\Facades\Storage;
@endphp

<x-app-layout>
<x-mobile-header />

    <!-- Include Staff Sidebar -->
    <x-staff-sidebar />

    <!-- Main Content -->
    <div class="lg:ml-64">
        <div class="p-8">
            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">My Clients</h1>
                <p class="text-gray-600 mt-2">Manage your client relationships</p>
            </div>

            <!-- Search and Filters -->
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <form method="GET" action="{{ route('staff.clients') }}" class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1">
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Search clients by name, email, or phone..."
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                    </div>
                    <button type="submit" class="bg-pink-500 hover:bg-pink-600 text-white px-6 py-2 rounded-lg transition-colors duration-200">
                        Search
                    </button>
                </form>
            </div>

            <!-- Client List -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">Client List ({{ $clients->total() }} total)</h2>
                </div>

                @if($clients->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Appointments</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Completed</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($clients as $client)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            @if($client->profile_picture)
                                                <img src="{{ $client->profile_picture_url ?? asset('storage/' . $client->profile_picture) }}" alt="{{ $client->name }}" class="h-10 w-10 rounded-full object-cover">
                                            @else
                                                <div class="h-10 w-10 rounded-full bg-pink-100 flex items-center justify-center">
                                                    <span class="text-sm font-medium text-pink-600">{{ substr($client->first_name, 0, 1) }}{{ substr($client->last_name, 0, 1) }}</span>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $client->name }}</div>
                                            <div class="text-sm text-gray-500">{{ ucfirst($client->gender) }}, {{ $client->birth_date ? \Carbon\Carbon::parse($client->birth_date)->age : 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $client->email }}</div>
                                    <div class="text-sm text-gray-500">{{ $client->mobile_number }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $client->total_appointments }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $client->completed_appointments }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('staff.clients.show', $client) }}" class="text-blue-600 hover:text-blue-900">View Details</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $clients->links() }}
                </div>
                @else
                <div class="text-center py-12">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <p class="text-gray-500">No clients found</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
