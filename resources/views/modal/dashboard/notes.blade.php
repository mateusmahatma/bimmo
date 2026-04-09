<div class="modal fade" id="notesModal" tabindex="-1" aria-labelledby="notesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg p-0" style="border-radius: 20px; overflow: hidden; background-color: var(--bs-body-bg);">
            <div class="modal-header border-0 py-4 px-4 align-items-center" style="background-color: var(--bs-tertiary-bg);">
                <div class="d-flex align-items-center gap-3">
                    <i class="bi bi-sticky-fill fs-2 text-primary"></i>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="notesModalLabel">{{ __('Smart Reminders') }}</h5>
                        <p class="text-secondary small mb-0">{{ __('Manage your tasks with rich formatting.') }}</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Add Note Form -->
                <div class="mb-4">
                    <div id="editor-container" class="bg-body-tertiary rounded-3 mb-2" style="height: 120px; border: 1px solid var(--bs-border-color);"></div>
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <small class="text-muted"><kbd>Enter</kbd> {{ __('to save') }}</small>
                        <div class="d-flex gap-2">
                            <button class="btn btn-secondary btn-sm px-3 rounded-pill d-none" id="cancelEditBtn">{{ __('Cancel') }}</button>
                            <button class="btn btn-primary btn-sm px-4 rounded-pill d-flex align-items-center gap-2" id="addNewNoteBtn">
                                <i class="bi bi-plus-lg"></i>
                                <span class="fw-bold" id="btnNoteText">{{ __('Add Reminder') }}</span>
                            </button>
                        </div>
                    </div>
                </div>
 
                <!-- Notes List -->
                <div id="notesContainer" style="max-height: 500px; overflow-y: auto; padding: 2px;">
                    <!-- Active Notes -->
                    <div class="mb-4">
                        <h6 class="text-uppercase fw-bold text-primary small ls-1 mb-3 d-flex align-items-center gap-2">
                            <i class="bi bi-list-task text-primary"></i>
                            {{ __('Active Reminders') }}
                        </h6>
                        <div id="activeNotesList" class="d-flex flex-column gap-2">
                            <!-- Notes will be loaded here via AJAX -->
                        </div>
                    </div>

                    <!-- Completed Notes -->
                    <div class="mt-4 pt-3 border-top">
                        <h6 class="text-uppercase fw-bold text-secondary small ls-1 mb-3 d-flex align-items-center gap-2">
                            <i class="bi bi-check2-all text-secondary"></i>
                            {{ __('Completed') }}
                        </h6>
                        <div id="completedNotesList" class="d-flex flex-column gap-2 opacity-75">
                            <!-- Completed notes will be loaded here via AJAX -->
                        </div>
                    </div>

                    <div class="text-center py-5 d-none" id="notesLoading">
                        <div class="spinner-border text-primary spinner-border-sm mb-3" role="status"></div>
                        <p class="text-secondary small mb-0">{{ __('Syncing your workspace...') }}</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 py-3 px-4 justify-content-between bg-body-tertiary">
                <span class="text-secondary small" id="notesCountLabel">0 {{ __('reminders found') }}</span>
                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-4" data-bs-dismiss="modal">{{ __('Close') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- Quill.js - Corporate Rich Text -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
 
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
        box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.2) !important;
    }
    
    .note-card {
        background: var(--bs-body-bg);
        border: 1px solid var(--bs-border-color);
        border-radius: 12px;
        padding: 12px 16px;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        transition: border-color 0.2s ease;
    }

    .note-card:hover {
        border-color: var(--bs-primary);
    }
    
    .note-card.checked-card {
        background-color: var(--bs-tertiary-bg);
        border-color: var(--bs-border-color-translucent);
        opacity: 0.8;
    }
 
    .form-check-input {
        cursor: pointer;
        width: 1.2rem;
        height: 1.2rem;
        margin-top: 0.2rem;
    }
 
    .note-text {
        font-size: 0.95rem;
        color: var(--bs-body-color);
        word-break: break-word;
    }

    /* Quill Styles in Corporate Theme */
    .ql-container.ql-snow {
        border: none !important;
        font-family: inherit;
        background: transparent !important;
    }
    .ql-toolbar.ql-snow {
        border: none !important;
        border-bottom: 1px solid var(--bs-border-color) !important;
        background: var(--bs-tertiary-bg) !important;
        border-radius: 8px 8px 0 0;
    }
    .ql-editor {
        font-size: 0.95rem;
        color: inherit !important;
    }
 
    .note-text.checked {
        text-decoration: line-through;
        opacity: 0.6;
    }
 
    .action-btns {
        display: flex;
        gap: 4px;
        opacity: 0;
        transition: opacity 0.2s;
    }

    .note-card:hover .action-btns {
        opacity: 1;
    }

    .btn-note-action {
        color: var(--bs-secondary-color);
        padding: 4px 8px;
        border-radius: 6px;
        border: none;
        background: transparent;
    }
 
    .btn-note-action:hover {
        background: var(--bs-tertiary-bg);
    }

    .btn-note-action.delete:hover {
        color: #e11d48;
        background: rgba(225, 29, 72, 0.1);
    }

    #notesContainer::-webkit-scrollbar {
        width: 6px;
    }
    #notesContainer::-webkit-scrollbar-thumb {
        background: var(--bs-border-color);
        border-radius: 10px;
    }

    /* Force dark mode with hardcoded colors for absolute reliability */
    [data-bs-theme="dark"] #notesModal .modal-content {
        background-color: #1a1d21 !important;
        border: 1px solid #343a40 !important;
    }
    [data-bs-theme="dark"] #notesModal .modal-header {
        background-color: #212529 !important;
    }
    [data-bs-theme="dark"] #notesModal .modal-body {
        background-color: #1a1d21 !important;
    }
    [data-bs-theme="dark"] #notesModal .modal-footer {
        background-color: #212529 !important;
    }
    [data-bs-theme="dark"] #editor-container {
        background-color: #0d1117 !important;
        color: #e6edf3 !important;
        border-color: #30363d !important;
    }
    [data-bs-theme="dark"] .note-card {
        background-color: #0d1117 !important;
        border-color: #30363d !important;
    }
    [data-bs-theme="dark"] .note-card.checked-card {
        background-color: #161b22 !important;
        opacity: 0.7;
    }
    [data-bs-theme="dark"] .note-text {
        color: #e6edf3 !important;
    }
    [data-bs-theme="dark"] .ql-toolbar.ql-snow {
        background-color: #212529 !important;
        border-bottom-color: #30363d !important;
    }
    [data-bs-theme="dark"] .ql-container.ql-snow {
        background-color: transparent !important;
    }
    
    /* Quill Icon Colors in Dark Mode */
    [data-bs-theme="dark"] .ql-snow .ql-stroke {
        stroke: #adb5bd !important;
    }
    [data-bs-theme="dark"] .ql-snow .ql-fill {
        fill: #adb5bd !important;
    }
    [data-bs-theme="dark"] .ql-snow .ql-picker {
        color: #adb5bd !important;
    }
    [data-bs-theme="dark"] .ql-editor.ql-blank::before {
        color: #6c757d !important;
    }
    [data-bs-theme="dark"] .ql-snow.ql-toolbar button:hover .ql-stroke,
    [data-bs-theme="dark"] .ql-snow.ql-toolbar button.ql-active .ql-stroke {
        stroke: #3b82f6 !important;
    }
</style>
