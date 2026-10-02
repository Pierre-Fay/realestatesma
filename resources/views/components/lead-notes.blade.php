@props(['notes' => null])

@if ($notes !== null && $notes !== '')
    <details {{ $attributes->merge(['class' => 'max-w-64']) }}>
        <summary class="cursor-pointer">{{ __('View notes') }}</summary>
        <p class="text-muted-foreground whitespace-pre-wrap break-words text-sm">{{ $notes }}</p>
    </details>
@else
    —
@endif
