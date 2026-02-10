@extends('layouts.main')

@section('title', 'Cash Flow')

@section('container')

<div class="pagetitle mb-4">
    <h1>Cash Flow</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Transactions</li>
        </ol>
    </nav>
</div>

<section class="section dashboard">
    <div class="row">

        <!-- SUMMARY CARDS -->
        <div class="col-md-4 mb-4">
            <div class="card-dashboard h-100 d-flex flex-column justify-content-center">
                <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-success-light text-success me-3" style="width: 48px; height: 48px; background: rgba(25, 135, 84, 0.1);">
                        <i class="bi bi-arrow-down-circle fs-3"></i>
                    </div>
                    <div>
                        <h6 class="text-muted small text-uppercase mb-1">Total Income</h6>
                        <h4 class="mb-0 fw-bold text-success">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
             <div class="card-dashboard h-100 d-flex flex-column justify-content-center">
                <div class="d-flex align-items-center">
                   <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-danger-light text-danger me-3" style="width: 48px; height: 48px; background: rgba(220, 53, 69, 0.1);">
                        <i class="bi bi-arrow-up-circle fs-3"></i>
                    </div>
                    <div>
                        <h6 class="text-muted small text-uppercase mb-1">Total Expense</h6>
                        <h4 class="mb-0 fw-bold text-danger">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
             <div class="card-dashboard h-100 d-flex flex-column justify-content-center">
                <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-primary-light text-primary me-3" style="width: 48px; height: 48px; background: rgba(13, 110, 253, 0.1);">
                        <i class="bi bi-wallet2 fs-3"></i>
                    </div>
                    <div>
                        <h6 class="text-muted small text-uppercase mb-1">Net Income</h6>
                        <h4 class="mb-0 fw-bold {{ $netIncome >= 0 ? 'text-success' : 'text-danger' }}">
                            Rp {{ number_format($netIncome, 0, ',', '.') }}
                        </h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- MAIN CARD -->
        <div class="col-12">
            <div class="card-dashboard">

                <!-- TOOLBAR -->
                <form action="{{ route('transaksi.index') }}" method="GET" class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
                    <!-- Preserve Category Filters -->
                    @if(is_array(request('pemasukan')))
                        @foreach(request('pemasukan') as $p)
                            <input type="hidden" name="pemasukan[]" value="{{ $p }}">
                        @endforeach
                    @else
                        <input type="hidden" name="pemasukan" value="{{ request('pemasukan') }}">
                    @endif

                    @if(is_array(request('pengeluaran')))
                        @foreach(request('pengeluaran') as $p)
                            <input type="hidden" name="pengeluaran[]" value="{{ $p }}">
                        @endforeach
                    @else
                        <input type="hidden" name="pengeluaran" value="{{ request('pengeluaran') }}">
                    @endif
                    
                    <!-- Search & Date Filter Group -->
                    <div class="d-flex gap-2 flex-wrap align-items-center">
                        <!-- Search (Client-side) -->
                        <div class="search-bar" style="max-width: 200px;">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-search"></i></span>
                                <input type="text" id="searchTransaksi" class="form-control border-start-0 ps-0" placeholder="Search...">
                            </div>
                        </div>

                        <!-- Date Filters -->
                        <div class="d-flex gap-2 align-items-center">
                            <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date') }}" title="Start Date">
                            <span class="text-muted">-</span>
                            <input type="date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date') }}" title="End Date">
                            <button class="btn btn-primary btn-sm" title="Apply Filter"><i class="bi bi-filter"></i></button>
                        </div>
                        
                        <!-- Quick Dates -->
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('transaksi.index', array_merge(request()->query(), ['start_date' => date('Y-m-01'), 'end_date' => date('Y-m-t')])) }}" class="btn btn-outline-secondary" title="This Month">This Month</a>
                            <a href="{{ route('transaksi.index', array_merge(request()->query(), ['start_date' => date('Y-m-01', strtotime('-1 month')), 'end_date' => date('Y-m-t', strtotime('-1 month'))])) }}" class="btn btn-outline-secondary" title="Last Month">Last Month</a>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="d-flex gap-2 flex-wrap">
                        <button type="button" class="btn btn-danger btn-sm d-flex align-items-center gap-2 d-none" id="btnBulkDelete">
                            <i class="bi bi-trash"></i> Delete Selected <span class="badge bg-white text-danger ms-1" id="countSelected">0</span>
                        </button>

                        <button class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                            <i class="bi bi-sliders"></i> More
                        </button>
                        
                        <div class="dropdown">
                            <button class="btn btn-outline-success btn-sm dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-download"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('transaksi.export.excel', request()->query()) }}"><i class="bi bi-file-earmark-excel me-2"></i> Excel</a></li>
                                <li><a class="dropdown-item" href="{{ route('transaksi.export.pdf', request()->query()) }}"><i class="bi bi-file-earmark-pdf me-2"></i> PDF</a></li>
                            </ul>
                        </div>

                         <button type="button" class="btn btn-success btn-sm d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#importExcelModal">
                            <i class="bi bi-upload"></i>
                        </button>

                         <a href="{{ route('transaksi.download.template') }}" class="btn btn-outline-secondary btn-sm" title="Download Template">
                            <i class="bi bi-file-earmark-spreadsheet"></i>
                        </a>

                        <a href="{{ route('transaksi.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-2">
                            <i class="bi bi-plus-lg"></i> Add
                        </a>
                    </div>
                </form>

                <!-- FILTER COLLAPSE -->
                <div class="collapse mb-4 {{ request()->hasAny(['pemasukan', 'pengeluaran']) ? 'show' : '' }}" id="filterCollapse">
                    <div class="card card-body bg-light border-0 p-3">
                        <form method="GET" class="row g-3">
                            <!-- Preserve Date Filters if set -->
                            <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                            <input type="hidden" name="end_date" value="{{ request('end_date') }}">

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Income Category</label>
                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary w-100 text-start d-flex justify-content-between align-items-center" type="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
                                        <span>Select Categories</span>
                                        <span class="badge bg-secondary ms-2" id="count-pemasukan">0</span>
                                    </button>
                                    <ul class="dropdown-menu w-100 p-2 shadow" style="max-height: 250px; overflow-y: auto;">
                                        <li><h6 class="dropdown-header">Select Income Sources</h6></li>
                                        @foreach ($listPemasukan as $item)
                                        <li class="dropdown-item-text">
                                            <div class="form-check">
                                                <input class="form-check-input filter-checkbox-pemasukan" type="checkbox" name="pemasukan[]" value="{{ $item->id }}" id="in_{{ $item->id }}" @checked(in_array($item->id, (array)request('pemasukan', [])))>
                                                <label class="form-check-label w-100" for="in_{{ $item->id }}">
                                                    {{ $item->nama }}
                                                </label>
                                            </div>
                                        </li>
                                        @endforeach
                                        @if($listPemasukan->isEmpty())
                                            <li class="text-muted small text-center py-2">No categories found</li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Expense Category</label>
                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary w-100 text-start d-flex justify-content-between align-items-center" type="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
                                        <span>Select Categories</span>
                                        <span class="badge bg-secondary ms-2" id="count-pengeluaran">0</span>
                                    </button>
                                    <ul class="dropdown-menu w-100 p-2 shadow" style="max-height: 250px; overflow-y: auto;">
                                        <li><h6 class="dropdown-header">Select Expense Types</h6></li>
                                        @foreach ($listPengeluaran as $item)
                                        <li class="dropdown-item-text">
                                            <div class="form-check">
                                                <input class="form-check-input filter-checkbox-pengeluaran" type="checkbox" name="pengeluaran[]" value="{{ $item->id }}" id="out_{{ $item->id }}" @checked(in_array($item->id, (array)request('pengeluaran', [])))>
                                                <label class="form-check-label w-100" for="out_{{ $item->id }}">
                                                    {{ $item->nama }}
                                                </label>
                                            </div>
                                        </li>
                                        @endforeach
                                          @if($listPengeluaran->isEmpty())
                                            <li class="text-muted small text-center py-2">No categories found</li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                            <div class="col-12 d-flex justify-content-end gap-2">
                                <a href="{{ route('transaksi.index') }}" class="btn btn-sm btn-link text-decoration-none text-muted">Reset Filters</a>
                                <button class="btn btn-primary btn-sm">Apply</button>
                            </div>
                        </form>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- DATA TABLE -->
                <div class="table-responsive">
                    @php
                        $currentSort = request('sort');
                        $currentDir = request('direction','desc');

                        // Use anonymous function to prevent redeclaration error
                        $sortLink = function($column) {
                            $dir = request('direction') === 'asc' ? 'desc' : 'asc';
                            return request()->fullUrlWithQuery(['sort' => $column, 'direction' => $dir]);
                        };
                    @endphp
                    <table id="transaksiTable" class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 40px;" class="text-center">
                                    <input class="form-check-input" type="checkbox" id="checkAll">
                                </th>
                                <th style="width: 5%;">No</th>
                                <th style="width: 15%;">
                                    <a href="{{ $sortLink('tgl_transaksi') }}" class="text-decoration-none text-dark d-flex align-items-center gap-1">
                                        Date @if ($currentSort === 'tgl_transaksi') <i class="bi bi-arrow-{{ $currentDir === 'asc' ? 'up' : 'down' }}"></i> @endif
                                    </a>
                                </th>
                                <th style="width: 25%;">Category</th>
                                <th style="width: 30%;">Description</th>
                                <th style="width: 15%;" class="text-end">
                                    <a href="{{ $sortLink('nominal_pemasukan') }}" class="text-decoration-none text-dark d-flex align-items-center justify-content-end gap-1">
                                        Amount @if ($currentSort === 'nominal_pemasukan' || $currentSort === 'nominal') <i class="bi bi-arrow-{{ $currentDir === 'asc' ? 'up' : 'down' }}"></i> @endif
                                    </a>
                                </th>
                                <th style="width: 10%;" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($transaksi as $row)
                            <tr>
                                <td class="text-center">
                                    <input class="form-check-input check-item" type="checkbox" value="{{ $row->id }}">
                                </td>
                                <td>{{ $loop->iteration + ($transaksi->currentPage() - 1) * $transaksi->perPage() }}</td>
                                <td>
                                    <div class="fw-bold">{{ \Carbon\Carbon::parse($row->tgl_transaksi)->format('d M Y') }}</div>
                                    <div class="small text-muted">{{ \Carbon\Carbon::parse($row->tgl_transaksi)->format('l') }}</div>
                                </td>
                                <td>
                                    @if($row->nominal_pemasukan > 0)
                                        <span class="badge bg-success-light text-success border border-success-subtle">
                                            <i class="bi bi-arrow-down-left me-1"></i> {{ $row->pemasukanRelation?->nama ?? 'Income' }}
                                        </span>
                                    @else
                                        <span class="badge bg-danger-light text-danger border border-danger-subtle">
                                            <i class="bi bi-arrow-up-right me-1"></i> {{ $row->pengeluaranRelation?->nama ?? 'Expense' }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($row->keterangan)
                                        <div class="small text-muted" style="max-height: 60px; overflow-y: auto;">
                                            @php
                                                $descLines = array_filter(preg_split('/\r\n|\r|\n/', $row->keterangan), function($l) { return trim($l) !== ''; });
                                            @endphp
                                            @if(count($descLines) > 1)
                                                <ol class="ps-3 mb-0">
                                                    @foreach($descLines as $line)
                                                        <li>{{ trim($line) }}</li>
                                                    @endforeach
                                                </ol>
                                            @else
                                                {!! nl2br(e($row->keterangan)) !!}
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td class="text-end fw-bold {{ $row->nominal_pemasukan > 0 ? 'text-success' : 'text-danger' }}">
                                    @if($row->nominal_pemasukan > 0)
                                        + Rp {{ number_format($row->nominal_pemasukan, 0, ',', '.') }}
                                    @else
                                        - Rp {{ number_format($row->nominal, 0, ',', '.') }}
                                    @endif
                                </td>
                                <td class="text-center">
                                    @include('transaksi._aksi',['row'=>$row])
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <img src="{{ asset('img/no-data.svg') }}" alt="No Data" style="width: 100px; opacity: 0.5;" class="mb-3">
                                    <p class="text-muted">No transactions found.</p>
                                    <a href="{{ route('transaksi.create') }}" class="btn btn-primary btn-sm mt-2">Add First Transaction</a>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- PAGINATION -->
                <div class="d-flex justify-content-end mt-4">
                    {{ $transaksi->withQueryString()->links('pagination::bootstrap-5') }}
                </div>

            </div>
        </div>
    </div>
</section>

<!-- Import Modal -->
<div class="modal fade" id="importExcelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('transaksi.importTest') }}" method="POST" enctype="multipart/form-data" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Import Excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Select Excel File</label>
                    <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.csv" required>
                    <div class="form-text">Supported formats: .xlsx, .xls, .csv</div>
                </div>
                <div class="alert alert-info d-flex align-items-center small" role="alert">
                    <i class="bi bi-info-circle me-2 fs-5"></i>
                    <div>
                        Please use the provided template to ensure correct data formatting.
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Import Data</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Simple client-side search for visible rows
        const searchInput = document.getElementById('searchTransaksi');
        const table = document.getElementById('transaksiTable');
        
        if (searchInput && table) {
            const rows = table.querySelectorAll('tbody tr');
            searchInput.addEventListener('keyup', function() {
                const keyword = this.value.toLowerCase().trim();
                rows.forEach(row => {
                    const rowText = row.innerText.toLowerCase();
                    row.style.display = rowText.includes(keyword) ? '' : 'none';
                });
            });
        }

        // Checkbox Dropdown Logic
        function updateCount(name, badgeId) {
            const checkboxes = document.querySelectorAll(`input[name="${name}"]`);
            const badge = document.getElementById(badgeId);
            if(checkboxes && badge) {
                const count = Array.from(checkboxes).filter(c => c.checked).length;
                badge.textContent = count;
                badge.classList.toggle('bg-primary', count > 0);
                badge.classList.toggle('bg-secondary', count === 0);
            }
        }

        // Initialize and Listen - Pemasukan
        updateCount('pemasukan[]', 'count-pemasukan');
        document.querySelectorAll('.filter-checkbox-pemasukan').forEach(cb => {
            cb.addEventListener('change', () => updateCount('pemasukan[]', 'count-pemasukan'));
        });

        // Initialize and Listen - Pengeluaran
        updateCount('pengeluaran[]', 'count-pengeluaran');
        document.querySelectorAll('.filter-checkbox-pengeluaran').forEach(cb => {
            cb.addEventListener('change', () => updateCount('pengeluaran[]', 'count-pengeluaran'));
        });
        // ========================
        // BULK DELETE LOGIC
        // ========================
        const checkAll = document.getElementById('checkAll');
        const checkItems = document.querySelectorAll('.check-item');
        const btnBulkDelete = document.getElementById('btnBulkDelete');
        const countSelected = document.getElementById('countSelected');

        function updateBulkDeleteUI() {
            const checked = document.querySelectorAll('.check-item:checked');
            const count = checked.length;
            
            if (countSelected) countSelected.textContent = count;
            
            if (btnBulkDelete) {
                if (count > 0) {
                    btnBulkDelete.classList.remove('d-none');
                } else {
                    btnBulkDelete.classList.add('d-none');
                }
            }

            // Update checkAll state
            if (checkAll && checkItems.length > 0) {
                checkAll.checked = checked.length === checkItems.length;
                checkAll.indeterminate = checked.length > 0 && checked.length < checkItems.length;
            }
        }

        if (checkAll) {
            checkAll.addEventListener('change', function() {
                const isChecked = this.checked;
                document.querySelectorAll('.check-item').forEach(item => {
                    item.checked = isChecked;
                });
                updateBulkDeleteUI();
            });
        }

        if (checkItems) {
            checkItems.forEach(item => {
                item.addEventListener('change', updateBulkDeleteUI);
            });
        }

        if (btnBulkDelete) {
            btnBulkDelete.addEventListener('click', function() {
                const checked = document.querySelectorAll('.check-item:checked');
                const ids = Array.from(checked).map(cb => cb.value);

                if (ids.length === 0) return;

                if (confirm(`Are you sure you want to delete ${ids.length} transaction(s)?`)) {
                    // Show loading state
                    const originalText = this.innerHTML;
                    this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Deleting...';
                    this.disabled = true;

                    fetch("{{ route('transaksi.bulkDelete') }}", {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({ ids: ids })
                    })
                    .then(response => {
                        if (response.ok) {
                            return response.json();
                        }
                        throw new Error('Something went wrong');
                    })
                    .then(data => {
                        window.location.reload();
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Failed to delete transactions. Please try again.');
                        this.innerHTML = originalText;
                        this.disabled = false;
                    });
                }
            });
        }
    });
</script>
@endpush