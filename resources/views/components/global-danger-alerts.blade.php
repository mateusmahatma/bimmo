@php($alerts = $globalDangerAlerts ?? [])

@if(!empty($alerts))
    <div class="global-danger-alerts">
        @foreach($alerts as $alert)
            <div
                class="alert alert-{{ $alert['severity'] ?? 'danger' }} alert-dismissible fade show global-danger-alert mb-2"
                role="alert"
                data-alert-id="{{ $alert['id'] ?? '' }}"
                data-alert-version="{{ $alert['version'] ?? '' }}"
            >
                <div class="d-flex align-items-start gap-2">
                    <i class="bi bi-exclamation-triangle-fill mt-1"></i>
                    <div class="flex-grow-1">
                        @if(!empty($alert['title']))
                            <div class="fw-bold">{{ $alert['title'] }}</div>
                        @endif
                        @if(!empty($alert['message']))
                            <div class="small">{{ $alert['message'] }}</div>
                        @endif
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endforeach
    </div>

    <style>
        .global-danger-alerts {
            position: sticky;
            top: 0;
            z-index: 1015;
            padding-top: 10px;
            margin-top: 10px;
        }
        .global-danger-alert {
            position: relative;
            padding: 8px 12px;
            padding-right: 34px;
            border-radius: 10px;
            line-height: 1.2;
        }
        .global-danger-alert .fw-bold {
            font-size: 0.9rem;
            margin-bottom: 2px;
        }
        .global-danger-alert .small {
            font-size: 0.82rem;
        }
        .global-danger-alert .btn-close {
            position: absolute;
            top: 18px;
            right: 12px;
            padding: 0;
            width: 0.6em;
            height: 0.6em;
        }
        @media (max-width: 767.98px) {
            .global-danger-alerts {
                top: calc(48px + env(safe-area-inset-top));
                padding-top: 8px;
                margin-top: 8px;
            }
            .global-danger-alert .btn-close {
                top: 17px;
                right: 10px;
            }
        }
    </style>
@endif
