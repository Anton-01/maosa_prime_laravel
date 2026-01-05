
<div class="alert alert-{{ $alertType }}" role="alert">
    <div class="d-flex gap-4">
        <span><i class="fa-solid {{ $iconClass }} icon-{{ $alertType }}"></i></span>
        <div class="d-flex flex-column gap-2">
            <h6 class="mb-0">{{ $mainMessage }}</h6>
            <p class="mb-0">{{ $description }}</p>
        </div>
    </div>
</div>
