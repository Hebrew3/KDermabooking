<x-app-layout>
<x-mobile-header />
<x-admin-sidebar />

<div class="lg:ml-64">
    <div class="p-8">
        <!-- Page Header -->
        <div class="mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Demand Forecasting</h1>
                    <p class="text-gray-600 mt-2">Predict future appointment demand using Simple Moving Average (SMA)</p>
                </div>
                <a href="{{ route('admin.forecasting.export') }}" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                    Export Report
                </a>
            </div>
        </div>

        <!-- Forecasting Method Info -->
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-6 mb-8">
            <h3 class="text-lg font-semibold text-blue-800 mb-3">Simple Moving Average (SMA) Method</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="font-medium text-blue-700 mb-2">Formula:</h4>
                    <div class="bg-white p-3 rounded-lg border">
                        <code class="text-sm">Forecast<sub>n+1</sub> = (D<sub>1</sub> + D<sub>2</sub> + D<sub>3</sub> + ... + D<sub>n</sub>) / n</code>
                    </div>
                    <p class="text-sm text-blue-600 mt-2">Takes the average of the last few months to predict the next month</p>
                </div>
                <div>
                    <h4 class="font-medium text-blue-700 mb-2">Where:</h4>
                    <ul class="text-sm text-blue-600 space-y-1">
                        <li>• <strong>Forecast<sub>n+1</sub></strong> = forecast for next month</li>
                        <li>• <strong>D<sub>n</sub></strong> = actual demand (appointments) this month</li>
                        <li>• <strong>n</strong> = number of months you average (default: 3 months)</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Overall Appointments Forecast -->
        <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-6">Overall Appointments Forecast</h2>
            
            @if(isset($forecasts['appointments']))
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="bg-gradient-to-r from-pink-500 to-rose-500 text-white p-6 rounded-xl">
                        <h3 class="text-lg font-medium mb-2">Next Month Forecast</h3>
                        <p class="text-3xl font-bold">{{ $forecasts['appointments']['forecast'] }}</p>
                        <p class="text-pink-100 text-sm">appointments</p>
                    </div>
                    
                    <div class="bg-gradient-to-r from-blue-500 to-indigo-500 text-white p-6 rounded-xl">
                        <h3 class="text-lg font-medium mb-2">Confidence Level</h3>
                        <p class="text-2xl font-bold">{{ $forecasts['appointments']['confidence'] }}</p>
                        <div class="mt-2">
                            @if($forecasts['appointments']['confidence'] == 'High')
                                <div class="bg-green-400 h-2 rounded-full"></div>
                            @elseif($forecasts['appointments']['confidence'] == 'Medium')
                                <div class="bg-yellow-400 h-2 rounded-full w-2/3"></div>
                            @else
                                <div class="bg-red-400 h-2 rounded-full w-1/3"></div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="bg-gradient-to-r from-green-500 to-emerald-500 text-white p-6 rounded-xl">
                        <h3 class="text-lg font-medium mb-2">Trend Direction</h3>
                        <p class="text-2xl font-bold">{{ $forecasts['appointments']['trend'] }}</p>
                        @if($forecasts['appointments']['trend'] == 'Increasing')
                            <p class="text-green-100 text-sm">📈 Growing demand</p>
                        @elseif($forecasts['appointments']['trend'] == 'Decreasing')
                            <p class="text-green-100 text-sm">📉 Declining demand</p>
                        @else
                            <p class="text-green-100 text-sm">➡️ Steady demand</p>
                        @endif
                    </div>
                    
                    <div class="bg-gradient-to-r from-purple-500 to-violet-500 text-white p-6 rounded-xl">
                        <h3 class="text-lg font-medium mb-2">Historical Average</h3>
                        <p class="text-3xl font-bold">{{ $forecasts['appointments']['historical_average'] }}</p>
                        <p class="text-purple-100 text-sm">past {{ $forecasts['appointments']['periods_used'] }} months</p>
                    </div>
                </div>
                
                <!-- Calculation Details -->
                <div class="mt-6 bg-gray-50 p-4 rounded-lg">
                    <h4 class="font-medium text-gray-700 mb-2">Calculation Details:</h4>
                    <p class="text-sm text-gray-600">
                        Formula: {{ $forecasts['appointments']['calculation_details']['formula'] }}
                    </p>
                    <p class="text-sm text-gray-500 mt-1">
                        Using {{ $forecasts['appointments']['periods_used'] }}-month moving average
                    </p>
                </div>
            @else
                <div class="text-center py-8">
                    <p class="text-gray-500">Insufficient data for forecasting. Need at least 3 months of appointment history.</p>
                </div>
            @endif
        </div>

        <!-- Service-Specific Forecasts -->
        @if(isset($forecasts['services']) && count($forecasts['services']) > 0)
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-6">Service-Specific Forecasts</h2>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    @foreach($forecasts['services'] as $serviceId => $serviceData)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="font-medium text-gray-800">{{ $serviceData['name'] }}</h3>
                                <span class="px-2 py-1 text-xs rounded-full 
                                    @if($serviceData['forecast']['confidence'] == 'High') bg-green-100 text-green-800
                                    @elseif($serviceData['forecast']['confidence'] == 'Medium') bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ $serviceData['forecast']['confidence'] }} Confidence
                                </span>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-600">Next Month Forecast</p>
                                    <p class="text-2xl font-bold text-pink-600">{{ $serviceData['forecast']['forecast'] }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Trend</p>
                                    <p class="font-medium 
                                        @if($serviceData['forecast']['trend'] == 'Increasing') text-green-600
                                        @elseif($serviceData['forecast']['trend'] == 'Decreasing') text-red-600
                                        @else text-gray-600 @endif">
                                        {{ $serviceData['forecast']['trend'] }}
                                    </p>
                                </div>
                            </div>
                            
                            @if(isset($serviceData['forecast']['message']))
                                <div class="mt-3 p-2 bg-yellow-50 border border-yellow-200 rounded text-sm text-yellow-700">
                                    {{ $serviceData['forecast']['message'] }}
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Example Calculation -->
        <div class="mt-8 bg-gray-50 rounded-xl p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Example Calculation</h3>
            <div class="bg-white p-4 rounded-lg border">
                <h4 class="font-medium text-gray-700 mb-2">Sample Data:</h4>
                <ul class="text-sm text-gray-600 mb-3">
                    <li>• Jan: 120 appointments</li>
                    <li>• Feb: 100 appointments</li>
                    <li>• Mar: 110 appointments</li>
                </ul>
                <h4 class="font-medium text-gray-700 mb-2">Using 3-month moving average:</h4>
                <div class="bg-gray-50 p-3 rounded">
                    <code class="text-sm">Forecast<sub>April</sub> = (120 + 100 + 110) / 3 = 330 / 3 = 110 appointments</code>
                </div>
            </div>
        </div>
    </div>
</div>
</x-app-layout>
