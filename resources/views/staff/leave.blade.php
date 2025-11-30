<x-app-layout>
    <x-mobile-header />

    <!-- Include Staff Sidebar -->
    <x-staff-sidebar />

    <div class="lg:ml-64">
        <div class="p-8">
            <h1 class="text-2xl font-bold text-gray-900 mb-4">My Leave Requests</h1>

            @if(session('success'))
                <div class="mb-4 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid lg:grid-cols-2 gap-6">
                <!-- Leave request form -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Request Leave</h2>
                    <form method="POST" action="{{ route('staff.leave.store') }}" id="leaveRequestForm">
                        @csrf

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                                <input type="date" name="unavailable_date" value="{{ old('unavailable_date') }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500 text-sm">
                                @error('unavailable_date')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Start Time (optional)</label>
                                    <input type="time" name="start_time" value="{{ old('start_time') }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500 text-sm">
                                    @error('start_time')
                                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">End Time (optional)</label>
                                    <input type="time" name="end_time" value="{{ old('end_time') }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500 text-sm">
                                    @error('end_time')
                                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Reason</label>
                                <select name="reason" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500 text-sm">
                                    @foreach($reasonTypes as $key => $label)
                                        <option value="{{ $key }}" {{ old('reason') === $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('reason')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Notes (optional)</label>
                                <textarea name="notes" rows="3" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500 text-sm">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <button type="submit" id="submitLeaveBtn" class="bg-pink-500 hover:bg-pink-600 text-white px-4 py-2 rounded-lg text-sm font-semibold">Submit Leave Request</button>
                        </div>
                    </form>
                </div>

                <!-- Leave requests list -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Recent Requests</h2>

                    @if($leaveRequests->isEmpty())
                        <p class="text-sm text-gray-500">You have no leave requests yet.</p>
                    @else
                        <div class="space-y-3 max-h-[480px] overflow-y-auto pr-1">
                            @foreach($leaveRequests as $leave)
                                <div class="border border-gray-100 rounded-lg px-4 py-3 flex items-start justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-800">
                                            {{ $leave->unavailable_date->format('M d, Y') }}
                                            <span class="text-xs text-gray-500 ml-2">{{ $leave->formatted_time_range }}</span>
                                        </p>
                                        <p class="text-xs text-gray-500 mt-1">Reason: {{ $leave->formatted_reason }}</p>
                                        @if($leave->notes)
                                            <p class="text-xs text-gray-500 mt-1">{{ $leave->notes }}</p>
                                        @endif
                                    </div>
                                    <div class="ml-4 text-right">
                                        @if($leave->isPending())
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-50 text-yellow-700 border border-yellow-200">Pending</span>
                                        @elseif($leave->isApproved())
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-200">Approved</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-50 text-red-700 border border-red-200">Rejected</span>
                                        @endif
                                        @if($leave->approved_at)
                                            <p class="text-[10px] text-gray-400 mt-1">Updated {{ $leave->approved_at->format('M d, Y H:i') }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('leaveRequestForm');
            const submitBtn = document.getElementById('submitLeaveBtn');

            if (form && submitBtn) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    // Get form values for display
                    const date = document.querySelector('input[name="unavailable_date"]').value;
                    const startTime = document.querySelector('input[name="start_time"]').value || 'All day';
                    const endTime = document.querySelector('input[name="end_time"]').value || '';
                    const reason = document.querySelector('select[name="reason"]').selectedOptions[0].text;
                    const notes = document.querySelector('textarea[name="notes"]').value;

                    // Format date for display
                    const formattedDate = date ? new Date(date).toLocaleDateString('en-US', { 
                        year: 'numeric', 
                        month: 'long', 
                        day: 'numeric' 
                    }) : 'Not specified';

                    // Format time range
                    let timeRange = 'All day';
                    if (startTime && endTime) {
                        const start = new Date('2000-01-01T' + startTime).toLocaleTimeString('en-US', { 
                            hour: 'numeric', 
                            minute: '2-digit',
                            hour12: true 
                        });
                        const end = new Date('2000-01-01T' + endTime).toLocaleTimeString('en-US', { 
                            hour: 'numeric', 
                            minute: '2-digit',
                            hour12: true 
                        });
                        timeRange = `${start} - ${end}`;
                    } else if (startTime) {
                        const start = new Date('2000-01-01T' + startTime).toLocaleTimeString('en-US', { 
                            hour: 'numeric', 
                            minute: '2-digit',
                            hour12: true 
                        });
                        timeRange = `From ${start}`;
                    }

                    // Build confirmation message
                    let message = `<div class="text-left">
                        <p class="mb-2"><strong>Date:</strong> ${formattedDate}</p>
                        <p class="mb-2"><strong>Time:</strong> ${timeRange}</p>
                        <p class="mb-2"><strong>Reason:</strong> ${reason}</p>`;
                    
                    if (notes) {
                        message += `<p class="mb-2"><strong>Notes:</strong> ${notes}</p>`;
                    }
                    
                    message += `<p class="mt-3 text-sm text-gray-600">Your request will be submitted for admin approval.</p></div>`;

                    Swal.fire({
                        title: 'Confirm Leave Request',
                        html: message,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#ec4899',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Yes, Submit Request',
                        cancelButtonText: 'Cancel',
                        reverseButtons: true,
                        focusConfirm: false,
                        customClass: {
                            popup: 'text-left'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Show loading state
                            submitBtn.disabled = true;
                            submitBtn.innerHTML = '<span class="inline-block animate-spin mr-2">⏳</span> Submitting...';
                            
                            // Submit the form
                            form.submit();
                        }
                    });
                });
            }
        });
    </script>
</x-app-layout>
