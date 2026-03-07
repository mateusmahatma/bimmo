@extends('layouts.main')

@section('title', 'Financial Goals')

@push('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.bootstrap5.min.css">
<style>
    .goal-card {
        transition: transform 0.2s;
        border-radius: 15px;
    }
    .goal-card:hover {
        transform: translateY(-5px);
    }
    .priority-high { border-left: 5px solid #dc3545; }
    .priority-medium { border-left: 5px solid #ffc107; }
    .priority-low { border-left: 5px solid #0dcaf0; }
</style>
@endpush

@section('container')

<div class="pagetitle mb-4">
    <h1>Financial Goals</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Financial Goals</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row mb-4">
        <div class="col-md-12 text-end">
            <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalAddGoal">
                <i class="bi bi-plus-lg me-1"></i> Add New Goal
            </button>
        </div>
    </div>

    <div class="row" id="goalsContainer">
        <!-- Goals will be loaded here or we can use a table -->
    </div>

    <div class="card card-dashboard border-0 shadow-sm" style="border-radius: 12px;">
        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="card-title mb-0 fw-bold text-dark">Track Your Goals</h5>
                <p class="text-muted small mb-0 mt-1">Monitor progress and reach your financial targets.</p>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="goalsTable" class="table table-hover align-middle mb-0" style="width:100%">
                    <thead class="bg-light">
                        <tr>
                            <th class="py-3 text-center">No</th>
                            <th class="py-3">Goal Name</th>
                            <th class="py-3">Category</th>
                            <th class="py-3">Target</th>
                            <th class="py-3">Collected</th>
                            <th class="py-3 text-center">Progress</th>
                            <th class="py-3">Remaining Time</th>
                            <th class="py-3">Rec. Savings/Mo</th>
                            <th class="py-3 text-center">Priority</th>
                            <th class="py-3 text-center">Action</th>
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
        <div class="modal-content border-0 shadow" style="border-radius: 15px;">
            <form action="{{ route('tujuan-keuangan.store') }}" method="POST" id="formAddGoal">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Add Financial Goal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Goal Name</label>
                        <input type="text" name="nama_target" class="form-control" placeholder="e.g., New Car, Emergency Fund" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Category</label>
                        <select name="kategori" class="form-select" required>
                            <option value="Savings">Savings</option>
                            <option value="Investment">Investment</option>
                            <option value="Purchase">Purchase</option>
                            <option value="Debt">Debt</option>
                            <option value="Others">Others</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-secondary">Target Amount (Rp)</label>
                            <input type="number" name="nominal_target" id="input_target" class="form-control" placeholder="0" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-secondary">Deadline</label>
                            <input type="date" name="tenggat_waktu" id="input_deadline" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Priority</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="prioritas" value="High" id="p_high" required>
                                <label class="form-check-label" for="p_high">High</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="prioritas" value="Medium" id="p_medium" checked>
                                <label class="form-check-label" for="p_medium">Medium</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="prioritas" value="Low" id="p_low">
                                <label class="form-check-label" for="p_low">Low</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info border-0 mb-0" id="recommendationAlert" style="display: none;">
                        <i class="bi bi-info-circle me-2"></i>
                        <span id="recommendationText"></span>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">Save Goal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Update Progress -->
<div class="modal fade" id="modalProgress" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow" style="border-radius: 15px;">
            <form id="formUpdateProgress" method="POST">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Update Progress</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3 text-center">
                        <h6 id="goalNameProgress" class="text-muted"></h6>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Add Amount (Rp)</label>
                        <input type="number" name="nominal_tambah" class="form-control" placeholder="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Note (Optional)</label>
                        <input type="text" name="keterangan" class="form-control" placeholder="e.g., Monthly savings, Bonus">
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 text-center d-block">
                    <button type="submit" class="btn btn-success w-100 rounded-pill shadow-sm">Add Savings</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Simulation -->
<div class="modal fade" id="modalSimulate" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 15px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Savings Simulation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-4 text-center">
                    <h4 id="simGoalName" class="fw-bold text-primary"></h4>
                    <span id="simTargetInfo" class="text-muted"></span>
                </div>
                
                <div class="mb-4">
                    <label class="form-label d-flex justify-content-between fw-bold">
                        Monthly Savings
                        <span id="simMonthlyValue" class="text-primary">Rp 0</span>
                    </label>
                    <input type="range" class="form-range" id="simSlider" min="100000" max="10000000" step="100000" value="1000000">
                </div>

                <div class="card bg-light border-0" style="border-radius: 12px;">
                    <div class="card-body p-3 text-center">
                        <p class="mb-1 text-muted">Estimated Target Completion</p>
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
        <div class="modal-content border-0 shadow" style="border-radius: 15px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Progress History</h5>
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
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Note</th>
                                <th class="text-center">Action</th>
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
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.4.1/js/responsive.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/tujuan-keuangan.js') }}"></script>
@endpush
