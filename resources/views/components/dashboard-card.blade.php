@props([
    'title' => '',
    'value' => '',
    'subtitle' => '',
    'icon' => '',
    'color' => 'pink',
    'trend' => null,
    'trendValue' => '',
    'href' => null
])

@php
    $colorClasses = [
        'pink' => [
            'bg' => 'bg-gradient-to-br from-pink-500 to-rose-500',
            'hover' => 'hover:from-pink-600 hover:to-rose-600',
            'text' => 'text-pink-600',
            'bgLight' => 'bg-pink-50',
            'border' => 'border-pink-100'
        ],
        'blue' => [
            'bg' => 'bg-gradient-to-br from-blue-500 to-indigo-500',
            'hover' => 'hover:from-blue-600 hover:to-indigo-600',
            'text' => 'text-blue-600',
            'bgLight' => 'bg-blue-50',
            'border' => 'border-blue-100'
        ],
        'green' => [
            'bg' => 'bg-gradient-to-br from-green-500 to-emerald-500',
            'hover' => 'hover:from-green-600 hover:to-emerald-600',
            'text' => 'text-green-600',
            'bgLight' => 'bg-green-50',
            'border' => 'border-green-100'
        ],
        'purple' => [
            'bg' => 'bg-gradient-to-br from-purple-500 to-violet-500',
            'hover' => 'hover:from-purple-600 hover:to-violet-600',
            'text' => 'text-purple-600',
            'bgLight' => 'bg-purple-50',
            'border' => 'border-purple-100'
        ],
        'yellow' => [
            'bg' => 'bg-gradient-to-br from-yellow-500 to-orange-500',
            'hover' => 'hover:from-yellow-600 hover:to-orange-600',
            'text' => 'text-yellow-600',
            'bgLight' => 'bg-yellow-50',
            'border' => 'border-yellow-100'
        ]
    ];

    $colors = $colorClasses[$color] ?? $colorClasses['pink'];
@endphp

<div class="bg-white/80 backdrop-blur-sm rounded-2xl p-6 shadow-pink-lg hover:shadow-pink-xl transition-all duration-300 transform hover:-translate-y-1 {{ $href ? 'cursor-pointer' : '' }}"
     @if($href) onclick="window.location.href='{{ $href }}'" @endif>
    <div class="flex items-center justify-between">
        <div class="flex-1">
            <p class="text-sm font-medium text-neutral-600">{{ $title }}</p>
            <p class="text-3xl font-bold text-neutral-800 mt-1">{{ $value }}</p>
            @if($subtitle)
                <div class="flex items-center mt-2">
                    @if($trend === 'up')
                        <svg class="h-4 w-4 mr-1 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"></path>
                        </svg>
                        <p class="text-sm text-green-600">{{ $trendValue }}</p>
                    @elseif($trend === 'down')
                        <svg class="h-4 w-4 mr-1 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"></path>
                        </svg>
                        <p class="text-sm text-red-600">{{ $trendValue }}</p>
                    @else
                        <p class="text-sm {{ $colors['text'] }}">{{ $subtitle }}</p>
                    @endif
                </div>
            @endif
        </div>
        <div class="h-12 w-12 {{ $colors['bg'] }} rounded-xl flex items-center justify-center">
            @if($icon)
                {!! $icon !!}
            @else
                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            @endif
        </div>
    </div>
</div>
