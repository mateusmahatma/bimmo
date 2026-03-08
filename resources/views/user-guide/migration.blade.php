@extends('layouts.main')

@section('container')
<div class="container-fluid py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-8">
            <!-- Header Section -->
            <div class="text-center mb-5 pb-2">
                @if(session('success'))
                <div class="alert alert-success border-0 shadow-sm rounded-4 p-4 mb-5 d-flex align-items-center justify-content-between text-start scale-in">
                    <div class="d-flex align-items-center">
                        <div class="icon-box bg-success bg-opacity-10 text-success me-3">
                            <i class="bi bi-check2-circle fs-3"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1">{{ session('success') }}</h5>
                            <p class="mb-0 text-muted small">{{ __('Data processed successfully') }}</p>
                        </div>
                    </div>
                    @if(session('redirect_url'))
                    <a href="{{ session('redirect_url') }}" target="_blank" class="btn btn-success px-4 py-2 rounded-pill fw-bold shadow-sm">
                        <i class="bi bi-eye me-2"></i> {{ __('Lihat Data') }} {{ session('redirect_name') }}
                    </a>
                    @endif
                </div>
                @endif

                @if(session('error'))
                <div class="alert alert-danger border-0 shadow-sm rounded-4 p-4 mb-5 d-flex align-items-center text-start">
                    <div class="icon-box bg-danger bg-opacity-10 text-danger me-3">
                        <i class="bi bi-exclamation-triangle fs-3"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-1">{{ __('Gagal Mengunggah') }}</h5>
                        <p class="mb-0 text-muted small">{{ session('error') }}</p>
                    </div>
                </div>
                @endif

                <nav aria-label="breadcrumb" class="mb-3">
                    <ol class="breadcrumb justify-content-center bg-transparent p-0 mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('panduan.index') }}" class="text-primary text-decoration-none">{{ __('User Guide') }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ __('Migrate to Bimmo') }}</li>
                    </ol>
                </nav>
                <h1 class="display-5 fw-bold text-navy mb-3">{{ __('Financial Data Migration') }}</h1>
                <p class="lead text-muted mx-auto" style="max-width: 700px;">{!! __('A practical solution for your <strong class="text-danger">initial data migration</strong> to Bimmo. Seamlessly transfer your entire financial history from other platforms with speed, ease, and systematic organization.') !!}</p>
                <div class="mt-4">
                    <hr class="mx-auto" style="width: 60px; height: 3px; background-color: #0d6efd; border: none; border-radius: 2px;">
                </div>
            </div>

            <!-- Migration Categories Grid -->
            <div class="row g-4 mb-5">
                @foreach($features as $feature)
                <div class="col-md-6 col-lg-6">
                    <div class="card border-0 shadow-sm h-100 corporate-card">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="icon-box bg-{{ $feature['color'] }} bg-opacity-10 text-{{ $feature['color'] }} me-3">
                                    <i class="bi {{ $feature['icon'] }}"></i>
                                </div>
                                <h5 class="fw-bold text-navy mb-0">{{ __($feature['name']) }}</h5>
                            </div>
                            <p class="text-muted small mb-4">{{ __($feature['description']) }}</p>
                            
                            <div class="d-flex gap-2">
                                <a href="{{ route('panduan.pindah.template', ['type' => $feature['id']]) }}" class="btn btn-outline-primary btn-sm flex-fill d-flex align-items-center justify-content-center py-2">
                                    <i class="bi bi-download me-2"></i> {{ __('Template') }}
                                </a>
                                <button type="button" class="btn btn-primary btn-sm flex-fill d-flex align-items-center justify-content-center py-2" 
                                        onclick="openUploadModal('{{ $feature['id'] }}', '{{ __($feature['name']) }}')">
                                    <i class="bi bi-upload me-2"></i> {{ __('Upload') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Professional Footer -->
            <div class="text-center pb-5">
                <p class="text-muted small mb-0">© {{ date('Y') }} BIMMO - {{ __('Systematic Financial Management Solution.') }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-navy" id="uploadModalLabel">{{ __('Upload File') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('panduan.pindah.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="type" id="migrationType">
                <div class="modal-body p-4">
                    <p class="text-muted small mb-4">{{ __('Upload file instruction') }} <span id="categoryName" class="fw-bold text-primary"></span>. {{ __('Ensure format matching template') }}</p>
                    
                    <div id="dropZone" class="drop-zone p-5 text-center mb-3 rounded-4 border-2 border-style-dashed">
                        <div class="drop-zone-content">
                            <i class="bi bi-cloud-arrow-up display-4 text-primary opacity-50 mb-3 block"></i>
                            <p class="fw-medium mb-1">{{ __('Drag and drop file instruction') }}</p>
                            <p class="text-muted smaller">CSV, XLSX (Max 10MB)</p>
                        </div>
                        <input type="file" name="file" id="fileInput" class="d-none" accept=".csv, .xlsx">
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pb-4 px-4">
                    <button type="button" class="btn btn-light px-4 py-2 rounded-pill fw-bold text-muted" data-bs-dismiss="modal">{{ __('Batal') }}</button>
                    <button type="submit" class="btn btn-primary px-4 py-2 rounded-pill fw-bold shadow-sm" id="btnUpload">{{ __('Upload') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    :root {
        --navy: #0f172a;
    }
    .text-navy { color: var(--navy); }
    
    .corporate-card {
        border-radius: 12px;
        border: 1px solid #e2e8f0 !important;
        transition: all 0.2s ease-in-out;
    }
    .corporate-card:hover {
        border-color: #cbd5e1 !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05) !important;
        transform: translateY(-2px);
    }
    
    .icon-box {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
    }
    
    .border-style-dashed {
        border: 2px dashed #e2e8f0;
    }
    
    .drop-zone {
        background-color: #f8fafc;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .drop-zone:hover, .drop-zone.dragover {
        background-color: rgba(13, 110, 253, 0.05);
        border-color: #0d6efd;
    }
    
    .smaller { font-size: 0.75rem; }

    [data-bs-theme="dark"] .text-navy { color: #f8fafc !important; }
    [data-bs-theme="dark"] .corporate-card { border-color: #333 !important; background-color: #242424; }
    [data-bs-theme="dark"] .drop-zone { background-color: #1a1a1a; border-color: #333; }
</style>

<script>
    function openUploadModal(type, name) {
        document.getElementById('migrationType').value = type;
        document.getElementById('categoryName').innerText = name;
        new bootstrap.Modal(document.getElementById('uploadModal')).show();
    }

    document.addEventListener('DOMContentLoaded', function() {
        const dropZone = document.getElementById('dropZone');
        const fileInput = document.getElementById('fileInput');

        dropZone.addEventListener('click', () => fileInput.click());

        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('dragover');
        });

        ['dragleave', 'drop'].forEach(event => {
            dropZone.addEventListener(event, () => dropZone.classList.remove('dragover'));
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            if (e.dataTransfer.files.length) {
                fileInput.files = e.dataTransfer.files;
                updateDropZoneText(e.dataTransfer.files[0].name);
            }
        });

        fileInput.addEventListener('change', () => {
            if (fileInput.files.length) {
                updateDropZoneText(fileInput.files[0].name);
            }
        });

        function updateDropZoneText(fileName) {
            const content = dropZone.querySelector('.drop-zone-content');
            content.innerHTML = `
                <i class="bi bi-file-earmark-check display-4 text-success mb-3 block"></i>
                <p class="fw-bold mb-1">${fileName}</p>
                <p class="text-muted smaller">{{ __('Click to change file') }}</p>
            `;
        }
    });
</script>
@endsection
