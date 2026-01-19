@props([
    'message' => null,
    'type' => null,
])

@php
    $resolvedType = $type;
    $resolvedMessage = $message;

    if ($resolvedType === null) {
        if (session('success')) {
            $resolvedType = 'success';
            $resolvedMessage = session('success');
        } elseif (session('warning')) {
            $resolvedType = 'warning';
            $resolvedMessage = session('warning');
        } elseif (session('error')) {
            $resolvedType = 'error';
            $resolvedMessage = session('error');
        }
    }

    if ($resolvedMessage === null && $errors->any()) {
        $resolvedType = 'error';
        $resolvedMessage = 'Revisa los campos marcados. Hay datos inválidos o incompletos.';
    }

    $baseClasses = 'rounded-md p-4 text-sm border';

    $variantClasses = match ($resolvedType) {
        'success' => 'bg-green-50 text-green-800 border-green-200',
        'warning' => 'bg-yellow-50 text-yellow-800 border-yellow-200',
        'error' => 'bg-red-50 text-red-800 border-red-200',
        default => 'bg-gray-50 text-gray-800 border-gray-200',
    };
@endphp

@if ($resolvedMessage)
    <div {{ $attributes->merge(['class' => $baseClasses.' '.$variantClasses]) }}>
        <div class="font-medium">{{ $resolvedMessage }}</div>

        @if ($resolvedType === 'error' && $errors->any())
            <ul class="mt-2 list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif
    </div>
@endif
