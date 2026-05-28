<div class="mb-5">
    <div id="noteEditorWrapper" class="d-none">
        <div id="editor-container" class="rounded-3 mb-2"></div>
        <div class="d-flex justify-content-between align-items-center mt-2">
            <small class="text-muted"><kbd>Enter</kbd> {{ __('to save') }}</small>
            <span></span>
        </div>
    </div>
    <div class="d-flex justify-content-end align-items-center mt-2">
        <div class="d-flex gap-2">
            <button class="btn btn-secondary btn-sm px-3 d-none" id="cancelEditBtn" type="button">
                <i class="bi bi-x-circle me-1"></i>{{ __('Cancel') }}
            </button>
            <button class="btn btn-primary btn-sm px-4 d-flex align-items-center gap-2" id="addNewNoteBtn"
                type="button">
                <i class="bi bi-plus-lg"></i>
                <span class="fw-bold" id="btnNoteText">{{ __('Add Data') }}</span>
            </button>
        </div>
    </div>
</div>
