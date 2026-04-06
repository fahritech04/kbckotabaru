@php
    $description = $description ?? null;
    $primaryAction = $primaryAction ?? null;
    $secondaryAction = $secondaryAction ?? null;
@endphp

<section class="surface-card p-5 sm:p-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="admin-headline">{{ $title }}</h1>
            @if ($description)
                <p class="admin-subline">{{ $description }}</p>
            @endif
        </div>
        <div class="flex flex-wrap gap-2">
            @if ($secondaryAction)
                <a href="{{ $secondaryAction['url'] }}" class="{{ $secondaryAction['class'] ?? 'btn-secondary' }}">{{ $secondaryAction['label'] }}</a>
            @endif
            @if ($primaryAction)
                <a href="{{ $primaryAction['url'] }}" class="{{ $primaryAction['class'] ?? 'btn-accent' }}">{{ $primaryAction['label'] }}</a>
            @endif
        </div>
    </div>
</section>
