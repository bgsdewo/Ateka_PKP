{{-- Render deretan langkah tracker. Variabel: $steps (array), $rejectedReason (string|null) --}}
@php
    $icons = [
        'doc' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line>',
        'check' => '<path d="M9 11l3 3L22 4"></path><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>',
        'x' => '<circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line>',
        'box' => '<path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line>',
        'truck' => '<path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"></path><path d="M15 18H9"></path><path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.62l-3.48-4.35A1 1 0 0 0 17.52 8H14v10"></path><circle cx="17" cy="18" r="2"></circle><circle cx="7" cy="18" r="2"></circle>',
        'flag' => '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline>',
    ];
    $lastIdx = count($steps) - 1;
@endphp
<div class="atk-stepper-wrapper atk-steps-{{ count($steps) }}">
    @foreach ($steps as $i => $s)
        @php
            $cls = match ($s['state']) {
                'done' => 'step-done' . ($i < $lastIdx ? ' line-completed' : ''),
                'done-rejected' => 'step-done line-rejected',
                'rejected' => 'step-rejected',
                'waiting' => 'step-waiting',
                'current', 'final' => 'step-current',
                default => 'step-upcoming',
            };
        @endphp
        <div class="atk-stepper-item {{ $cls }}">
            <div class="atk-step-circle">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $icons[$s['icon']] ?? '' !!}</svg>
            </div>
            <div class="atk-step-text">
                <div class="atk-step-title">{{ $s['title'] }}</div>
                <div class="atk-step-subtitle">{{ $s['subtitle'] }}</div>
                <div class="atk-step-time">{{ $s['time'] ?: '-' }}</div>
            </div>
        </div>
    @endforeach
</div>
@if (!empty($rejectedReason))
    <div class="alert alert-danger py-2 px-3 mt-3 mb-0 small" role="alert">
        <strong>Alasan penolakan Kasubbag TU:</strong> {{ $rejectedReason }}
    </div>
@endif
