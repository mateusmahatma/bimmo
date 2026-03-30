@php
    $uiStyle = auth()->user()->ui_style ?? 'corporate';
@endphp
<div class="card card-dashboard border-0 shadow-sm {{ $uiStyle === 'milenial' ? 'm-glass-container' : '' }}" style="border-radius: {{ $uiStyle === 'milenial' ? 'var(--m-radius-lg)' : '12px' }};">
    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-3 {{ $uiStyle === 'milenial' ? 'm-card-header-vibrant bg-transparent' : '' }}">
        <div class="header-title-container">
            <h5 class="card-title mb-0 fw-bold text-dark {{ $uiStyle === 'milenial' ? 'm-card-title-vibrant' : '' }}" style="font-size: 1.1rem; letter-spacing: -0.01em;">
                Activity Calendar
            </h5>
            <p class="text-muted small mb-0 mt-1 d-none d-sm-block" style="font-size: 0.85rem;">Manage your schedules and financial deadlines.</p>
        </div>
        <div class="d-flex gap-2 align-items-center flex-grow-1 flex-sm-grow-0 justify-content-end header-actions-container">
            <div class="input-group input-group-sm rounded-pill overflow-hidden border shadow-sm search-input-group">
                <span class="input-group-text bg-white border-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" id="calendarSearch" class="form-control border-0" placeholder="Search...">
            </div>
            <button class="btn {{ $uiStyle === 'milenial' ? 'btn-primary bg-gradient shadow-sm' : 'btn-primary' }} btn-sm rounded-pill px-3 shadow-sm flex-shrink-0" id="btnNewEvent">
                <i class="bi bi-plus-lg me-1"></i> <span class="d-none d-md-inline">New Event</span><span class="d-md-none">New</span>
            </button>
        </div>
    </div>
    <div class="card-body p-2 p-md-4">
        <div class="row mb-3 g-2">
            <div class="col-12 d-flex flex-wrap gap-2 justify-content-start overflow-auto pb-1 calendar-filters" style="scrollbar-width: none; -ms-overflow-style: none;">
                <style>.calendar-filters::-webkit-scrollbar { display: none; }</style>
                <div class="form-check form-check-inline me-0">
                    <input class="form-check-input filter-category" type="checkbox" id="cat-reminder" value="reminder" checked>
                    <label class="form-check-label small" for="cat-reminder"><span class="badge {{ $uiStyle === 'milenial' ? 'm-badge-modern' : '' }} bg-info p-1 me-1"></span>Reminder</label>
                </div>
                <div class="form-check form-check-inline me-0">
                    <input class="form-check-input filter-category" type="checkbox" id="cat-task" value="task" checked>
                    <label class="form-check-label small" for="cat-task"><span class="badge {{ $uiStyle === 'milenial' ? 'm-badge-modern' : '' }} bg-primary p-1 me-1"></span>Task</label>
                </div>
                <div class="form-check form-check-inline me-0">
                    <input class="form-check-input filter-category" type="checkbox" id="cat-meeting" value="meeting" checked>
                    <label class="form-check-label small" for="cat-meeting"><span class="badge {{ $uiStyle === 'milenial' ? 'm-badge-modern' : '' }} bg-warning p-1 me-1"></span>Meeting</label>
                </div>
                <div class="form-check form-check-inline me-0">
                    <input class="form-check-input filter-category" type="checkbox" id="cat-deadline" value="deadline" checked>
                    <label class="form-check-label small" for="cat-deadline"><span class="badge {{ $uiStyle === 'milenial' ? 'm-badge-modern' : '' }} bg-danger p-1 me-1"></span>Deadline</label>
                </div>
            </div>
        </div>
        <div id="calendar" style="min-height: 400px; font-family: {{ $uiStyle === 'milenial' ? "'Inter', sans-serif" : 'inherit' }};"></div>
    </div>
</div>

<!-- Add Event Modal -->
<div class="modal fade" id="addEventModal" tabindex="-1" aria-labelledby="addEventModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: {{ $uiStyle === 'milenial' ? '24px' : '15px' }}; overflow: hidden;">
            <form id="eventForm">
                <input type="hidden" name="id" id="eventId">
                <div class="modal-header border-bottom py-3 {{ $uiStyle === 'milenial' ? 'bg-light bg-opacity-50' : '' }}">
                    <h5 class="modal-title fw-bold" id="addEventModalLabel">Add New Event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3 p-md-4">
                    <!-- Basic Information Section -->
                    <div class="mb-3 mb-md-4">
                        <label class="form-label small fw-bold text-muted text-uppercase mb-2" style="letter-spacing: 0.05em;">Basic Information</label>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Event Title</label>
                            <input type="text" name="title" class="form-control rounded-3 shadow-sm border-light" required placeholder="What are you planning?">
                        </div>
                        <div class="mb-0">
                            <label class="form-label small fw-bold">Category</label>
                            <select name="category" class="form-select rounded-3 shadow-sm border-light" required>
                                <option value="reminder">Reminder</option>
                                <option value="task">Task</option>
                                <option value="meeting">Meeting</option>
                                <option value="deadline">Deadline</option>
                            </select>
                        </div>
                    </div>

                    <hr class="text-muted opacity-25 my-3 my-md-4">

                    <!-- Schedule Section -->
                    <div class="mb-3 mb-md-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-0" style="letter-spacing: 0.05em;">Schedule</label>
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" name="all_day" id="allDaySwitch">
                                <label class="form-check-label small fw-bold" for="allDaySwitch">All Day</label>
                            </div>
                        </div>

                        <div id="timeInputsContainer" class="row g-2 g-md-3">
                            <div class="col-6 col-md-6 mt-0">
                                <label class="form-label small fw-bold">Starts At</label>
                                <input type="datetime-local" name="start_at" class="form-control form-control-sm rounded-3 shadow-sm border-light">
                            </div>
                            <div class="col-6 col-md-6 mt-0">
                                <label class="form-label small fw-bold">Ends At</label>
                                <input type="datetime-local" name="end_at" class="form-control form-control-sm rounded-3 shadow-sm border-light">
                            </div>
                        </div>

                        <div id="dateInputsContainer" class="row d-none g-2 g-md-3">
                            <div class="col-6 col-md-6 mt-0">
                                <label class="form-label small fw-bold">Start Date</label>
                                <input type="date" name="start_date" class="form-control form-control-sm rounded-3 shadow-sm border-light">
                            </div>
                            <div class="col-6 col-md-6 mt-0">
                                <label class="form-label small fw-bold">End Date</label>
                                <input type="date" name="end_date" class="form-control form-control-sm rounded-3 shadow-sm border-light">
                            </div>
                        </div>
                    </div>

                    <hr class="text-muted opacity-25 my-3 my-md-4">

                    <!-- Recurrence Section -->
                    <div class="mb-3 mb-md-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-0" style="letter-spacing: 0.05em;">Recurrence</label>
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" name="is_recurring" id="recurringSwitch">
                                <label class="form-check-label small fw-bold" for="recurringSwitch">Repeat</label>
                            </div>
                        </div>

                        <div id="recurringInputs" class="row d-none g-2 g-md-3">
                            <div class="col-6 col-md-6 mt-0">
                                <label class="form-label small fw-bold">Frequency</label>
                                <select name="rrule_freq" id="rruleFreq" class="form-select form-select-sm rounded-3 shadow-sm border-light">
                                    <option value="DAILY">Daily</option>
                                    <option value="WEEKLY">Weekly</option>
                                    <option value="MONTHLY">Monthly</option>
                                </select>
                            </div>
                            <div class="col-6 col-md-6 mt-0">
                                <label class="form-label small fw-bold">Ends On</label>
                                <input type="date" name="rrule_until" id="rruleUntil" class="form-control form-control-sm rounded-3 shadow-sm border-light">
                            </div>
                        </div>
                    </div>

                    <hr class="text-muted opacity-25 my-3 my-md-4">

                    <!-- Settings Section -->
                    <div class="mb-0">
                        <label class="form-label small fw-bold text-muted text-uppercase mb-3" style="letter-spacing: 0.05em;">Notifications</label>
                        
                        <div class="row align-items-center mb-3">
                            <div class="col-12 col-md-6 mb-2 mb-md-0">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="send_email" id="sendEmailSwitch" checked>
                                    <label class="form-check-label small fw-bold" for="sendEmailSwitch">Email Reminder</label>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 d-none" id="emailInputContainer">
                                <input type="email" name="notification_email" id="notificationEmail" class="form-control form-control-sm rounded-3 shadow-sm border-light" value="{{ auth()->user()->email }}" placeholder="Target email">
                            </div>
                        </div>

                        <div class="mb-0">
                            <label class="form-label small fw-bold">Description</label>
                            <textarea name="description" class="form-control rounded-3 shadow-sm border-light" rows="2" placeholder="Add some notes..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top p-3 {{ $uiStyle === 'milenial' ? 'bg-light bg-opacity-50' : '' }}">
                    <button type="button" class="btn btn-light rounded-pill px-3 px-md-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-3 px-md-4">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Event Detail Pop-up -->
<div id="eventPopover" class="popover shadow-lg border-0" style="display: none; position: absolute; z-index: 1060; max-width: 250px; border-radius: 12px; background: white;">
    <div class="popover-header fw-bold border-bottom p-2 d-flex justify-content-between align-items-center bg-light" style="border-radius: 12px 12px 0 0;">
        <span id="popoverTitle"></span>
        <button type="button" class="btn-close small" id="btnClosePopover" style="font-size: 0.6rem;"></button>
    </div>
    <div class="popover-body p-3">
        <div class="small mb-2 text-muted" id="popoverTime"></div>
        <div class="small mb-3" id="popoverDesc"></div>
        <div class="d-flex justify-content-between gap-2">
            <button class="btn btn-sm btn-outline-danger border-0 p-1" id="btnDeleteEvent"><i class="bi bi-trash"></i></button>
            <button class="btn btn-sm btn-primary rounded-pill px-3 flex-grow-1" id="btnEditEvent">Edit</button>
        </div>
    </div>
</div>

<style>
    .fc-event {
        cursor: pointer;
        padding: 2px 4px;
        border-radius: 4px;
        border: none;
    }
    .fc-event:hover {
        opacity: 0.8;
    }
    .fc-theme-bootstrap5 .fc-h-event {
        background-color: var(--fc-event-bg-color, #3788d8);
    }
    
    /* Responsive Header Adjustments */
    @media (max-width: 576px) {
        .search-input-group {
            width: 100% !important;
            order: 2;
        }
        .header-title-container {
            width: auto;
            order: 1;
        }
        .header-actions-container {
            width: 100%;
            justify-content: flex-end !important;
            flex-wrap: wrap;
        }
        #btnNewEvent {
            order: 1;
        }
        .search-input-group {
            order: 2;
        }
        .fc-header-toolbar {
            flex-direction: column;
            gap: 10px;
        }
        .fc-toolbar-chunk {
            display: flex;
            justify-content: center;
            width: 100%;
        }
        .fc-toolbar-title {
            font-size: 1.1rem !important;
        }
    }

    /* FullCalendar Custom Mobile Fixes */
    .fc .fc-toolbar {
        flex-wrap: wrap;
    }
    .fc .fc-button {
        padding: 0.3em 0.5em !important;
        font-size: 0.85rem !important;
    }
    
    /* Dark Mode Compatibility */
    body.dark-mode .popover {
        background: #2b3035 !important;
        color: white !important;
    }
    body.dark-mode .popover-header {
        background: #343a40 !important;
        color: white !important;
        border-color: #495057 !important;
    }
    
    /* PWA Mode Adjustments */
    .pwa-mode .card-dashboard {
        border-radius: 0 !important;
        box-shadow: none !important;
    }
</style>

