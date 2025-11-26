@props([
    'clientName' => '',
    'service' => '',
    'time' => '',
    'date' => '',
    'status' => 'confirmed',
    'staff' => '',
    'avatar' => null,
    'color' => 'pink'
])

@php
    $statusClasses = [
        'confirmed' => 'bg-green-100 text-green-800',
        'pending' => 'bg-yellow-100 text-yellow-800',
        'cancelled' => 'bg-red-100 text-red-800',
        'completed' => 'bg-blue-100 text-blue-800'
    ];

    $colorClasses = [
        'pink' => 'bg-gradient-to-br from-pink-400 to-rose-400',
        'blue' => 'bg-gradient-to-br from-blue-400 to-indigo-400',
        'purple' => 'bg-gradient-to-br from-purple-400 to-violet-400',
        'green' => 'bg-gradient-to-br from-green-400 to-emerald-400'
    ];

    $bgClasses = [
        'pink' => 'bg-pink-50 border-pink-100',
        'blue' => 'bg-blue-50 border-blue-100',
        'purple' => 'bg-purple-50 border-purple-100',
        'green' => 'bg-green-50 border-green-100'
    ];

    $statusClass = $statusClasses[$status] ?? $statusClasses['confirmed'];
    $avatarClass = $colorClasses[$color] ?? $colorClasses['pink'];
    $bgClass = $bgClasses[$color] ?? $bgClasses['pink'];
@endphp

<div class="flex items-center justify-between p-4 {{ $bgClass }} rounded-xl border transition-all duration-200 hover:shadow-md">
    <div class="flex items-center space-x-4">
        <div class="h-10 w-10 {{ $avatarClass }} rounded-full flex items-center justify-center">
            @if($avatar)
                <img src="{{ $avatar }}" alt="{{ $clientName }}" class="h-10 w-10 rounded-full object-cover">
            @else
                <span class="text-white font-medium text-sm">{{ substr($clientName, 0, 1) }}</span>
            @endif
        </div>
        <div>
            <p class="font-medium text-neutral-800">{{ $clientName }}</p>
            <p class="text-sm text-neutral-600">{{ $service }}</p>
            @if($staff)
                <p class="text-xs text-neutral-500">with {{ $staff }}</p>
            @endif
        </div>
    </div>
    <div class="text-right">
        <p class="text-sm font-medium text-neutral-800">{{ $time }}</p>
        @if($date)
            <p class="text-xs text-neutral-600">{{ $date }}</p>
        @endif
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }} mt-1">
            {{ ucfirst($status) }}
        </span>
    </div>
</div>
