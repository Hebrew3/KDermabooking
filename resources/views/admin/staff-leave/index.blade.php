<x-app-layout>
    <x-mobile-header />
    <x-admin-sidebar />

    <div class="lg:ml-64">
        <div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-pink-50">
            <!-- Enhanced Header -->
            <div class="bg-white/95 backdrop-blur-sm shadow-md border-b border-gray-200 sticky top-0 z-30">
                <div class="px-4 sm:px-6 lg:px-8 py-4 sm:py-6">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center space-x-3">
                                <div class="hidden sm:flex items-center justify-center w-12 h-12 bg-gradient-to-br from-pink-500 to-rose-600 rounded-xl shadow-lg">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h1 class="text-2xl sm:text-3xl font-extrabold bg-gradient-to-r from-pink-600 via-rose-500 to-pink-600 bg-clip-text text-transparent">
                                        Staff Leave Requests
                                    </h1>
                                    <p class="text-sm sm:text-base text-gray-600 mt-1">Review and manage staff leave requests</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
                @if(session('success'))
                    <div class="mb-6 rounded-xl bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-200 px-4 py-3 text-sm text-green-800 shadow-sm animate-fade-in">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="font-semibold">{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                <!-- Enhanced Status Tabs -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 sm:p-6 mb-6">
                    <div class="flex flex-wrap gap-2 sm:gap-4 border-b-2 border-gray-100 pb-4">
                        <a href="{{ route('admin.staff-leave.index', ['status' => 'pending']) }}" 
                           class="group relative px-4 sm:px-6 py-2.5 sm:py-3 text-sm font-semibold rounded-xl transition-all duration-200 flex items-center space-x-2 {{ $status === 'pending' ? 'bg-gradient-to-r from-yellow-50 to-amber-50 text-yellow-700 border-2 border-yellow-300 shadow-md' : 'text-gray-600 hover:text-yellow-600 hover:bg-yellow-50 border-2 border-transparent' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Pending</span>
                            @if($counts['pending'] > 0)
                                <span class="px-2.5 py-0.5 text-xs font-bold rounded-full {{ $status === 'pending' ? 'bg-yellow-500 text-white' : 'bg-yellow-100 text-yellow-700' }} shadow-sm">
                                    {{ $counts['pending'] }}
                                </span>
                            @endif
                        </a>
                        <a href="{{ route('admin.staff-leave.index', ['status' => 'approved']) }}" 
                           class="group relative px-4 sm:px-6 py-2.5 sm:py-3 text-sm font-semibold rounded-xl transition-all duration-200 flex items-center space-x-2 {{ $status === 'approved' ? 'bg-gradient-to-r from-green-50 to-emerald-50 text-green-700 border-2 border-green-300 shadow-md' : 'text-gray-600 hover:text-green-600 hover:bg-green-50 border-2 border-transparent' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Approved</span>
                            @if($counts['approved'] > 0)
                                <span class="px-2.5 py-0.5 text-xs font-bold rounded-full {{ $status === 'approved' ? 'bg-green-500 text-white' : 'bg-green-100 text-green-700' }} shadow-sm">
                                    {{ $counts['approved'] }}
                                </span>
                            @endif
                        </a>
                        <a href="{{ route('admin.staff-leave.index', ['status' => 'rejected']) }}" 
                           class="group relative px-4 sm:px-6 py-2.5 sm:py-3 text-sm font-semibold rounded-xl transition-all duration-200 flex items-center space-x-2 {{ $status === 'rejected' ? 'bg-gradient-to-r from-red-50 to-rose-50 text-red-700 border-2 border-red-300 shadow-md' : 'text-gray-600 hover:text-red-600 hover:bg-red-50 border-2 border-transparent' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Rejected</span>
                            @if($counts['rejected'] > 0)
                                <span class="px-2.5 py-0.5 text-xs font-bold rounded-full {{ $status === 'rejected' ? 'bg-red-500 text-white' : 'bg-red-100 text-red-700' }} shadow-sm">
                                    {{ $counts['rejected'] }}
                                </span>
                            @endif
                        </a>
                        <a href="{{ route('admin.staff-leave.index', ['status' => 'all']) }}" 
                           class="group relative px-4 sm:px-6 py-2.5 sm:py-3 text-sm font-semibold rounded-xl transition-all duration-200 flex items-center space-x-2 {{ $status === 'all' ? 'bg-gradient-to-r from-blue-50 to-indigo-50 text-blue-700 border-2 border-blue-300 shadow-md' : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50 border-2 border-transparent' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                            <span>All Requests</span>
                            <span class="px-2.5 py-0.5 text-xs font-bold rounded-full {{ $status === 'all' ? 'bg-blue-500 text-white' : 'bg-gray-100 text-gray-700' }} shadow-sm">
                                {{ $counts['all'] }}
                            </span>
                        </a>
                    </div>
                </div>

                <!-- Leave Requests Cards/List -->
                @if($leaveRequests->isEmpty())
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8 sm:p-12">
                        <div class="text-center">
                            <div class="inline-flex items-center justify-center w-20 h-20 sm:w-24 sm:h-24 bg-gradient-to-br from-gray-100 to-gray-200 rounded-2xl mb-6 shadow-inner">
                                <svg class="w-10 h-10 sm:w-12 sm:h-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl sm:text-2xl font-bold text-gray-900 mb-2">No leave requests found</h3>
                            <p class="text-sm sm:text-base text-gray-600 max-w-md mx-auto">
                                @if($status === 'pending')
                                    There are no pending leave requests at the moment. All requests have been processed.
                                @elseif($status === 'approved')
                                    There are no approved leave requests in this view.
                                @elseif($status === 'rejected')
                                    There are no rejected leave requests in this view.
                                @else
                                    No leave requests have been submitted yet.
                                @endif
                            </p>
                        </div>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($leaveRequests as $leave)
                            <div class="bg-white rounded-2xl shadow-md border-2 border-gray-100 hover:shadow-xl hover:border-pink-200 transition-all duration-300 overflow-hidden group">
                                <div class="p-4 sm:p-6">
                                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                                        <!-- Left Section: Staff & Date Info -->
                                        <div class="flex-1 space-y-4">
                                            <div class="flex items-start space-x-4">
                                                <!-- Staff Avatar -->
                                                <div class="flex-shrink-0">
                                                    <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-pink-500 to-rose-600 rounded-xl flex items-center justify-center shadow-lg text-white font-bold text-lg sm:text-xl">
                                                        {{ strtoupper(substr($leave->staff->first_name ?? $leave->staff->name ?? 'S', 0, 1)) }}
                                                    </div>
                                                </div>
                                                
                                                <!-- Staff Details -->
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-center space-x-2 mb-1">
                                                        <h3 class="text-base sm:text-lg font-bold text-gray-900 truncate">
                                                            {{ $leave->staff->full_name ?? $leave->staff->name ?? 'Unknown Staff' }}
                                                        </h3>
                                                        @if($leave->is_emergency)
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700 border border-red-300">
                                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                                                </svg>
                                                                Emergency
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <p class="text-xs sm:text-sm text-gray-500 truncate">{{ $leave->staff->email ?? 'No email' }}</p>
                                                    
                                                    <!-- Date & Time -->
                                                    <div class="flex flex-wrap items-center gap-3 mt-3">
                                                        <div class="flex items-center space-x-2 text-sm">
                                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                            </svg>
                                                            <span class="font-semibold text-gray-700">{{ $leave->unavailable_date->format('M d, Y') }}</span>
                                                            @if($leave->unavailable_date->isPast())
                                                                <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-gray-100 text-gray-600">Past</span>
                                                            @elseif($leave->unavailable_date->isToday())
                                                                <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-yellow-100 text-yellow-700">Today</span>
                                                            @else
                                                                <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-blue-100 text-blue-700">Upcoming</span>
                                                            @endif
                                                        </div>
                                                        @if($leave->formatted_time_range && $leave->formatted_time_range !== 'All Day')
                                                            <div class="flex items-center space-x-2 text-sm text-gray-600">
                                                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                                </svg>
                                                                <span>{{ $leave->formatted_time_range }}</span>
                                                            </div>
                                                        @else
                                                            <div class="flex items-center space-x-2 text-sm text-gray-600">
                                                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                                </svg>
                                                                <span>All Day</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Reason & Notes -->
                                            <div class="flex flex-wrap gap-3 mt-4">
                                                <div class="flex items-center space-x-2">
                                                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-semibold {{ $leave->reason === 'emergency' ? 'bg-red-100 text-red-700 border border-red-300' : 'bg-gray-100 text-gray-700 border border-gray-300' }}">
                                                        {{ $leave->formatted_reason }}
                                                    </span>
                                                </div>
                                                @if($leave->notes)
                                                    <div class="flex items-start space-x-2 text-xs text-gray-600 max-w-md">
                                                        <svg class="w-4 h-4 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                                                        </svg>
                                                        <span class="line-clamp-2">{{ $leave->notes }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            <!-- Requested By -->
                                            <div class="flex items-center space-x-2 text-xs text-gray-500 pt-2 border-t border-gray-100">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                </svg>
                                                <span>
                                                    Requested by {{ $leave->reportedBy->full_name ?? $leave->reportedBy->name ?? 'System' }}
                                                    @if($leave->reported_at)
                                                        on {{ $leave->reported_at->format('M d, Y H:i') }}
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <!-- Right Section: Status & Actions -->
                                        <div class="flex flex-col sm:flex-row lg:flex-col items-start sm:items-center lg:items-end gap-3 lg:gap-4 lg:min-w-[200px]">
                                            <!-- Status Badge -->
                                            <div class="flex-shrink-0">
                                                @if($leave->approval_status === 'pending')
                                                    <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-bold bg-gradient-to-r from-yellow-100 to-amber-100 text-yellow-800 border-2 border-yellow-300 shadow-sm">
                                                        <svg class="w-4 h-4 mr-1.5 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        Pending
                                                    </span>
                                                @elseif($leave->approval_status === 'approved')
                                                    <div class="space-y-1">
                                                        <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-bold bg-gradient-to-r from-green-100 to-emerald-100 text-green-800 border-2 border-green-300 shadow-sm">
                                                            <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                            </svg>
                                                            Approved
                                                        </span>
                                                        @if($leave->approver)
                                                            <p class="text-xs text-gray-500 text-right">
                                                                by {{ $leave->approver->full_name ?? $leave->approver->name }}
                                                            </p>
                                                            @if($leave->approved_at)
                                                                <p class="text-xs text-gray-400 text-right">
                                                                    {{ $leave->approved_at->format('M d, Y') }}
                                                                </p>
                                                            @endif
                                                        @endif
                                                    </div>
                                                @elseif($leave->approval_status === 'rejected')
                                                    <div class="space-y-1">
                                                        <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-bold bg-gradient-to-r from-red-100 to-rose-100 text-red-800 border-2 border-red-300 shadow-sm">
                                                            <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                                            </svg>
                                                            Rejected
                                                        </span>
                                                        @if($leave->approver)
                                                            <p class="text-xs text-gray-500 text-right">
                                                                by {{ $leave->approver->full_name ?? $leave->approver->name }}
                                                            </p>
                                                            @if($leave->approved_at)
                                                                <p class="text-xs text-gray-400 text-right">
                                                                    {{ $leave->approved_at->format('M d, Y') }}
                                                                </p>
                                                            @endif
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            <!-- Action Buttons -->
                                            @if($leave->approval_status === 'pending')
                                                <div class="flex space-x-2 w-full sm:w-auto lg:w-full">
                                                    <form method="POST" action="{{ route('admin.staff-leave.approve', $leave) }}" class="flex-1 sm:flex-none">
                                                        @csrf
                                                        <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 rounded-xl text-xs font-bold bg-gradient-to-r from-green-500 to-emerald-600 text-white border-2 border-green-600 hover:from-green-600 hover:to-emerald-700 shadow-md hover:shadow-lg transition-all duration-200 transform hover:scale-105 active:scale-95">
                                                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                            </svg>
                                                            Approve
                                                        </button>
                                                    </form>
                                                    <form method="POST" action="{{ route('admin.staff-leave.reject', $leave) }}" class="flex-1 sm:flex-none">
                                                        @csrf
                                                        <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 rounded-xl text-xs font-bold bg-gradient-to-r from-red-500 to-rose-600 text-white border-2 border-red-600 hover:from-red-600 hover:to-rose-700 shadow-md hover:shadow-lg transition-all duration-200 transform hover:scale-105 active:scale-95">
                                                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                            </svg>
                                                            Reject
                                                        </button>
                                                    </form>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Enhanced Pagination -->
                    @if($leaveRequests->hasPages())
                        <div class="mt-8 flex justify-center">
                            <div class="bg-white rounded-xl shadow-md border border-gray-100 p-2">
                                {{ $leaveRequests->withQueryString()->links() }}
                            </div>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>

    <style>
        @keyframes fade-in {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate-fade-in {
            animation: fade-in 0.3s ease-out;
        }
    </style>
</x-app-layout>
