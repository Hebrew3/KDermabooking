@props([
    'user',
    'size' => 'md',
    'class' => ''
])

@php
    $sizeClasses = [
        'xs' => 'h-6 w-6',
        'sm' => 'h-8 w-8', 
        'md' => 'h-12 w-12',
        'lg' => 'h-16 w-16',
        'xl' => 'h-20 w-20',
        '2xl' => 'h-24 w-24'
    ];
    
    $iconSizes = [
        'xs' => 'h-3 w-3',
        'sm' => 'h-4 w-4',
        'md' => 'h-6 w-6', 
        'lg' => 'h-8 w-8',
        'xl' => 'h-10 w-10',
        '2xl' => 'h-12 w-12'
    ];
    
    $sizeClass = $sizeClasses[$size] ?? $sizeClasses['md'];
    $iconSize = $iconSizes[$size] ?? $iconSizes['md'];
    $fallbackId = 'avatar-fallback-' . uniqid();
@endphp

@if($user->profile_picture_url)
    <div class="{{ $sizeClass }} {{ $class }} relative">
        <img class="w-full h-full object-cover rounded-full ring-2 ring-pink-200" 
             src="{{ $user->profile_picture_url }}" 
             alt="{{ $user->name }}"
             crossorigin="anonymous"
             referrerpolicy="no-referrer"
             onload="this.nextElementSibling.style.display='none';"
             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
        <div class="w-full h-full bg-pink-100 rounded-full flex items-center justify-center ring-2 ring-pink-200 absolute inset-0">
            <svg class="{{ $iconSize }} text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
        </div>
    </div>
@else
    <div class="{{ $sizeClass }} {{ $class }} bg-pink-100 rounded-full flex items-center justify-center ring-2 ring-pink-200">
        <svg class="{{ $iconSize }} text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
        </svg>
    </div>
@endif
