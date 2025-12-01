@props(['href', 'active' => false])

@php
$classes = $active 
    ? 'flex items-center px-4 py-3 text-sm font-medium bg-pink-100 text-pink-700 rounded-xl border-l-4 border-pink-500 transition-colors duration-200 group'
    : 'flex items-center px-4 py-3 text-sm font-medium text-neutral-700 rounded-xl hover:bg-pink-50 hover:text-pink-700 transition-colors duration-200 group';

$iconClasses = $active
    ? 'h-5 w-5 mr-3 text-pink-500'
    : 'h-5 w-5 mr-3 text-neutral-500 group-hover:text-pink-500';
@endphp

<a href="{{ $href }}" 
   {{ $attributes->merge(['class' => $classes]) }}
   onclick="if(window.innerWidth < 1024) { const sidebar = document.getElementById('staff-sidebar') || document.getElementById('admin-sidebar') || document.getElementById('client-sidebar'); const overlay = document.getElementById('sidebar-overlay'); if(sidebar) sidebar.classList.add('-translate-x-full'); if(overlay) overlay.classList.add('hidden'); }">
    <svg class="{{ $iconClasses }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        {{ $icon }}
    </svg>
    {{ $slot }}
</a>
