<div class="card card-dashboard border-0 shadow-none {{ $uiStyle === 'milenial' ? 'm-glass-container' : '' }}" style="overflow: hidden;">
    <div class="card-header d-flex justify-content-between align-items-center {{ $uiStyle === 'milenial' ? 'm-card-header-vibrant bg-transparent' : '' }}">
        <div>
            <h5 class="card-title mb-0 fw-bold text-dark {{ $uiStyle === 'milenial' ? 'm-card-title-vibrant' : '' }}" style="font-size: 1rem;">
                {{ __('Financial Goals') }}
            </h5>
            <p class="text-muted mb-0 mt-1" style="font-size: 0.82rem;">{{ __('Track your targets and deadlines.') }}</p>
        </div>
        <a href="{{ route('tujuan-keuangan.index') }}"
            class="btn {{ $uiStyle === 'milenial' ? 'btn-light border-0' : 'btn-outline-primary' }} btn-sm rounded-0 px-3 d-flex align-items-center gap-1">
            <i class="bi bi-bullseye"></i>
            <span class="fw-semibold" style="font-size: 0.75rem;">{{ __('Manage') }}</span>
        </a>
    </div>

    <div class="card-body p-4">
        @if(($financialGoalsActiveCount ?? 0) <= 0)
            <div class="text-center py-4">
                <div class="mb-2 text-muted">
                    <i class="bi bi-flag" style="font-size: 2rem;"></i>
                </div>
                <p class="mb-1 fw-semibold text-dark">{{ __('No active goals yet.') }}</p>
                <p class="text-muted mb-0" style="font-size: 0.82rem;">{{ __('Create a goal to start tracking progress here.') }}</p>
            </div>
        @else
            <div class="text-center mb-4">
                <h2 class="fw-bold mb-0 text-primary {{ $uiStyle === 'milenial' ? 'fw-extrabold' : '' }}" style="font-size: 2.4rem; letter-spacing: -1px;">
                    {{ number_format($financialGoalsOverallPercent ?? 0, 1) }}%
                </h2>
                <p class="section-label mt-1 mb-0">
                    {{ __('Overall progress') }} •
                    <span class="fw-semibold">{{ $financialGoalsActiveCount ?? 0 }}</span> {{ __('active') }}
                </p>
            </div>

            <div class="progress-clean mb-4">
                <div class="progress-bar progress-bar-striped progress-bar-animated"
                     role="progressbar"
                     style="width: {{ min(100, $financialGoalsOverallPercent ?? 0) }}%; height: 100%; border-radius: 100px; background: {{ $uiStyle === 'milenial' ? 'var(--m-primary-gradient)' : '#2563eb' }};"
                     aria-valuenow="{{ $financialGoalsOverallPercent ?? 0 }}"
                     aria-valuemin="0"
                     aria-valuemax="100">
                </div>
            </div>

            <div class="row g-3 text-center mb-4">
                <div class="col-6">
                    <div class="p-3 rounded-0 {{ $uiStyle === 'milenial' ? 'bg-primary bg-opacity-5' : 'stat-card-inner' }}">
                        <p class="section-label mb-1">{{ __('Collected') }}</p>
                        <h6 class="fw-bold mb-0 text-dark" id="goals-total-collected" style="font-size: 0.88rem;">{{ $financialGoalsTotalCollectedView ?? 'Rp 0' }}</h6>
                    </div>
                </div>
                <div class="col-6">
                    <div class="p-3 rounded-0 stat-card-inner">
                        <p class="section-label mb-1">{{ __('Target') }}</p>
                        <h6 class="fw-bold mb-0 text-dark" id="goals-total-target" style="font-size: 0.88rem;">{{ $financialGoalsTotalTargetView ?? 'Rp 0' }}</h6>
                    </div>
                </div>
            </div>

            @if(!empty($financialGoalsNextDue))
                <p class="text-muted text-center mb-4" style="font-size: 0.82rem;">
                    <i class="bi bi-calendar-event me-1"></i>
                    {{ __('Next deadline') }}:
                    <strong class="text-dark">{{ \Carbon\Carbon::parse($financialGoalsNextDue)->translatedFormat('d M Y') }}</strong>
                </p>
            @endif

            @if(!empty($financialGoalsItems) && count($financialGoalsItems) > 0)
                <div class="d-flex flex-column gap-3">
                    @foreach($financialGoalsItems as $goal)
                        @php
                            $target = (float) ($goal['target'] ?? 0);
                            $collected = (float) ($goal['collected'] ?? 0);
                            $percent = $target > 0 ? min(100, ($collected / $target) * 100) : 0;
                            $due = $goal['due'] ?? null;
                        @endphp
                        <div class="p-3 rounded-0 {{ $uiStyle === 'milenial' ? 'bg-primary bg-opacity-5' : 'stat-card-inner' }}">
                            <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <p class="mb-0 fw-semibold text-dark" style="font-size: 0.9rem; line-height: 1.2;">
                                            {{ $goal['name'] ?? '-' }}
                                        </p>
                                        @if(!empty($goal['priority']))
                                            @php
                                                $priority = (string) $goal['priority'];
                                                $priorityClass = $priority === 'High' ? 'danger' : ($priority === 'Medium' ? 'warning' : 'secondary');
                                            @endphp
                                            <span class="badge bg-{{ $priorityClass }}" style="font-size: 0.65rem;">{{ $priority }}</span>
                                        @endif
                                    </div>
                                    <p class="text-muted mb-0 mt-1" style="font-size: 0.78rem;">
                                        {{ __('Collected') }}: <span class="fw-semibold text-dark">{{ $goal['collectedView'] ?? 'Rp 0' }}</span>
                                        <span class="mx-1">/</span>
                                        {{ __('Target') }}: <span class="fw-semibold text-dark">{{ $goal['targetView'] ?? 'Rp 0' }}</span>
                                        @if($due)
                                            <span class="mx-1">•</span>
                                            {{ __('Due') }}: <span class="fw-semibold text-dark">{{ \Carbon\Carbon::parse($due)->translatedFormat('d M Y') }}</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold text-primary" style="font-size: 0.9rem;">{{ number_format($percent, 0) }}%</div>
                                </div>
                            </div>
                            <div class="progress-clean" style="height: 10px;">
                                <div class="progress-bar"
                                     role="progressbar"
                                     style="width: {{ $percent }}%; height: 100%; border-radius: 100px; background: {{ $uiStyle === 'milenial' ? 'var(--m-primary-gradient)' : '#2563eb' }};"
                                     aria-valuenow="{{ $percent }}"
                                     aria-valuemin="0"
                                     aria-valuemax="100">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        @endif
    </div>
</div>

