@props([
    'type' => 'submit',
    'action' => 'update',
    'title' => null,
    'text' => null,
    'class' => 'bg-pink-500 hover:bg-pink-600 text-white px-4 py-2 rounded-lg'
])

@php
    $defaultTitles = [
        'create' => 'Create New Record',
        'update' => 'Update Record', 
        'delete' => 'Delete Record'
    ];
    
    $defaultTexts = [
        'create' => 'Are you sure you want to create this record?',
        'update' => 'Are you sure you want to update this record?',
        'delete' => 'This action cannot be undone!'
    ];
    
    $confirmTitle = $title ?? $defaultTitles[$action] ?? 'Confirm Action';
    $confirmText = $text ?? $defaultTexts[$action] ?? 'Are you sure you want to proceed?';
    $confirmFunction = 'confirm' . ucfirst($action);
@endphp

<button 
    type="button" 
    onclick="{{ $confirmFunction }}(this.form, '{{ $confirmTitle }}', '{{ $confirmText }}')" 
    {{ $attributes->merge(['class' => $class]) }}
>
    {{ $slot }}
</button>
