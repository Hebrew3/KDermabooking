<x-app-layout>
<x-mobile-header />

    <!-- Include Staff Sidebar -->
    <x-staff-sidebar />

    <!-- FullCalendar CSS -->
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css' rel='stylesheet' />
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <!-- Custom Calendar Styles -->
    <style>
        .fc-event {
            border-radius: 6px !important;
            font-weight: 500 !important;
            font-size: 12px !important;
        }
        
        .fc-event-title {
            font-weight: 500 !important;
        }
        
        .fc-daygrid-event {
            margin: 1px 2px !important;
        }
        
        .fc-timegrid-event {
            border-radius: 4px !important;
        }
        
        .fc-bg-event {
            opacity: 0.3 !important;
        }
        
        .legend-item:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .legend-item {
            transition: all 0.2s ease;
        }
    </style>

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
                        <div class="text-sm text-gray-500">
                            Your schedule is managed by the clinic admin. This calendar is read-only.
                        </div>
                    </div>
                </div>
            </div>

            @if(session('success'))
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: '{{ session('success') }}',
                            timer: 3000,
                            timerProgressBar: true,
                            showConfirmButton: false,
                            toast: true,
                            position: 'top-end'
                        });
                    });
                </script>
            @endif

            @if(session('error'))
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: '{{ session('error') }}',
                            timer: 5000,
                            timerProgressBar: true,
                            showConfirmButton: true,
                            toast: true,
                            position: 'top-end'
                        });
                    });
                </script>
            @endif

            <!-- Legend -->
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Calendar Legend</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="legend-item flex items-center space-x-3 p-3 bg-green-50 rounded-lg border border-green-200 cursor-pointer">
                        <div class="flex items-center justify-center w-8 h-8 bg-green-500 rounded-lg">
                            <i class="fas fa-clock text-white text-sm"></i>
                        </div>
                        <div>
                            <div class="font-medium text-green-900">Available Hours</div>
                            <div class="text-xs text-green-700">Your working time</div>
                        </div>
                    </div>
                    
                    <div class="legend-item flex items-center space-x-3 p-3 bg-blue-50 rounded-lg border border-blue-200 cursor-pointer">
                        <div class="flex items-center justify-center w-8 h-8 bg-blue-500 rounded-lg">
                            <i class="fas fa-calendar-check text-white text-sm"></i>
                        </div>
                        <div>
                            <div class="font-medium text-blue-900">Confirmed Appointments</div>
                            <div class="text-xs text-blue-700">Scheduled bookings</div>
                        </div>
                    </div>
                    
                    <div class="legend-item flex items-center space-x-3 p-3 bg-yellow-50 rounded-lg border border-yellow-200 cursor-pointer">
                        <div class="flex items-center justify-center w-8 h-8 bg-yellow-500 rounded-lg">
                            <i class="fas fa-hourglass-half text-white text-sm"></i>
                        </div>
                        <div>
                            <div class="font-medium text-yellow-900">Pending Appointments</div>
                            <div class="text-xs text-yellow-700">Awaiting confirmation</div>
                        </div>
                    </div>
                    
                    <div class="legend-item flex items-center space-x-3 p-3 bg-red-50 rounded-lg border border-red-200 cursor-pointer">
                        <div class="flex items-center justify-center w-8 h-8 bg-red-500 rounded-lg">
                            <i class="fas fa-ban text-white text-sm"></i>
                        </div>
                        <div>
                            <div class="font-medium text-red-900">Unavailable</div>
                            <div class="text-xs text-red-700">Blocked time slots</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FullCalendar Container -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div id='calendar'></div>
            </div>
        </div>
    </div>

    <!-- Unavailability Modal (disabled for staff) -->
    <div id="unavailabilityModal" class="hidden">
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
                                    <input type="time" name="start_time" id="unavailableStartTime"
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">End Time (Optional)</label>
                                    <input type="time" name="end_time" id="unavailableEndTime"
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Reason</label>
                                <select name="reason" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                                    <option value="">Select a reason</option>
                                    <option value="emergency">Emergency</option>
                                    <option value="sick_leave">Sick Leave</option>
                                    <option value="personal_leave">Personal Leave</option>
                                    <option value="vacation">Vacation</option>
                                    <option value="training">Training</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Additional Notes (Optional)</label>
                                <textarea name="notes" rows="3" placeholder="Additional details about the unavailability..."
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

    <!-- Schedule Settings Modal (disabled for staff) -->
    <div id="scheduleSettingsModal" class="hidden">
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

                    <form method="POST" action="{{ route('staff.schedule.update') }}" class="space-y-4" onsubmit="handleScheduleUpdate(event)">
                        @csrf
                        @foreach($weeklySchedule as $day => $schedule)
                        <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                            <input type="hidden" name="schedules[{{ $day }}][day_of_week]" value="{{ $day }}">
                            
                            <div class="flex items-center space-x-4">
                                <div class="w-20 text-sm font-medium text-gray-700">{{ $day }}</div>
                                
                                <label class="flex items-center">
                                    <input type="checkbox" 
                                           name="schedules[{{ $day }}][is_available]" 
                                           value="1"
                                           {{ isset($schedule['is_available']) && $schedule['is_available'] ? 'checked' : '' }}
                                           class="h-4 w-4 text-pink-600 focus:ring-pink-500 border-gray-300 rounded"
                                           onchange="toggleTimeInputs(this, '{{ $day }}')">
                                    <span class="ml-2 text-sm text-gray-600">Available</span>
                                </label>
                            </div>

                            <div class="flex items-center space-x-2" id="time-inputs-{{ $day }}">
                                <input type="time" 
                                       name="schedules[{{ $day }}][start_time]" 
                                       value="{{ $schedule['start_time'] ?? '' }}"
                                       class="text-sm border border-gray-300 rounded px-2 py-1 focus:ring-2 focus:ring-pink-500 focus:border-pink-500"
                                       {{ !isset($schedule['is_available']) || !$schedule['is_available'] ? 'disabled' : '' }}>
                                <span class="text-gray-400">to</span>
                                <input type="time" 
                                       name="schedules[{{ $day }}][end_time]" 
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

    <!-- Event Details Modal -->
    <div id="eventDetailsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 id="eventDetailsTitle" class="text-lg font-semibold text-gray-900"></h3>
                        <button onclick="closeEventDetailsModal()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <div id="eventDetailsContent">
                        <!-- Content will be populated by JavaScript -->
                    </div>

                    <div class="flex space-x-3 mt-6" id="eventDetailsActions">
                        <!-- Actions will be populated by JavaScript -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden form for removing unavailability -->
    <form id="removeUnavailabilityForm" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <!-- FullCalendar JS -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        let calendar;
        let selectedDate = null;

        // Calendar data from server
        const calendarData = {
            appointments: @json($monthlyAppointments ?? []),
            unavailabilities: @json($monthlyUnavailabilities ?? []),
            weeklySchedule: @json($weeklySchedule ?? [])
        };

        // Calendar data loaded successfully

        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');
            
            calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                firstDay: 1, // Start week on Monday (0=Sunday, 1=Monday)
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                height: 'auto',
                slotMinTime: '06:00:00',
                slotMaxTime: '22:00:00',
                allDaySlot: false,
                nowIndicator: true,
                businessHours: false, // We'll handle this with our own events
                events: function(fetchInfo, successCallback, failureCallback) {
                    const events = [];
                    
                    // Add appointments as events
                    Object.keys(calendarData.appointments).forEach(date => {
                        calendarData.appointments[date].forEach(appointment => {
                            events.push({
                                id: 'appointment-' + appointment.id,
                                title: `${appointment.client_name} - ${appointment.service_name}`,
                                start: `${appointment.appointment_date}T${appointment.appointment_time}`,
                                backgroundColor: getAppointmentColor(appointment.status),
                                borderColor: getAppointmentColor(appointment.status),
                                textColor: '#ffffff',
                                extendedProps: {
                                    type: 'appointment',
                                    appointment: appointment
                                }
                            });
                        });
                    });
                    
                    // Add unavailabilities as events
                    calendarData.unavailabilities.forEach((unavailability) => {
                        const startTime = unavailability.start_time || '00:00';
                        const endTime = unavailability.end_time || '23:59';
                        
                        // Extract just the date part (YYYY-MM-DD) from the datetime string
                        const dateOnly = unavailability.unavailable_date.split('T')[0];
                        
                        // Format reason for display
                        const reasonMap = {
                            'emergency': 'Emergency',
                            'sick_leave': 'Sick Leave',
                            'personal_leave': 'Personal Leave',
                            'vacation': 'Vacation',
                            'training': 'Training',
                            'other': 'Other'
                        };
                        const formattedReason = reasonMap[unavailability.reason] || unavailability.reason;
                        
                        const unavailabilityEvent = {
                            id: 'unavailability-' + unavailability.id,
                            title: `Unavailable: ${formattedReason}`,
                            start: `${dateOnly}T${startTime}`,
                            end: `${dateOnly}T${endTime}`,
                            backgroundColor: '#ef4444',
                            borderColor: '#dc2626',
                            textColor: '#ffffff',
                            extendedProps: {
                                type: 'unavailability',
                                unavailability: unavailability
                            }
                        };
                        
                        events.push(unavailabilityEvent);
                    });
                    
                    // Add working hours as background events
                    const startDate = new Date(fetchInfo.start);
                    const endDate = new Date(fetchInfo.end);
                    
                    for (let d = new Date(startDate); d < endDate; d.setDate(d.getDate() + 1)) {
                        // Use consistent day mapping (0=Sunday, 1=Monday, etc.)
                        const dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                        const dayName = dayNames[d.getDay()];
                        const schedule = calendarData.weeklySchedule[dayName];
                        
                        // Use local date string to avoid timezone issues
                        const year = d.getFullYear();
                        const month = String(d.getMonth() + 1).padStart(2, '0');
                        const day = String(d.getDate()).padStart(2, '0');
                        const dateString = `${year}-${month}-${day}`;
                        
                        if (schedule && schedule.is_available && schedule.start_time && schedule.end_time) {
                            
                            // Add as both background and regular event for better visibility
                            const backgroundEvent = {
                                id: 'working-hours-bg-' + dateString,
                                title: '',
                                start: `${dateString}T${schedule.start_time}`,
                                end: `${dateString}T${schedule.end_time}`,
                                display: 'background',
                                backgroundColor: '#dcfce7',
                                borderColor: 'transparent',
                                extendedProps: {
                                    type: 'working-hours-bg'
                                }
                            };
                            
                            const workingEvent = {
                                id: 'working-hours-' + dateString,
                                title: `Available ${schedule.start_time.substring(0,5)} - ${schedule.end_time.substring(0,5)}`,
                                start: `${dateString}T${schedule.start_time}`,
                                end: `${dateString}T${schedule.end_time}`,
                                backgroundColor: '#10b981',
                                borderColor: '#059669',
                                textColor: '#ffffff',
                                display: 'block',
                                allDay: false,
                                extendedProps: {
                                    type: 'working-hours'
                                }
                            };
                            
                            events.push(backgroundEvent);
                            events.push(workingEvent);
                        }
                    }
                    
                    successCallback(events);
                },
                eventClick: function(info) {
                    const event = info.event;
                    const props = event.extendedProps;
                    
                    if (props.type === 'appointment') {
                        showAppointmentDetails(props.appointment);
                    } else if (props.type === 'unavailability') {
                        showUnavailabilityDetails(props.unavailability);
                    } else if (props.type === 'working-hours') {
                        showWorkingHoursDetails(event);
                    }
                },
                dateClick: function(info) {
                    // Read-only: no action when clicking on a date
                },
                dayMaxEvents: 3,
                moreLinkClick: 'popover',
                eventDisplay: 'block',
                displayEventTime: true,
                eventTimeFormat: {
                    hour: 'numeric',
                    minute: '2-digit',
                    meridiem: 'short'
                }
            });
            
            calendar.render();
        });

        function getAppointmentColor(status) {
            switch(status) {
                case 'confirmed': return '#3b82f6'; // Blue for confirmed
                case 'pending': return '#f59e0b';   // Yellow for pending
                case 'completed': return '#10b981'; // Green for completed
                case 'cancelled': return '#6b7280'; // Gray for cancelled
                default: return '#8b5cf6';          // Purple for others
            }
        }

        function showAppointmentDetails(appointment) {
            document.getElementById('eventDetailsTitle').textContent = 'Appointment Details';
            
            const content = `
                <div class="space-y-3">
                    <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg">
                        <h4 class="font-medium text-blue-900">Client</h4>
                        <p class="text-blue-700">${appointment.client_name}</p>
                    </div>
                    <div class="p-3 bg-green-50 border border-green-200 rounded-lg">
                        <h4 class="font-medium text-green-900">Service</h4>
                        <p class="text-green-700">${appointment.service_name}</p>
                    </div>
                    <div class="p-3 bg-gray-50 border border-gray-200 rounded-lg">
                        <h4 class="font-medium text-gray-900">Time</h4>
                        <p class="text-gray-700">${formatTime(appointment.appointment_time)}</p>
                    </div>
                    <div class="p-3 bg-purple-50 border border-purple-200 rounded-lg">
                        <h4 class="font-medium text-purple-900">Status</h4>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-${getStatusColor(appointment.status)}-100 text-${getStatusColor(appointment.status)}-800">
                            ${appointment.status.charAt(0).toUpperCase() + appointment.status.slice(1)}
                        </span>
                    </div>
                </div>
            `;
            
            document.getElementById('eventDetailsContent').innerHTML = content;
            
            const actions = `
                <button onclick="closeEventDetailsModal()" 
                        class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 py-2 px-4 rounded-lg font-medium transition-colors">
                    Close
                </button>
            `;
            
            document.getElementById('eventDetailsActions').innerHTML = actions;
            document.getElementById('eventDetailsModal').classList.remove('hidden');
        }

        function showUnavailabilityDetails(unavailability) {
            document.getElementById('eventDetailsTitle').textContent = 'Unavailability Details';
            
            const timeRange = unavailability.start_time && unavailability.end_time 
                ? `${formatTime(unavailability.start_time)} - ${formatTime(unavailability.end_time)}`
                : 'All Day';
            
            // Format reason for display
            const reasonMap = {
                'emergency': 'Emergency',
                'sick_leave': 'Sick Leave',
                'personal_leave': 'Personal Leave',
                'vacation': 'Vacation',
                'training': 'Training',
                'other': 'Other'
            };
            const formattedReason = reasonMap[unavailability.reason] || unavailability.reason;
            
            let content = `
                <div class="space-y-3">
                    <div class="p-3 bg-red-50 border border-red-200 rounded-lg">
                        <h4 class="font-medium text-red-900">Reason</h4>
                        <p class="text-red-700">${formattedReason}</p>
                    </div>
                    <div class="p-3 bg-gray-50 border border-gray-200 rounded-lg">
                        <h4 class="font-medium text-gray-900">Time</h4>
                        <p class="text-gray-700">${timeRange}</p>
                    </div>`;
            
            // Add notes section if notes exist
            if (unavailability.notes && unavailability.notes.trim()) {
                content += `
                    <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg">
                        <h4 class="font-medium text-blue-900">Notes</h4>
                        <p class="text-blue-700">${unavailability.notes}</p>
                    </div>`;
            }
            
            content += `</div>`;
            
            document.getElementById('eventDetailsContent').innerHTML = content;
            
            const actions = `
                <button onclick="closeEventDetailsModal()" 
                        class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 py-2 px-4 rounded-lg font-medium transition-colors">
                    Close
                </button>
            `;
            
            document.getElementById('eventDetailsActions').innerHTML = actions;
            document.getElementById('eventDetailsModal').classList.remove('hidden');
        }

        function showWorkingHoursDetails(event) {
            document.getElementById('eventDetailsTitle').textContent = 'Working Hours';
            
            const startTime = formatTime(event.start.toTimeString().substring(0, 8));
            const endTime = formatTime(event.end.toTimeString().substring(0, 8));
            const dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            const dayName = dayNames[event.start.getDay()];
            
            const content = `
                <div class="space-y-3">
                    <div class="p-3 bg-green-50 border border-green-200 rounded-lg">
                        <h4 class="font-medium text-green-900">Available Hours</h4>
                        <p class="text-green-700">${startTime} - ${endTime}</p>
                    </div>
                    <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg">
                        <h4 class="font-medium text-blue-900">Day</h4>
                        <p class="text-blue-700">${dayName}, ${event.start.toLocaleDateString()}</p>
                    </div>
                </div>
            `;
            
            document.getElementById('eventDetailsContent').innerHTML = content;
            
            const actions = `
                <button onclick="closeEventDetailsModal()" 
                        class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 py-2 px-4 rounded-lg font-medium transition-colors">
                    Close
                </button>
            `;
            
            document.getElementById('eventDetailsActions').innerHTML = actions;
            document.getElementById('eventDetailsModal').classList.remove('hidden');
        }

        function getStatusColor(status) {
            switch(status) {
                case 'confirmed': return 'green';
                case 'pending': return 'yellow';
                case 'completed': return 'blue';
                case 'cancelled': return 'gray';
                default: return 'purple';
            }
        }

        function formatTime(timeString) {
            if (!timeString) return '';
            const [hours, minutes] = timeString.split(':');
            const hour = parseInt(hours);
            const ampm = hour >= 12 ? 'PM' : 'AM';
            const displayHour = hour % 12 || 12;
            return `${displayHour}:${minutes} ${ampm}`;
        }

        function openUnavailabilityModal(date = null) {
            document.getElementById('unavailabilityModal').classList.remove('hidden');
            if (date) {
                document.getElementById('unavailableDate').value = date;
            }
        }

        function closeUnavailabilityModal() {
            document.getElementById('unavailabilityModal').classList.add('hidden');
        }

        function openScheduleSettingsModal() {
            // Populate the modal with current schedule data
            populateScheduleModal();
            document.getElementById('scheduleSettingsModal').classList.remove('hidden');
        }

        function populateScheduleModal() {
            const days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
            
            days.forEach((day) => {
                const schedule = calendarData.weeklySchedule[day];
                
                if (schedule) {
                    // Set checkbox
                    const checkbox = document.querySelector(`input[name="schedules[${day}][is_available]"]`);
                    if (checkbox) {
                        checkbox.checked = schedule.is_available;
                        
                        // Set time inputs
                        const startTimeInput = document.querySelector(`input[name="schedules[${day}][start_time]"]`);
                        const endTimeInput = document.querySelector(`input[name="schedules[${day}][end_time]"]`);
                        
                        if (startTimeInput) startTimeInput.value = schedule.start_time || '';
                        if (endTimeInput) endTimeInput.value = schedule.end_time || '';
                        
                        // Enable/disable time inputs based on availability
                        if (startTimeInput) startTimeInput.disabled = !schedule.is_available;
                        if (endTimeInput) endTimeInput.disabled = !schedule.is_available;
                    }
                }
            });
        }

        function closeScheduleSettingsModal() {
            document.getElementById('scheduleSettingsModal').classList.add('hidden');
        }

        function closeEventDetailsModal() {
            document.getElementById('eventDetailsModal').classList.add('hidden');
        }

        function toggleTimeInputs(checkbox, day) {
            const timeInputs = document.querySelectorAll(`#time-inputs-${day} input`);
            timeInputs.forEach(input => {
                input.disabled = !checkbox.checked;
                if (!checkbox.checked) {
                    input.value = '';
                }
            });
        }

        function removeUnavailability(id) {
            Swal.fire({
                title: 'Remove Unavailability?',
                text: 'Are you sure you want to remove this unavailability? This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, remove it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('removeUnavailabilityForm');
                    form.action = `/staff/schedule/unavailability/${id}`;
                    form.submit();
                }
            });
        }

        function handleScheduleUpdate(event) {
            event.preventDefault();
            
            const form = event.target;
            const formData = new FormData(form);
            
            // Debug: Log form data
            console.log('Form data being sent:');
            for (let [key, value] of formData.entries()) {
                console.log(key, value);
            }
            
            // Show loading state with SweetAlert
            Swal.fire({
                title: 'Updating Schedule...',
                text: 'Please wait while we save your schedule changes.',
                icon: 'info',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            const submitButton = form.querySelector('button[type="submit"]');
            const originalText = submitButton.textContent;
            submitButton.disabled = true;
            
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                                   document.querySelector('input[name="_token"]')?.value
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(errorData => {
                        console.error('Server validation errors:', errorData);
                        
                        // Show detailed validation errors
                        if (errorData.errors) {
                            const errorMessages = Object.entries(errorData.errors).map(([field, messages]) => {
                                return `${field}: ${Array.isArray(messages) ? messages.join(', ') : messages}`;
                            }).join('\n');
                            
                            showNotification(`Validation errors:\n${errorMessages}`, 'error');
                        }
                        
                        throw new Error(errorData.message || 'Server error');
                    });
                }
                return response.json();
            })
            .then(data => {
                Swal.close(); // Close loading dialog
                
                if (data.success) {
                    // Update the calendar data
                    updateCalendarScheduleData(data.weeklySchedule);
                    
                    // Refresh the calendar
                    calendar.refetchEvents();
                    
                    // Close modal
                    closeScheduleSettingsModal();
                    
                    // Show success message
                    showNotification('Schedule updated successfully!', 'success');
                } else {
                    showNotification(data.message || 'Failed to update schedule. Please try again.', 'error');
                    
                    // Show validation errors if available
                    if (data.errors) {
                        console.error('Validation errors:', data.errors);
                        const errorMessages = Object.values(data.errors).flat();
                        showNotification(errorMessages.join(' '), 'error');
                    }
                }
            })
            .catch(error => {
                Swal.close(); // Close loading dialog
                console.error('Error updating schedule:', error);
                showNotification('An error occurred. Please try again.', 'error');
            })
            .finally(() => {
                // Reset button state
                submitButton.textContent = originalText;
                submitButton.disabled = false;
            });
        }

        function updateCalendarScheduleData(newScheduleData) {
            // Update the global calendar data
            calendarData.weeklySchedule = newScheduleData;
            console.log('Updated calendar schedule data:', calendarData.weeklySchedule);
        }

        function showNotification(message, type) {
            if (type === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: message,
                    timer: 3000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            } else {
                // Handle multi-line error messages
                const formattedMessage = message.includes('\n') ? message.replace(/\n/g, '<br>') : message;
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    html: formattedMessage,
                    timer: 5000,
                    timerProgressBar: true,
                    showConfirmButton: true,
                    toast: true,
                    position: 'top-end'
                });
            }
        }

        // Close modals when clicking outside
        document.addEventListener('click', function(e) {
            if (e.target.id === 'unavailabilityModal') closeUnavailabilityModal();
            if (e.target.id === 'scheduleSettingsModal') closeScheduleSettingsModal();
            if (e.target.id === 'eventDetailsModal') closeEventDetailsModal();
        });
    </script>
</x-app-layout>
