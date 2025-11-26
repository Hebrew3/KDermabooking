<x-app-layout>
<x-mobile-header />

    <!-- Include Staff Sidebar -->
    <x-staff-sidebar />

    <!-- Main Content -->
    <div class="lg:ml-64">
        <div class="p-8">
            <!-- Page Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">My Schedule Calendar</h1>
                        <p class="text-gray-600 mt-2">Manage your availability, appointments, and working hours</p>
                    </div>
                    
                    <!-- Calendar Controls -->
                    <div class="flex items-center space-x-4">
                        <button onclick="previousMonth()" class="p-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </button>
                        
                        <div class="text-center">
                            <div id="currentMonth" class="text-lg font-semibold text-gray-900"></div>
                            <button onclick="goToToday()" class="text-sm text-pink-600 hover:text-pink-700">Today</button>
                        </div>
                        
                        <button onclick="nextMonth()" class="p-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                        
                        <div class="flex space-x-2">
                            <button onclick="openScheduleSettingsModal()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                                <i class="fas fa-cog mr-2"></i>Schedule Settings
                            </button>
                            <button onclick="openUnavailabilityModal()" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                                <i class="fas fa-ban mr-2"></i>Mark Unavailable
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Legend -->
            <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Legend</h3>
                    <div class="flex items-center space-x-6 text-sm">
                        <div class="flex items-center space-x-2">
                            <div class="w-4 h-4 bg-green-200 border-2 border-green-400 rounded"></div>
                            <span class="text-gray-600">Available</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-4 h-4 bg-blue-200 border-2 border-blue-400 rounded"></div>
                            <span class="text-gray-600">Has Appointments</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-4 h-4 bg-red-200 border-2 border-red-400 rounded"></div>
                            <span class="text-gray-600">Unavailable</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-4 h-4 bg-gray-200 border-2 border-gray-400 rounded"></div>
                            <span class="text-gray-600">Not Working</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-4 h-4 bg-pink-200 border-2 border-pink-400 rounded"></div>
                            <span class="text-gray-600">Today</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Calendar -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <!-- Calendar Header -->
                <div class="grid grid-cols-7 bg-gray-50 border-b border-gray-200">
                    <div class="p-4 text-center font-semibold text-gray-700">Sunday</div>
                    <div class="p-4 text-center font-semibold text-gray-700">Monday</div>
                    <div class="p-4 text-center font-semibold text-gray-700">Tuesday</div>
                    <div class="p-4 text-center font-semibold text-gray-700">Wednesday</div>
                    <div class="p-4 text-center font-semibold text-gray-700">Thursday</div>
                    <div class="p-4 text-center font-semibold text-gray-700">Friday</div>
                    <div class="p-4 text-center font-semibold text-gray-700">Saturday</div>
                </div>

                <!-- Calendar Body -->
                <div id="calendarBody" class="grid grid-cols-7">
                    <!-- Calendar days will be populated by JavaScript -->
                </div>
            </div>
        </div>
    </div>

    <!-- Day Details Modal -->
    <div id="dayDetailsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-screen overflow-y-auto">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 id="dayDetailsTitle" class="text-lg font-semibold text-gray-900"></h3>
                        <button onclick="closeDayDetailsModal()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <div id="dayDetailsContent">
                        <!-- Content will be populated by JavaScript -->
                    </div>

                    <div class="flex space-x-3 mt-6">
                        <button onclick="openUnavailabilityModalForDate()" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            Mark Unavailable
                        </button>
                        <button onclick="closeDayDetailsModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg font-medium transition-colors">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Unavailability Modal -->
    <div id="unavailabilityModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Mark Unavailable</h3>
                        <button onclick="closeUnavailabilityModal()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <form method="POST" action="{{ route('staff.schedule.add-unavailability') }}">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                                <input type="date" name="unavailable_date" id="unavailableDate" required
                                       min="{{ Carbon\Carbon::today()->format('Y-m-d') }}"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Start Time (Optional)</label>
                                    <input type="time" name="start_time"
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">End Time (Optional)</label>
                                    <input type="time" name="end_time"
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Reason</label>
                                <textarea name="reason" rows="3" required placeholder="e.g., Personal appointment, Sick leave, etc."
                                          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-pink-500 focus:border-pink-500"></textarea>
                            </div>
                        </div>

                        <div class="flex space-x-3 mt-6">
                            <button type="button" onclick="closeUnavailabilityModal()" 
                                    class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 py-2 px-4 rounded-lg font-medium transition-colors">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="flex-1 bg-red-500 hover:bg-red-600 text-white py-2 px-4 rounded-lg font-medium transition-colors">
                                Mark Unavailable
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Schedule Settings Modal -->
    <div id="scheduleSettingsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Weekly Schedule Settings</h3>
                        <button onclick="closeScheduleSettingsModal()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <form method="POST" action="{{ route('staff.schedule.update') }}" class="space-y-4">
                        @csrf
                        @foreach($weeklySchedule as $day => $schedule)
                        <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                            <input type="hidden" name="schedules[{{ $loop->index }}][day_of_week]" value="{{ $day }}">
                            
                            <div class="flex items-center space-x-4">
                                <div class="w-20 text-sm font-medium text-gray-700">{{ $day }}</div>
                                
                                <label class="flex items-center">
                                    <input type="checkbox" 
                                           name="schedules[{{ $loop->index }}][is_available]" 
                                           value="1"
                                           {{ isset($schedule['is_available']) && $schedule['is_available'] ? 'checked' : '' }}
                                           class="h-4 w-4 text-pink-600 focus:ring-pink-500 border-gray-300 rounded"
                                           onchange="toggleTimeInputs(this, {{ $loop->index }})">
                                    <span class="ml-2 text-sm text-gray-600">Available</span>
                                </label>
                            </div>

                            <div class="flex items-center space-x-2" id="time-inputs-{{ $loop->index }}">
                                <input type="time" 
                                       name="schedules[{{ $loop->index }}][start_time]" 
                                       value="{{ $schedule['start_time'] ?? '' }}"
                                       class="text-sm border border-gray-300 rounded px-2 py-1 focus:ring-2 focus:ring-pink-500 focus:border-pink-500"
                                       {{ !isset($schedule['is_available']) || !$schedule['is_available'] ? 'disabled' : '' }}>
                                <span class="text-gray-400">to</span>
                                <input type="time" 
                                       name="schedules[{{ $loop->index }}][end_time]" 
                                       value="{{ $schedule['end_time'] ?? '' }}"
                                       class="text-sm border border-gray-300 rounded px-2 py-1 focus:ring-2 focus:ring-pink-500 focus:border-pink-500"
                                       {{ !isset($schedule['is_available']) || !$schedule['is_available'] ? 'disabled' : '' }}>
                            </div>
                        </div>
                        @endforeach

                        <div class="flex space-x-3 mt-6">
                            <button type="button" onclick="closeScheduleSettingsModal()" 
                                    class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 py-2 px-4 rounded-lg font-medium transition-colors">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="flex-1 bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-lg font-medium transition-colors">
                                Update Schedule
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden form for removing unavailability -->
    <form id="removeUnavailabilityForm" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <script>
        // Calendar data from server
        const calendarData = {
            appointments: @json($monthlyAppointments ?? []),
            unavailabilities: @json($monthlyUnavailabilities ?? []),
            weeklySchedule: @json($weeklySchedule ?? [])
        };

        let currentDate = new Date();
        let selectedDate = null;

        // Initialize calendar
        document.addEventListener('DOMContentLoaded', function() {
            renderCalendar();
        });

        function renderCalendar() {
            const year = currentDate.getFullYear();
            const month = currentDate.getMonth();
            
            // Update month display
            document.getElementById('currentMonth').textContent = 
                new Date(year, month).toLocaleDateString('en-US', { month: 'long', year: 'numeric' });

            // Get first day of month and number of days
            const firstDay = new Date(year, month, 1).getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();
            const today = new Date();

            const calendarBody = document.getElementById('calendarBody');
            calendarBody.innerHTML = '';

            // Add empty cells for days before month starts
            for (let i = 0; i < firstDay; i++) {
                const emptyCell = document.createElement('div');
                emptyCell.className = 'h-32 bg-gray-50 border-b border-r border-gray-200';
                calendarBody.appendChild(emptyCell);
            }

            // Add days of the month
            for (let day = 1; day <= daysInMonth; day++) {
                const date = new Date(year, month, day);
                const dateString = date.toISOString().split('T')[0];
                const dayName = date.toLocaleDateString('en-US', { weekday: 'long' });
                
                const dayCell = document.createElement('div');
                dayCell.className = 'h-32 border-b border-r border-gray-200 p-2 cursor-pointer hover:bg-gray-50 relative';
                
                // Determine day status
                const isToday = date.toDateString() === today.toDateString();
                const isPast = date < today && !isToday;
                const daySchedule = calendarData.weeklySchedule[dayName];
                const isWorkingDay = daySchedule && daySchedule.is_available;
                
                // Get appointments and unavailabilities for this date
                const dayAppointments = getAppointmentsForDate(dateString);
                const dayUnavailabilities = getUnavailabilitiesForDate(dateString);
                
                // Set background color based on status
                if (isToday) {
                    dayCell.classList.add('bg-pink-100', 'border-pink-300');
                } else if (dayUnavailabilities.length > 0) {
                    dayCell.classList.add('bg-red-100', 'border-red-300');
                } else if (dayAppointments.length > 0) {
                    dayCell.classList.add('bg-blue-100', 'border-blue-300');
                } else if (isWorkingDay) {
                    dayCell.classList.add('bg-green-100', 'border-green-300');
                } else {
                    dayCell.classList.add('bg-gray-100', 'border-gray-300');
                }

                if (isPast) {
                    dayCell.classList.add('opacity-60');
                }

                // Day number
                const dayNumber = document.createElement('div');
                dayNumber.className = `text-lg font-semibold ${isToday ? 'text-pink-800' : 'text-gray-800'}`;
                dayNumber.textContent = day;
                dayCell.appendChild(dayNumber);

                // Working hours
                if (isWorkingDay && daySchedule.start_time && daySchedule.end_time) {
                    const workingHours = document.createElement('div');
                    workingHours.className = 'text-xs text-gray-600 mt-1';
                    workingHours.textContent = `${formatTime(daySchedule.start_time)} - ${formatTime(daySchedule.end_time)}`;
                    dayCell.appendChild(workingHours);
                }

                // Appointments indicator
                if (dayAppointments.length > 0) {
                    const appointmentsIndicator = document.createElement('div');
                    appointmentsIndicator.className = 'text-xs bg-blue-500 text-white px-1 rounded mt-1';
                    appointmentsIndicator.textContent = `${dayAppointments.length} appointment${dayAppointments.length > 1 ? 's' : ''}`;
                    dayCell.appendChild(appointmentsIndicator);
                }

                // Unavailability indicator
                if (dayUnavailabilities.length > 0) {
                    const unavailabilityIndicator = document.createElement('div');
                    unavailabilityIndicator.className = 'text-xs bg-red-500 text-white px-1 rounded mt-1';
                    unavailabilityIndicator.textContent = 'Unavailable';
                    dayCell.appendChild(unavailabilityIndicator);
                }

                // Click handler
                dayCell.addEventListener('click', () => openDayDetails(date, dayAppointments, dayUnavailabilities, daySchedule));

                calendarBody.appendChild(dayCell);
            }
        }

        function getAppointmentsForDate(dateString) {
            return calendarData.appointments[dateString] || [];
        }

        function getUnavailabilitiesForDate(dateString) {
            return calendarData.unavailabilities.filter(u => u.unavailable_date === dateString);
        }

        function formatTime(timeString) {
            if (!timeString) return '';
            const [hours, minutes] = timeString.split(':');
            const hour = parseInt(hours);
            const ampm = hour >= 12 ? 'PM' : 'AM';
            const displayHour = hour % 12 || 12;
            return `${displayHour}:${minutes} ${ampm}`;
        }

        function openDayDetails(date, appointments, unavailabilities, schedule) {
            selectedDate = date;
            const dateString = date.toLocaleDateString('en-US', { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            });
            
            document.getElementById('dayDetailsTitle').textContent = dateString;
            
            let content = '';
            
            // Schedule info
            if (schedule && schedule.is_available) {
                content += `
                    <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg">
                        <h4 class="font-medium text-green-900">Working Hours</h4>
                        <p class="text-green-700">${formatTime(schedule.start_time)} - ${formatTime(schedule.end_time)}</p>
                    </div>
                `;
            } else {
                content += `
                    <div class="mb-4 p-3 bg-gray-50 border border-gray-200 rounded-lg">
                        <h4 class="font-medium text-gray-900">Not Working Today</h4>
                        <p class="text-gray-600">This is not a scheduled working day</p>
                    </div>
                `;
            }
            
            // Appointments
            if (appointments.length > 0) {
                content += '<div class="mb-4"><h4 class="font-medium text-gray-900 mb-2">Appointments</h4>';
                appointments.forEach(apt => {
                    content += `
                        <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg mb-2">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-medium text-blue-900">${apt.client_name}</p>
                                    <p class="text-blue-700">${apt.service_name}</p>
                                    <p class="text-blue-600 text-sm">${formatTime(apt.appointment_time)}</p>
                                </div>
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-${apt.status === 'confirmed' ? 'green' : apt.status === 'pending' ? 'yellow' : 'blue'}-100 text-${apt.status === 'confirmed' ? 'green' : apt.status === 'pending' ? 'yellow' : 'blue'}-800">
                                    ${apt.status.charAt(0).toUpperCase() + apt.status.slice(1)}
                                </span>
                            </div>
                        </div>
                    `;
                });
                content += '</div>';
            }
            
            // Unavailabilities
            if (unavailabilities.length > 0) {
                content += '<div class="mb-4"><h4 class="font-medium text-gray-900 mb-2">Unavailabilities</h4>';
                unavailabilities.forEach(unav => {
                    content += `
                        <div class="p-3 bg-red-50 border border-red-200 rounded-lg mb-2">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-medium text-red-900">${unav.reason}</p>
                                    <p class="text-red-700 text-sm">
                                        ${unav.start_time && unav.end_time ? 
                                            `${formatTime(unav.start_time)} - ${formatTime(unav.end_time)}` : 
                                            'All Day'
                                        }
                                    </p>
                                </div>
                                <button onclick="removeUnavailability(${unav.id})" class="text-red-600 hover:text-red-800 text-sm">
                                    Remove
                                </button>
                            </div>
                        </div>
                    `;
                });
                content += '</div>';
            }
            
            document.getElementById('dayDetailsContent').innerHTML = content;
            document.getElementById('dayDetailsModal').classList.remove('hidden');
        }

        function closeDayDetailsModal() {
            document.getElementById('dayDetailsModal').classList.add('hidden');
            selectedDate = null;
        }

        function openUnavailabilityModal(date = null) {
            document.getElementById('unavailabilityModal').classList.remove('hidden');
            if (date) {
                document.getElementById('unavailableDate').value = date.toISOString().split('T')[0];
            }
        }

        function openUnavailabilityModalForDate() {
            if (selectedDate) {
                openUnavailabilityModal(selectedDate);
                closeDayDetailsModal();
            }
        }

        function closeUnavailabilityModal() {
            document.getElementById('unavailabilityModal').classList.add('hidden');
        }

        function openScheduleSettingsModal() {
            document.getElementById('scheduleSettingsModal').classList.remove('hidden');
        }

        function closeScheduleSettingsModal() {
            document.getElementById('scheduleSettingsModal').classList.add('hidden');
        }

        function toggleTimeInputs(checkbox, index) {
            const timeInputs = document.querySelectorAll(`#time-inputs-${index} input`);
            timeInputs.forEach(input => {
                input.disabled = !checkbox.checked;
                if (!checkbox.checked) {
                    input.value = '';
                }
            });
        }

        function removeUnavailability(id) {
            if (confirm('Are you sure you want to remove this unavailability?')) {
                const form = document.getElementById('removeUnavailabilityForm');
                form.action = `/staff/schedule/unavailability/${id}`;
                form.submit();
            }
        }

        function previousMonth() {
            currentDate.setMonth(currentDate.getMonth() - 1);
            renderCalendar();
        }

        function nextMonth() {
            currentDate.setMonth(currentDate.getMonth() + 1);
            renderCalendar();
        }

        function goToToday() {
            currentDate = new Date();
            renderCalendar();
        }

        // Close modals when clicking outside
        document.addEventListener('click', function(e) {
            if (e.target.id === 'dayDetailsModal') closeDayDetailsModal();
            if (e.target.id === 'unavailabilityModal') closeUnavailabilityModal();
            if (e.target.id === 'scheduleSettingsModal') closeScheduleSettingsModal();
        });
    </script>
</x-app-layout>
