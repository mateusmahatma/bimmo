<div class="modal fade" id="notesModal" tabindex="-1" aria-labelledby="notesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border-0 shadow-lg p-0" style="border-radius: 20px; overflow: hidden; background-color: var(--bs-body-bg);">
            <div class="modal-header border-0 py-4 px-4 align-items-center" style="background-color: var(--bs-tertiary-bg);">
                <div class="d-flex align-items-center gap-3">
                    <i class="bi bi-journal-check-fill fs-2" style="color: #3b82f6;"></i>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="notesModalLabel">{{ __('Personal Reminders') }}</h5>
                        <p class="text-secondary small mb-0">{{ __('Manage your daily tasks and notes.') }}</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Add Note Form -->
                <div class="mb-4">
                    <div class="input-group shadow-sm rounded-pill overflow-hidden border border-secondary border-opacity-25">
                        <input type="text" id="newNoteContent" class="form-control border-0 px-4 py-2 bg-body-tertiary" placeholder="{{ __('What needs to be done?') }}" style="box-shadow: none;">
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
            <div class="modal-footer border-0 py-3 px-4 justify-content-between bg-body-tertiary">
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
        box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175) !important;
    }
    
    .note-card {
        background: var(--bs-body-bg);
        border: 1px solid var(--bs-border-color);
        border-radius: 14px;
        padding: 12px 16px;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    
    .note-card {
        background: var(--bs-body-bg);
        border: 1px solid var(--bs-border-color);
        border-radius: 12px;
        padding: 12px 18px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 10px;
        /* Removed all hover animations and transitions */
    }
    
    .note-card.checked-card {
        background-color: var(--bs-tertiary-bg);
        border-color: var(--bs-border-color-translucent);
    }
 
    .form-check-input {
        background-color: var(--bs-secondary-bg);
        border-color: var(--bs-border-color);
        cursor: pointer;
        width: 1.3rem;
        height: 1.3rem;
    }
 
    .form-check-input:checked {
        background-color: #059669; /* Slightly darker/premium green */
        border-color: #059669;
    }
 
    .note-text {
        font-size: 1rem;
        font-weight: 500;
        line-height: 1.5;
        color: var(--bs-body-color);
        cursor: default;
        word-break: break-word;
    }
 
    .note-text.checked {
        color: #059669 !important;
        text-decoration: line-through;
        opacity: 0.6;
    }
 
    .note-text.unchecked {
        color: #e11d48 !important; /* Premium Crimson/Red */
        opacity: 0.9;
    }
 
    .btn-delete-note {
        color: var(--bs-secondary-color);
        padding: 8px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        background: transparent;
        transition: none;
        opacity: 0.8; /* Make it more visible as requested */
    }
 
    .btn-delete-note:hover {
        color: #e11d48;
        background: rgba(225, 29, 72, 0.1);
        opacity: 1;
    }
 
    #notesList::-webkit-scrollbar {
        width: 5px;
    }
    #notesList::-webkit-scrollbar-track {
        background: transparent;
    }
    #notesList::-webkit-scrollbar-thumb {
        background: var(--bs-border-color);
        border-radius: 10px;
    }

    /* Force high contrast for dark mode if defaults are too grey */
    [data-bs-theme="dark"] .modal-content,
    [data-bs-theme="dark"] .modal-header {
        background-color: #000000 !important;
        border-color: #1a1a1a !important;
    }
    
    [data-bs-theme="dark"] .note-card {
        background-color: #0a0a0a !important; /* Slightly elevated from pure black */
        border-color: #1a1a1a !important;
    }

    [data-bs-theme="dark"] #newNoteContent {
        background-color: #0a0a0a !important;
        color: #ffffff !important;
    }

    [data-bs-theme="light"] .modal-content,
    [data-bs-theme="light"] .modal-header {
        background-color: #ffffff !important;
    }
    
    [data-bs-theme="light"] .note-card {
        background-color: #ffffff !important;
        border-color: #f0f0f0 !important;
    }

    [data-bs-theme="light"] #newNoteContent {
        background-color: #f8f9fa !important;
    }
</style>
