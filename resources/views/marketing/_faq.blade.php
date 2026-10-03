<div class="accordion" id="{{ $id }}">
    @foreach ($items as $i => [$q, $a])
        <div class="accordion-item">
            <h3 class="accordion-header" id="{{ $id }}-h{{ $i }}">
                <button class="accordion-button {{ $i ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $id }}-c{{ $i }}" aria-expanded="{{ $i ? 'false' : 'true' }}" aria-controls="{{ $id }}-c{{ $i }}">{{ $q }}</button>
            </h3>
            <div id="{{ $id }}-c{{ $i }}" class="accordion-collapse collapse {{ $i ? '' : 'show' }}" aria-labelledby="{{ $id }}-h{{ $i }}" data-bs-parent="#{{ $id }}">
                <div class="accordion-body">{{ $a }}</div>
            </div>
        </div>
    @endforeach
</div>
@once
    @push('scripts')<script src="/assets/site/bootstrap/bootstrap.min.js" defer></script>@endpush
@endonce
