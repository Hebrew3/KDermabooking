@props([
    'title' => '',
    'icon' => '',
    'color' => 'pink',
    'href' => '#'
])

@php
    $colorClasses = [
        'pink' => [
            'bg' => 'bg-gradient-to-br from-pink-500 to-rose-500',
            'hover' => 'hover:bg-pink-100',
            'bgLight' => 'bg-pink-50'
        ],
        'blue' => [
            'bg' => 'bg-gradient-to-br from-blue-500 to-indigo-500',
            'hover' => 'hover:bg-blue-100',
            'bgLight' => 'bg-blue-50'
        ],
        'green' => [
            'bg' => 'bg-gradient-to-br from-green-500 to-emerald-500',
            'hover' => 'hover:bg-green-100',
            'bgLight' => 'bg-green-50'
        ],
        'purple' => [
            'bg' => 'bg-gradient-to-br from-purple-500 to-violet-500',
            'hover' => 'hover:bg-purple-100',
            'bgLight' => 'bg-purple-50'
        ]
    ];

    $colors = $colorClasses[$color] ?? $colorClasses['pink'];
@endphp

<a href="{{ $href }}" class="flex items-center p-3 {{ $colors['bgLight'] }} {{ $colors['hover'] }} rounded-xl transition-colors duration-200 group">
    <div class="h-8 w-8 {{ $colors['bg'] }} rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-200">
        @if($icon)
            {!! $icon !!}
        @else
            <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
        @endif
    </div>
    <span class="text-sm font-medium text-neutral-700 group-hover:text-neutral-900">{{ $title }}</span>
</a>
