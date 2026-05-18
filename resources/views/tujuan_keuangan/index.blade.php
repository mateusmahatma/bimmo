@extends('layouts.main')

@section('title', __('Financial Goals'))

@push('css')
    <link rel="stylesheet" href="{{ asset('vendors/datatables/dataTables.bootstrap5.min.css') }}">
    <style>
        .goal-card {
            transition: transform 0.2s;
        }

        .goal-card:hover {
            transform: translateY(-5px);
        }

        .priority-high {
            border-left: 5px solid #dc3545;
        }

        .priority-medium {
            border-left: 5px solid #ffc107;
        }

        .priority-low {
            border-left: 5px solid #0dcaf0;
        }

        /* Improved Table Styling for Horizontal Scroll */
        #goalsTable th,
        #goalsTable td {
            white-space: nowrap;
            vertical-align: middle;
            padding: 0.75rem 1rem;
        }

        /* Sticky (freeze) columns — controlled via JS (user-selectable) */
        #goalsTable_wrapper .dataTables_scrollHead th.tk-sticky,
        #goalsTable_wrapper .dataTables_scrollBody td.tk-sticky {
            position: sticky;
            left: var(--tk-left, 0px);
            background: #fff;
            z-index: 3;
        }

        /* Header should sit above body sticky cells */
        #goalsTable_wrapper .dataTables_scrollHead th.tk-sticky {
            z-index: 5;
        }

        /* Dark mode background */
        [data-bs-theme="dark"] #goalsTable_wrapper .dataTables_scrollHead th.tk-sticky,
        [data-bs-theme="dark"] #goalsTable_wrapper .dataTables_scrollBody td.tk-sticky {
            background: #1e1e1e;
        }

        /* Visual separator on the last frozen column (set by JS) */
        #goalsTable_wrapper .dataTables_scrollHead th.tk-sticky-last,
        #goalsTable_wrapper .dataTables_scrollBody td.tk-sticky-last {
            box-shadow: 6px 0 10px -8px rgba(0, 0, 0, 0.35);
        }

        /* Make table header labels align nicely with DataTables sort icons */
        #goalsTable thead th {
            vertical-align: middle;
            line-height: 1.2;
        }

        #goalsTable thead th.sorting,
        #goalsTable thead th.sorting_asc,
        #goalsTable thead th.sorting_desc,
        #goalsTable thead th.sorting_asc_disabled,
        #goalsTable thead th.sorting_desc_disabled {
            padding-right: 2rem !important;
            position: relative;
        }

        /* Force DataTables sort icons to be vertically centered (more precise) */
        #goalsTable thead th.sorting::before,
        #goalsTable thead th.sorting::after,
        #goalsTable thead th.sorting_asc::before,
        #goalsTable thead th.sorting_asc::after,
        #goalsTable thead th.sorting_desc::before,
        #goalsTable thead th.sorting_desc::after,
        #goalsTable thead th.sorting_asc_disabled::before,
        #goalsTable thead th.sorting_asc_disabled::after,
        #goalsTable thead th.sorting_desc_disabled::before,
        #goalsTable thead th.sorting_desc_disabled::after {
            top: 50% !important;
            transform: translateY(-50%) !important;
            margin-top: 0 !important;
        }

        /* ============================================================
           DataTables Controls — custom dom (dt-top-bar / dt-bottom-bar)
           Keep spacing consistent with Dana Darurat index
           ============================================================ */
        #goalsTable_wrapper .dt-top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1.25rem;
            background-color: #fff;
            border-bottom: 1px solid #edf2f9;
        }

        #goalsTable_wrapper .dt-bottom-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 0.75rem;
            padding: 0.7rem 1.25rem;
            background-color: #fff;
        }

        [data-bs-theme="dark"] #goalsTable_wrapper .dt-top-bar,
        [data-bs-theme="dark"] #goalsTable_wrapper .dt-bottom-bar {
            background-color: #1e1e1e;
            border-color: rgba(255, 255, 255, 0.07);
        }

        #goalsTable_wrapper .dataTables_length label,
        #goalsTable_wrapper .dataTables_filter label {
            font-size: 0.85rem;
            font-weight: 500;
            color: #6c757d;
            margin-bottom: 0;
            display: flex;
            align-items: center;
            white-space: nowrap;
            gap: 0.4rem;
        }

        #goalsTable_wrapper .dataTables_length select {
            border-radius: 6px !important;
            padding: 0.3rem 1.8rem 0.3rem 0.6rem !important;
            border: 1px solid #dee2e6 !important;
            font-size: 0.85rem;
            box-shadow: none !important;
            height: auto;
        }

        #goalsTable_wrapper .dataTables_filter input {
            border-radius: 4px !important;
            padding: 0.35rem 0.75rem !important;
            border: 1px solid #dee2e6 !important;
            margin-left: 0 !important;
            box-shadow: none !important;
            font-size: 0.85rem;
            outline: none;
            transition: border-color 0.15s ease;
        }

        #goalsTable_wrapper .dataTables_filter input:focus {
            border-color: #0d6efd !important;
            box-shadow: none !important;
        }

        [data-bs-theme="dark"] #goalsTable_wrapper .dataTables_length select,
        [data-bs-theme="dark"] #goalsTable_wrapper .dataTables_filter input {
            background-color: #2b2b2b !important;
            border-color: #444 !important;
            color: #e0e0e0 !important;
        }

        #goalsTable_wrapper .dataTables_info {
            font-size: 0.82rem;
            color: #6c757d;
        }

        #goalsTable_wrapper .pagination {
            margin-bottom: 0;
        }

        /* Keep Freeze Columns dropdown always opening downward (even when alerts appear) */
        #freezeColumnsContainer .dropdown-menu {
            top: 100% !important;
            bottom: auto !important;
            transform: none !important;
            inset: auto auto auto 0 !important;
            margin-top: 0.35rem;
        }

        /* Mobile: stack length + search */
        @media (max-width: 575.98px) {
            #goalsTable_wrapper .dt-top-bar,
            #goalsTable_wrapper .dt-bottom-bar {
                flex-direction: column;
                align-items: stretch;
            }

            #goalsTable_wrapper .dataTables_filter label,
            #goalsTable_wrapper .dataTables_length label {
                justify-content: space-between;
            }

            #goalsTable_wrapper .dataTables_filter input {
                width: 100%;
            }
        }

        /* Hide DataTables sort icons on bulk checkbox column */
        #goalsTable thead th:first-child::before,
        #goalsTable thead th:first-child::after {
            display: none !important;
        }

        #goalsTable thead th:first-child {
            cursor: default !important;
        }

        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e0 transparent;
            padding-bottom: 10px;
            /* Space for scrollbar */
        }

        .table-responsive::-webkit-scrollbar {
            height: 6px;
        }

        .table-responsive::-webkit-scrollbar-track {
            background: transparent;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background-color: #cbd5e0;
        }

        /* Prevent dropdown clipping in small tables */
        .table-responsive {
            min-height: 350px;
            /* Give enough room for dropdowns even with 1 row */
        }
    </style>
@endpush

@section('container')

    <div class="pagetitle mb-4">
        <h1 class="fw-bold mb-1">{{ __('Financial Goals') }}</h1>
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Financial Goals') }}</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row align-items-center mb-4">
            <div class="col-md-8">
                <div class="d-flex flex-wrap gap-2">
                    <div class="filter-group">
                        <select id="filter_kategori" class="form-select border-0 shadow-sm">
                            <option value="">{{ __('All Categories') }}</option>
                            <option value="Savings">{{ __('Savings') }}</option>
                            <option value="Investment">{{ __('Investment') }}</option>
                            <option value="Purchase">{{ __('Purchase') }}</option>
                            <option value="Debt">{{ __('Debt') }}</option>
                            <option value="Others">{{ __('Others') }}</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <select id="filter_prioritas" class="form-select border-0 shadow-sm">
                            <option value="">{{ __('All Priorities') }}</option>
                            <option value="High">{{ __('High') }}</option>
                            <option value="Medium">{{ __('Medium') }}</option>
                            <option value="Low">{{ __('Low') }}</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <button type="button" class="btn btn-primary px-4 shadow-sm" data-bs-toggle="modal"
                    data-bs-target="#modalAddGoal">
                    <i class="bi bi-plus-lg me-1"></i> {{ __('Add New Goal') }}
                </button>
            </div>
        </div>

        <div class="row" id="goalsContainer">
            <!-- Goals will be loaded here or we can use a table -->
        </div>

        <div class="card card-dashboard border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title mb-0 fw-bold text-dark">{{ __('Track Your Goals') }}</h5>
                    <p class="text-muted small mb-0 mt-1">{{ __('Monitor progress and reach your financial targets.') }}
                    </p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <small class="text-muted" id="bulkSelectedCount" data-selected-label="{{ __('selected') }}"
                        style="display:none;">0 {{ __('selected') }}</small>
                    <div id="freezeColumnsContainer" class="d-inline-flex align-items-center">
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button"
                                id="tkFreezeDropdown" data-bs-toggle="dropdown" data-bs-auto-close="outside"
                                data-bs-display="static"
                                aria-expanded="false">
                                Freeze Columns
                            </button>
                            <div class="dropdown-menu dropdown-menu-end p-2" aria-labelledby="tkFreezeDropdown"
                                style="min-width: 240px;">
                                <div class="small text-muted mb-2">Pilih kolom yang mau di-freeze</div>
                                <div class="tk-freeze-list" style="max-height: 220px; overflow:auto;"></div>
                                <div class="d-flex gap-2 mt-2">
                                    <button type="button" class="btn btn-light btn-sm w-50"
                                        data-tk-freeze-clear="1">Clear</button>
                                    <button type="button" class="btn btn-primary btn-sm w-50"
                                        data-tk-freeze-apply="1">Apply</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-outline-danger btn-sm" id="bulkDeleteGoalsBtn" disabled>
                        <i class="bi bi-trash me-1"></i> {{ __('Delete Selected') }}
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table id="goalsTable" class="table table-hover align-middle mb-0" style="width:100%">
                        <thead class="bg-light">
                            <tr>
                                <th class="py-3 text-center">
                                    <input type="checkbox" class="form-check-input" id="selectAllGoals"
                                        aria-label="Select all goals">
                                </th>
                                <th class="py-3 text-center">{{ __('No') }}</th>
                                <th class="py-3">{{ __('Goal Name') }}</th>
                                <th class="py-3">{{ __('Category') }}</th>
                                <th class="py-3">{{ __('Target') }}</th>
                                <th class="py-3">{{ __('Collected') }}</th>
                                <th class="py-3 text-center">{{ __('Progress') }}</th>
                                <th class="py-3">{{ __('Remaining Time') }}</th>
                                <th class="py-3">{{ __('Rec. Savings/Mo') }}</th>
                                <th class="py-3 text-center">{{ __('Priority') }}</th>
                                <th class="py-3 text-center">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal Add Goal -->
    <div class="modal fade" id="modalAddGoal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('tujuan-keuangan.store') }}" method="POST" id="formAddGoal">
                    @csrf
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold">{{ __('Add Financial Goal') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">{{ __('Goal Name') }}</label>
                            <input type="text" name="nama_target" class="form-control"
                                placeholder="{{ __('e.g., New Car, Emergency Fund') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">{{ __('Category') }}</label>
                            <select name="kategori" class="form-select" required>
                                <option value="Savings">{{ __('Savings') }}</option>
                                <option value="Investment">{{ __('Investment') }}</option>
                                <option value="Purchase">{{ __('Purchase') }}</option>
                                <option value="Debt">{{ __('Debt') }}</option>
                                <option value="Others">{{ __('Others') }}</option>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label
                                    class="form-label small fw-bold text-secondary">{{ __('Target Amount (Rp)') }}</label>
                                <input type="number" name="nominal_target" id="input_target" class="form-control"
                                    placeholder="0" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold text-secondary">{{ __('Deadline') }}</label>
                                <input type="date" name="tenggat_waktu" id="input_deadline" class="form-control"
                                    required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">{{ __('Priority') }}</label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="prioritas" value="High"
                                        id="p_high" required>
                                    <label class="form-check-label" for="p_high">{{ __('High') }}</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="prioritas" value="Medium"
                                        id="p_medium" checked>
                                    <label class="form-check-label" for="p_medium">{{ __('Medium') }}</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="prioritas" value="Low"
                                        id="p_low">
                                    <label class="form-check-label" for="p_low">{{ __('Low') }}</label>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info border-0 mb-0" id="recommendationAlert" style="display: none;">
                            <i class="bi bi-info-circle me-2"></i>
                            <span id="recommendationText"></span>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light px-3"
                            data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary px-4 shadow-sm">{{ __('Save Goal') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Update Progress -->
    <div class="modal fade" id="modalProgress" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow">
                <form id="formUpdateProgress" method="POST">
                    @csrf
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold">{{ __('Update Progress') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3 text-center">
                            <h6 id="goalNameProgress" class="text-muted"></h6>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">{{ __('Add Amount (Rp)') }}</label>
                            <input type="number" name="nominal_tambah" class="form-control" placeholder="0" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">{{ __('Note (Optional)') }}</label>
                            <input type="text" name="keterangan" class="form-control"
                                placeholder="{{ __('e.g., Monthly savings, Bonus') }}">
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0 text-center d-block">
                        <button type="submit" class="btn btn-success w-100 shadow-sm">{{ __('Add Savings') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Simulation -->
    <div class="modal fade" id="modalSimulate" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">{{ __('Savings Simulation') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-4 text-center">
                        <h4 id="simGoalName" class="fw-bold text-primary"></h4>
                        <span id="simTargetInfo" class="text-muted"></span>
                    </div>

                    <div class="mb-4">
                        <label class="form-label d-flex justify-content-between fw-bold">
                            {{ __('Monthly Savings') }}
                            <span id="simMonthlyValue" class="text-primary">Rp 0</span>
                        </label>
                        <input type="range" class="form-range" id="simSlider" min="100000" max="10000000"
                            step="100000" value="1000000">
                    </div>

                    <div class="card bg-light border-0">
                        <div class="card-body p-3 text-center">
                            <p class="mb-1 text-muted">{{ __('Estimated Target Completion') }}</p>
                            <h3 id="simResultDate" class="fw-bold text-success mb-0">-</h3>
                            <p id="simMonthsLeft" class="small text-muted mb-0"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal History -->
    <div class="modal fade" id="modalHistory" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">{{ __('Progress History') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <h6 id="historyGoalName" class="fw-bold text-primary mb-0"></h6>
                        <small id="historyGoalTarget" class="text-muted"></small>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover" id="historyTable">
                            <thead>
                                <tr>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Amount') }}</th>
                                    <th>{{ __('Note') }}</th>
                                    <th class="text-center">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody id="historyList">
                                <!-- Logs will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="{{ asset('vendors/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendors/datatables/dataTables.bootstrap5.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/tujuan-keuangan.js') }}?v={{ filemtime(public_path('js/tujuan-keuangan.js')) }}"></script>
@endpush
