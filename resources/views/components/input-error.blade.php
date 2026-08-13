@props(['messages'])

@if ($messages)
    <p {{ $attributes->merge(['class' => 'mt-2 text-sm text-red-600']) }}>
        {{ $messages[0] }}
    </p>
@endif
