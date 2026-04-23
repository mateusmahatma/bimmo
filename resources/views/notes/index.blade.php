@extends('layouts.main')

@section('title', __('Notes'))

@section('container')
<div class="pagetitle mb-4">
    <h1 class="fw-bold mb-1">{{ __('Notes') }}</h1>
    <nav>
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Notes') }}</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-dashboard border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header border-bottom py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0 fw-bold" style="font-size: 1.1rem; letter-spacing: -0.01em;">{{ __('Smart Reminders') }}</h5>
                        <p class="text-muted small mb-0 mt-1" style="font-size: 0.85rem;">{{ __('Manage your tasks with rich formatting and efficiency.') }}</p>
                    </div>
                </div>

                <div class="card-body pt-4">
                    <!-- Add Note Form -->
                    <div class="mb-5">
                        <div id="noteEditorWrapper" class="d-none">
                            <div id="editor-container" class="rounded-3 mb-2" style="height: 150px; border: 1px solid var(--bs-border-color);"></div>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <small class="text-muted"><kbd>Enter</kbd> {{ __('to save') }}</small>
                                <span></span>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end align-items-center mt-2">
                            <div class="d-flex gap-2">
                                <button class="btn btn-secondary btn-sm px-3 rounded-pill d-none" id="cancelEditBtn">{{ __('Cancel') }}</button>
                                <button class="btn btn-primary btn-sm px-4 btn-no-radius d-flex align-items-center gap-2" id="addNewNoteBtn" type="button">
                                    <i class="bi bi-plus-lg"></i>
                                    <span class="fw-bold" id="btnNoteText">{{ __('Add Reminder') }}</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Search -->
                    <div class="mb-4">
                        <div class="input-group">
                            <span class="input-group-text bg-transparent">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" class="form-control" id="notesSearchInput" placeholder="{{ __('Search reminders...') }}"
                                autocomplete="off">
                            <button class="btn btn-outline-secondary d-none" type="button" id="notesSearchClearBtn"
                                aria-label="{{ __('Clear search') }}">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                        <small class="text-muted d-block mt-2" id="notesSearchHint"></small>
                    </div>

                    <!-- Notes List -->
                    <div id="notesContainer" style="min-height: 200px;">
                        <!-- Active Notes -->
                        <div class="mb-5">
                            <h6 class="text-uppercase fw-bold text-primary small ls-1 mb-3 d-flex align-items-center gap-2">
                                <i class="bi bi-list-task"></i>
                                {{ __('Active Reminders') }}
                            </h6>
                            <div id="activeNotesList" class="d-flex flex-column gap-2">
                                <!-- Notes loaded via AJAX -->
                            </div>
                        </div>

                        <!-- Completed Notes -->
                        <div class="mt-4 pt-3 border-top">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="text-uppercase fw-bold text-secondary small ls-1 mb-0 d-flex align-items-center gap-2 pointer"
                                    data-bs-toggle="collapse" data-bs-target="#completedSection" aria-expanded="true">
                                    <i class="bi bi-check2-all"></i>
                                    {{ __('Completed') }}
                                    <i class="bi bi-chevron-down ms-1 transition-icon" id="collapseIcon"></i>
                                </h6>
                                <button class="btn btn-link btn-sm text-danger text-decoration-none p-0 d-none" id="clearCompletedBtn">
                                    <i class="bi bi-trash3 me-1"></i>{{ __('Empty') }}
                                </button>
                            </div>

                            <div class="collapse show" id="completedSection">
                                <div id="completedNotesList" class="d-flex flex-column gap-2 opacity-75">
                                    <!-- Completed notes loaded via AJAX -->
                                </div>
                            </div>
                        </div>

                        <div class="text-center py-5 d-none" id="notesLoading">
                            <div class="spinner-border text-primary spinner-border-sm mb-3" role="status"></div>
                            <p class="text-secondary small mb-0">{{ __('Syncing your workspace...') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Confirm Delete Modal -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content shadow border-0" style="border-radius: 16px;">
            <div class="modal-body text-center p-4">
                <div class="mb-3">
                    <i class="bi bi-exclamation-circle text-warning" style="font-size: 3rem;"></i>
                </div>
                <h5 class="fw-bold mb-2">{{ __('Are you sure?') }}</h5>
                <p class="text-muted small mb-4">{{ __('Deleted data cannot be recovered!') }}</p>
                <div class="d-flex gap-2 justify-content-center">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="button" class="btn btn-danger rounded-pill px-4 fw-bold" id="btnConfirmDelete">{{ __('Yes, Delete') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('css')
<!-- Quill.js - Corporate Rich Text -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<style>
    .pointer {
        cursor: pointer;
    }

    .note-card {
        background: var(--bs-body-bg);
        border: 1px solid var(--bs-border-color);
        border-radius: 12px;
        padding: 12px 16px;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        transition: all 0.2s ease;
    }

    .note-card:hover {
        border-color: var(--bs-primary);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .note-card.pinned {
        border-left: 4px solid #f59e0b;
        /* amber */
        box-shadow: 0 6px 20px rgba(245, 159, 11, 0.06);
    }

    .note-card.checked-card {
        background-color: var(--bs-tertiary-bg);
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

    .note-text.checked {
        text-decoration: line-through;
        opacity: 0.5;
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

    .transition-icon {
        transition: transform 0.3s ease;
    }

    .collapsed .transition-icon {
        transform: rotate(-90deg);
    }

    /* Quill Styles in Corporate Theme */
    .ql-container.ql-snow {
        border: none !important;
        font-family: inherit;
    }

    .ql-toolbar.ql-snow {
        border: none !important;
        border-bottom: 1px solid var(--bs-border-color) !important;
        background: var(--bs-light) !important;
        border-radius: 8px 8px 0 0;
    }

    .ql-editor {
        font-size: 0.95rem;
        color: var(--bs-body-color);
    }

    #editor-container {
        background: var(--bs-tertiary-bg);
    }

    /* Dark Mode Overrides */
    [data-bs-theme="dark"] .card-header {
        background-color: #212529 !important;
    }

    [data-bs-theme="dark"] .note-card {
        background-color: #1a1d21 !important;
        border-color: #343a40 !important;
    }

    [data-bs-theme="dark"] .note-card.checked-card {
        background-color: #161b22 !important;
    }

    [data-bs-theme="dark"] .note-text {
        color: #f8f9fa !important;
    }

    [data-bs-theme="dark"] .note-text * {
        color: #f8f9fa !important;
    }

    [data-bs-theme="dark"] .ql-toolbar.ql-snow {
        background-color: #1a1d21 !important;
        border-bottom-color: #343a40 !important;
    }

    [data-bs-theme="dark"] .ql-container.ql-snow {
        background-color: #111418 !important;
    }

    [data-bs-theme="dark"] .ql-editor {
        color: #f8f9fa !important;
    }

    [data-bs-theme="dark"] .ql-editor * {
        color: #f8f9fa !important;
    }

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

    .btn-no-radius {
        border-radius: 0 !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const activeNotesList = document.getElementById('activeNotesList');
        const completedNotesList = document.getElementById('completedNotesList');
        const addNoteBtn = document.getElementById('addNewNoteBtn');
        const cancelEditBtn = document.getElementById('cancelEditBtn');
        const btnNoteText = document.getElementById('btnNoteText');
        const noteEditorWrapper = document.getElementById('noteEditorWrapper');
        const notesLoading = document.getElementById('notesLoading');
        const clearCompletedBtn = document.getElementById('clearCompletedBtn');
        const notesSearchInput = document.getElementById('notesSearchInput');
        const notesSearchClearBtn = document.getElementById('notesSearchClearBtn');
        const notesSearchHint = document.getElementById('notesSearchHint');
        const collapseIcon = document.getElementById('collapseIcon');
        const completedHeader = document.querySelector('[data-bs-target="#completedSection"]');
        const confirmDeleteModal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
        const btnConfirmDelete = document.getElementById('btnConfirmDelete');
        let deleteAction = null;
        let allNotes = [];
        let searchQuery = '';
        let searchDebounceTimer = null;

        // Handle collapse icon rotation
        const completedSection = document.getElementById('completedSection');
        completedSection.addEventListener('show.bs.collapse', () => {
            completedHeader.classList.remove('collapsed');
        });
        completedSection.addEventListener('hide.bs.collapse', () => {
            completedHeader.classList.add('collapsed');
        });

        let quill = new Quill('#editor-container', {
            theme: 'snow',
            placeholder: 'Write your reminder here...',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline'],
                    [{
                        'list': 'ordered'
                    }, {
                        'list': 'bullet'
                    }],
                    ['clean']
                ],
                keyboard: {
                    bindings: {
                        handleEnter: {
                            key: 13,
                            handler: function() {
                                saveNote();
                            }
                        }
                    }
                }
            }
        });

        let editingNoteId = null;
        let isEditorOpen = false;

        addNoteBtn.addEventListener('click', () => {
            if (!isEditorOpen) {
                openEditorForNew();
                return;
            }
            saveNote();
        });
        cancelEditBtn.addEventListener('click', resetForm);
        clearCompletedBtn.addEventListener('click', clearAllCompleted);

        function openEditor() {
            if (noteEditorWrapper) noteEditorWrapper.classList.remove('d-none');
            isEditorOpen = true;
            setTimeout(() => quill.focus(), 0);
        }

        function openEditorForNew() {
            if (!isEditorOpen) {
                openEditor();
            }
            editingNoteId = null;
            quill.setContents([]);
            btnNoteText.textContent = 'Save';
            cancelEditBtn.classList.remove('d-none');
            addNoteBtn.querySelector('i').classList.replace('bi-plus-lg', 'bi-check-lg');
        }

        function normalizeQuery(value) {
            return (value || '').toString().trim().toLowerCase();
        }

        function htmlToText(html) {
            const el = document.createElement('div');
            el.innerHTML = html || '';
            return (el.textContent || el.innerText || '').trim();
        }

        function updateSearchHint(visibleCount, totalCount) {
            const q = normalizeQuery(searchQuery);
            if (!notesSearchHint) return;
            if (!q) {
                notesSearchHint.textContent = totalCount > 0 ? `${totalCount} {{ __('items') }}` : '';
                return;
            }
            notesSearchHint.textContent = `${visibleCount} / ${totalCount} {{ __('matched') }}`;
        }

        function applyFiltersAndRender() {
            const q = normalizeQuery(searchQuery);
            const filteredNotes = q ? allNotes.filter(n => htmlToText(n.content).toLowerCase().includes(q)) : allNotes;

            activeNotesList.innerHTML = '';
            completedNotesList.innerHTML = '';

            const active = filteredNotes.filter(n => !n.is_checked);
            const completed = filteredNotes.filter(n => n.is_checked);

            // Clear button visibility should follow overall completed state (not the search result)
            const hasCompleted = allNotes.some(n => n.is_checked);
            if (hasCompleted) {
                clearCompletedBtn.classList.remove('d-none');
            } else {
                clearCompletedBtn.classList.add('d-none');
            }

            renderNoteSection(activeNotesList, active, 'active');
            renderNoteSection(completedNotesList, completed, 'completed');

            updateSearchHint(filteredNotes.length, allNotes.length);
        }

        if (notesSearchInput) {
            notesSearchInput.addEventListener('input', () => {
                searchQuery = notesSearchInput.value;
                if (notesSearchClearBtn) {
                    notesSearchClearBtn.classList.toggle('d-none', !notesSearchInput.value);
                }
                if (searchDebounceTimer) clearTimeout(searchDebounceTimer);
                searchDebounceTimer = setTimeout(() => applyFiltersAndRender(), 120);
            });

            notesSearchInput.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    notesSearchInput.value = '';
                    searchQuery = '';
                    if (notesSearchClearBtn) notesSearchClearBtn.classList.add('d-none');
                    applyFiltersAndRender();
                }
            });
        }

        if (notesSearchClearBtn) {
            notesSearchClearBtn.addEventListener('click', () => {
                if (!notesSearchInput) return;
                notesSearchInput.value = '';
                searchQuery = '';
                notesSearchClearBtn.classList.add('d-none');
                notesSearchInput.focus();
                applyFiltersAndRender();
            });
        }

        btnConfirmDelete.addEventListener('click', async () => {
            if (deleteAction) {
                await deleteAction();
                confirmDeleteModal.hide();
                deleteAction = null;
            }
        });

        async function saveNote() {
            const content = quill.root.innerHTML.trim();
            if (content === '<p><br></p>' || !content) return;

            addNoteBtn.disabled = true;
            const url = editingNoteId ? `/notes/${editingNoteId}` : '/notes';
            const method = editingNoteId ? 'PUT' : 'POST';

            try {
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        content
                    })
                });
                const res = await response.json();
                if (res.success) {
                    resetForm();
                    loadNotes();
                    showToast('{{ __("Success") }}', '{{ __("Data processed successfully") }}', 'success');
                }
            } catch (e) {
                console.error(e);
            }
            addNoteBtn.disabled = false;
        }

        async function clearAllCompleted() {
            deleteAction = async () => {
                try {
                    const response = await fetch("{{ route('notes.clear-completed') }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });
                    const res = await response.json();
                    if (res.success) {
                        loadNotes();
                        showToast('{{ __("Success") }}', '{{ __("Data processed successfully") }}', 'success');
                    }
                } catch (e) {
                    console.error(e);
                }
            };
            confirmDeleteModal.show();
        }

        function resetForm() {
            quill.setContents([]);
            editingNoteId = null;
            btnNoteText.textContent = 'Add Reminder';
            cancelEditBtn.classList.add('d-none');
            addNoteBtn.querySelector('i').classList.replace('bi-check-lg', 'bi-plus-lg');
            if (noteEditorWrapper) noteEditorWrapper.classList.add('d-none');
            isEditorOpen = false;
        }

        async function loadNotes() {
            notesLoading.classList.remove('d-none');

            try {
                const response = await fetch('/notes', {
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                allNotes = await response.json();
                applyFiltersAndRender();

            } catch (e) {
                console.error(e);
            }
            notesLoading.classList.add('d-none');
        }

        function renderNoteSection(container, notes, type) {
            if (notes.length === 0) {
                container.innerHTML = `
                <div class="text-center py-4 opacity-50 border rounded-3 border-dashed" style="border-style: dashed !important;">
                    <p class="small mb-0">${type === 'active' ? 'No active reminders yet.' : 'Empty'}</p>
                </div>`;
                return;
            }

            container.innerHTML = notes.map(note => `
            <div class="note-card ${note.is_checked ? 'checked-card' : ''} ${note.is_pinned ? 'pinned' : ''}" data-id="${note.id}">
                <div class="d-flex align-items-start gap-3 flex-grow-1">
                    <div class="form-check mb-0">
                        <input class="form-check-input note-checkbox" type="checkbox" data-id="${note.id}" ${note.is_checked ? 'checked' : ''}>
                    </div>
                    <div class="note-text ${note.is_checked ? 'checked' : ''}" data-id="${note.id}">
                        ${note.content}
                    </div>
                </div>
                <div class="action-btns ms-2">
                    <button class="btn-note-action pin-note" data-id="${note.id}" data-pinned="${note.is_pinned ? '1' : '0'}" title="${note.is_pinned ? 'Unpin' : 'Pin'}">
                        <i class="${note.is_pinned ? 'bi bi-pin-fill text-warning' : 'bi bi-pin'}"></i>
                    </button>
                    <button class="btn-note-action edit-note" data-id="${note.id}" title="Edit">
                        <i class="bi bi-pencil-square"></i>
                    </button>
                    <button class="btn-note-action delete delete-note" data-id="${note.id}" title="Delete">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        `).join('');

            // Listeners for checkbox
            container.querySelectorAll('.note-checkbox').forEach(cb => {
                cb.addEventListener('change', async (e) => {
                    const id = e.target.dataset.id;
                    try {
                        await fetch(`/notes/${id}`, {
                            method: 'PUT',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                is_checked: e.target.checked
                            })
                        });
                        loadNotes();
                    } catch (e) {
                        console.error(e);
                    }
                });
            });

            // Listeners for edit
            container.querySelectorAll('.edit-note').forEach(btn => {
                btn.addEventListener('click', async () => {
                    const id = btn.dataset.id;
                    const note = allNotes.find(n => n.id == id);
                    if (note) {
                        editingNoteId = id;
                        openEditor();
                        quill.root.innerHTML = note.content;
                        btnNoteText.textContent = 'Update';
                        cancelEditBtn.classList.remove('d-none');
                        addNoteBtn.querySelector('i').classList.replace('bi-plus-lg', 'bi-check-lg');
                        window.scrollTo({
                            top: 0,
                            behavior: 'smooth'
                        });
                    }
                });
            });

            // Listeners for delete
            container.querySelectorAll('.delete-note').forEach(btn => {
                btn.addEventListener('click', () => {
                    const id = btn.dataset.id;
                    deleteAction = async () => {
                        try {
                            await fetch(`/notes/${id}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                }
                            });
                            loadNotes();
                            showToast('{{ __("Success") }}', '{{ __("Data processed successfully") }}', 'success');
                        } catch (e) {
                            console.error(e);
                        }
                    };
                    confirmDeleteModal.show();
                });
            });

            // Listeners for pin/unpin
            container.querySelectorAll('.pin-note').forEach(btn => {
                btn.addEventListener('click', async () => {
                    const id = btn.dataset.id;
                    const currentlyPinned = btn.dataset.pinned === '1';
                    try {
                        await fetch(`/notes/${id}`, {
                            method: 'PUT',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                is_pinned: !currentlyPinned
                            })
                        });
                        loadNotes();
                        showToast('{{ __("Success") }}', currentlyPinned ? '{{ __("Unpinned") }}' : '{{ __("Pinned") }}', 'success');
                    } catch (e) {
                        console.error(e);
                    }
                });
            });
        }

        // Initial load
        loadNotes();
    });
</script>
@endpush
