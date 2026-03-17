<div class="modal fade" id="notesModal" tabindex="-1" aria-labelledby="notesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border-0 shadow-lg p-0" style="border-radius: 20px; overflow: hidden; background-color: #000000;">
            <div class="modal-header border-0 py-4 px-4 align-items-center" style="background-color: #0a0a0a;">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-box bg-primary bg-opacity-20 text-primary rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                        <i class="bi bi-journal-check fs-4"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0 text-white" id="notesModalLabel">{{ __('Personal Reminders') }}</h5>
                        <p class="text-secondary small mb-0">{{ __('Manage your daily tasks and notes.') }}</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" style="background-color: #000000;">
                <!-- Add Note Form -->
                <div class="mb-4">
                    <div class="input-group shadow-sm rounded-pill overflow-hidden border border-secondary border-opacity-25">
                        <input type="text" id="newNoteContent" class="form-control border-0 px-4 py-2 bg-dark text-white" placeholder="{{ __('What needs to be done?') }}" style="box-shadow: none;">
                        <button class="btn btn-primary px-4 border-0 d-flex align-items-center" type="button" id="addNewNoteBtn">
                            <i class="bi bi-plus-lg me-1"></i>
                            <span class="fw-bold small">{{ __('Add') }}</span>
                        </button>
                    </div>
                </div>

                <!-- Notes List -->
                <div id="notesList" class="d-flex flex-column gap-3" style="max-height: 450px; overflow-y: auto; padding: 2px;">
                    <!-- Notes will be loaded here via AJAX -->
                    <div class="text-center py-5" id="notesLoading">
                        <div class="spinner-border text-primary spinner-border-sm mb-3" role="status"></div>
                        <p class="text-secondary small mb-0">{{ __('Syncing your notes...') }}</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 py-3 px-4 justify-content-between" style="background-color: #0a0a0a;">
                <span class="text-secondary small" id="notesCountLabel">0 {{ __('items total') }}</span>
                <button type="button" class="btn btn-link text-secondary text-decoration-none small p-0" data-bs-dismiss="modal">{{ __('Close') }}</button>
            </div>
        </div>
    </div>
</div>

<style>
    /* Adaptive Corporate Button */
    .btn-light-corporate {
        background: var(--bs-body-bg);
        color: var(--bs-body-color);
        border-color: var(--bs-border-color);
    }
    .btn-light-corporate:hover {
        background: var(--bs-tertiary-bg);
        border-color: var(--bs-border-color-translucent);
    }

    /* Modal Styling */
    #notesModal .modal-content {
        box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.5) !important;
    }
    
    .note-card {
        background: #0f172a; /* Dark Blue-Grey / Charcoal */
        border: 1px solid #1e293b;
        border-radius: 14px;
        padding: 12px 16px;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    
    .note-card:hover {
        border-color: #334155;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3);
    }
    
    .note-card.checked-card {
        background-color: #020617;
        border-color: #0f172a;
        opacity: 0.8;
    }

    .form-check-input {
        background-color: #1e293b;
        border-color: #334155;
    }

    .form-check-input:checked {
        background-color: #10b981;
        border-color: #10b981;
    }

    .note-text {
        font-size: 0.95rem;
        font-weight: 500;
        transition: all 0.3s ease;
        line-height: 1.4;
        color: #f1f5f9; /* Off-white */
    }

    .note-text.checked {
        color: #10b981 !important;
        text-decoration: line-through;
        opacity: 0.6;
    }

    .note-text.unchecked {
        color: #f43f5e !important; /* Premium Rose/Red */
    }

    .btn-delete-note {
        opacity: 0;
        transition: opacity 0.2s ease;
        color: #64748b;
        padding: 4px;
        border-radius: 6px;
    }

    .note-card:hover .btn-delete-note {
        opacity: 1;
    }

    .btn-delete-note:hover {
        color: #f43f5e;
        background: rgba(244, 63, 94, 0.1);
    }

    #notesList::-webkit-scrollbar {
        width: 4px;
    }
    #notesList::-webkit-scrollbar-track {
        background: transparent;
    }
    #notesList::-webkit-scrollbar-thumb {
        background: #1e293b;
        border-radius: 10px;
    }
</style>
