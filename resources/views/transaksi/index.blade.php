@extends('layouts.main')

@section('title','Arus Kas')

@section('container')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Arus Kas</h4>

    <div class="d-flex gap-2">
        <a href="{{ route('transaksi.create') }}"
            class="btn btn-primary btn-sm">
            + Tambah Transaksi
        </a>

        {{-- IMPORT EXCEL --}}
        <button class="btn btn-success btn-sm"
            data-bs-toggle="modal"
            data-bs-target="#importExcelModal">
            Import Excel
        </button>

        <a href="{{ route('transaksi.download.template') }}"
            class="btn btn-outline-secondary btn-sm">
            Download Template
        </a>

    </div>
</div>


<div class="d-flex gap-2 mb-3">
    <a href="{{ route('transaksi.export.pdf', request()->query()) }}"
        class="btn btn-outline-danger btn-sm">
        Export PDF
    </a>

    <a href="{{ route('transaksi.export.excel', request()->query()) }}"
        class="btn btn-outline-success btn-sm">
        Export Excel
    </a>
</div>


{{-- FILTER --}}
<form method="GET" class="card mb-3">
    <div class="card-body row g-3">
        <div class="col-md-3">
            <label class="form-label">Tanggal Mulai</label>
            <input type="date" name="start_date" class="form-control"
                value="{{ request('start_date') }}">
        </div>

        <div class="col-md-3">
            <label class="form-label">Tanggal Akhir</label>
            <input type="date" name="end_date" class="form-control"
                value="{{ request('end_date') }}">
        </div>

        <div class="col-md-3">
            <label class="form-label">Jenis Pemasukan</label>
            <select name="pemasukan" class="form-select">
                <option value="">Semua</option>
                @foreach ($listPemasukan as $item)
                <option value="{{ $item->id }}"
                    @selected(request('pemasukan')==$item->id)>
                    {{ $item->nama }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label">Jenis Pengeluaran</label>
            <select name="pengeluaran" class="form-select">
                <option value="">Semua</option>
                @foreach ($listPengeluaran as $item)
                <option value="{{ $item->id }}"
                    @selected(request('pengeluaran')==$item->id)>
                    {{ $item->nama }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="col-12 d-flex gap-2">
            <button class="btn btn-primary">Terapkan Filter</button>
            <a href="{{ route('transaksi.index') }}" class="btn btn-outline-secondary">
                Reset
            </a>
        </div>
    </div>
</form>


{{-- SUMMARY --}}
<div class="card mb-3">
    <div class="card-body">
        <table class="table table-sm mb-0">
            <tr>
                <th>Total Pendapatan</th>
                <td>Rp {{ number_format($totalPemasukan,0,',','.') }}</td>
            </tr>
            <tr>
                <th>Total Pengeluaran</th>
                <td>Rp {{ number_format($totalPengeluaran,0,',','.') }}</td>
            </tr>
            <tr>
                <th>Laba Bersih</th>
                <td>
                    <strong class="{{ $netIncome >= 0 ? 'text-success' : 'text-danger' }}">
                        Rp {{ number_format($netIncome,0,',','.') }}
                    </strong>
                </td>
            </tr>
        </table>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header">
        <strong>Rincian Pendapatan</strong>
    </div>
    <div class="card-body p-0">
        <table class="table table-bordered table-sm mb-0">
            <thead class="table-light">
                <tr>
                    <th>Jenis Pendapatan</th>
                    <th class="text-end">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($summaryPemasukan as $row)
                <tr>
                    <td>{{ $row->pemasukanRelation?->nama ?? '-' }}</td>
                    <td class="text-end">
                        Rp {{ number_format($row->total,0,',','.') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="2" class="text-center text-muted">
                        Tidak ada data
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        <strong>Rincian Pengeluaran</strong>
    </div>
    <div class="card-body p-0">
        <table class="table table-bordered table-sm mb-0">
            <thead class="table-light">
                <tr>
                    <th>Jenis Pengeluaran</th>
                    <th class="text-end">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($summaryPengeluaran as $row)
                <tr>
                    <td>{{ $row->pengeluaranRelation?->nama ?? '-' }}</td>
                    <td class="text-end">
                        Rp {{ number_format($row->total,0,',','.') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="2" class="text-center text-muted">
                        Tidak ada data
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>


{{-- TABLE --}}
<div class="card">
    <div class="card-body table-responsive">
        @php
        $currentSort = request('sort');
        $currentDir = request('direction','desc');

        function sortLink($column) {
        $dir = request('direction') === 'asc' ? 'desc' : 'asc';
        return request()->fullUrlWithQuery([
        'sort' => $column,
        'direction' => $dir
        ]);
        }
        @endphp
        <table class="table table-bordered table-striped">
            <thead class="table-light">
                <tr>
                    <th>No</th>

                    <th>
                        <a href="{{ sortLink('tgl_transaksi') }}" class="text-decoration-none">
                            Tanggal
                            @if ($currentSort === 'tgl_transaksi')
                            {!! $currentDir === 'asc' ? '↑' : '↓' !!}
                            @endif
                        </a>
                    </th>

                    <th>Jenis Pemasukan</th>

                    <th class="text-end">
                        <a href="{{ sortLink('nominal_pemasukan') }}" class="text-decoration-none">
                            Nominal Masuk
                            @if ($currentSort === 'nominal_pemasukan')
                            {!! $currentDir === 'asc' ? '↑' : '↓' !!}
                            @endif
                        </a>
                    </th>

                    <th>Jenis Pengeluaran</th>

                    <th class="text-end">
                        <a href="{{ sortLink('nominal') }}" class="text-decoration-none">
                            Nominal Keluar
                            @if ($currentSort === 'nominal')
                            {!! $currentDir === 'asc' ? '↑' : '↓' !!}
                            @endif
                        </a>
                    </th>

                    <th>Keterangan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($transaksi as $row)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $row->tgl_transaksi }}</td>
                    <td>{{ $row->pemasukanRelation?->nama ?? '-' }}</td>
                    <td class="text-end">
                        {{ number_format($row->nominal_pemasukan,0,',','.') }}
                    </td>
                    <td>{{ $row->pengeluaranRelation?->nama ?? '-' }}</td>
                    <td class="text-end">
                        {{ number_format($row->nominal,0,',','.') }}
                    </td>
                    <td>
                        @if($row->keterangan)
                        <ol class="mb-0 ps-3">
                            @foreach(preg_split("/\r\n|\n|\r/", $row->keterangan) as $item)
                            @if(trim($item) !== '')
                            <li>{{ $item }}</li>
                            @endif
                            @endforeach
                        </ol>
                        @else
                        <span class="text-muted">-</span>
                        @endif
                    </td>

                    <td>@include('transaksi._aksi',['row'=>$row])</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted">
                        Tidak ada data transaksi
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{ $transaksi->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
</div>

<div class="modal fade" id="importExcelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('transaksi.import.excel') }}"
            method="POST"
            enctype="multipart/form-data"
            class="modal-content">
            @csrf

            <div class="modal-header">
                <h5 class="modal-title">Import Transaksi (Excel)</h5>
                <button type="button" class="btn-close"
                    data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">File Excel</label>
                    <input type="file"
                        name="file"
                        class="form-control"
                        accept=".xlsx,.xls,.csv"
                        required>
                </div>

                <div class="alert alert-info small mb-0">
                    Format file harus sesuai template.
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary"
                    data-bs-dismiss="modal">
                    Batal
                </button>
                <button class="btn btn-success">
                    Import
                </button>
            </div>
        </form>
    </div>
</div>
@endsection