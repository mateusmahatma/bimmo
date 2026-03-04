<div class="card card-dashboard border-0 shadow-sm" style="border-radius: 12px;">
    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h5 class="card-title mb-0 fw-bold text-dark" style="font-size: 1.1rem; letter-spacing: -0.01em;">
                Activity Calendar
            </h5>
            <p class="text-muted small mb-0 mt-1" style="font-size: 0.85rem;">Manage your schedules and financial deadlines.</p>
        </div>
        <div class="d-flex gap-2 align-items-center">
            <div class="input-group input-group-sm rounded-pill overflow-hidden border shadow-sm" style="width: 200px;">
                <span class="input-group-text bg-white border-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" id="calendarSearch" class="form-control border-0" placeholder="Search events...">
            </div>
            <button class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm" id="btnNewEvent">
                <i class="bi bi-plus-lg me-1"></i> New Event
            </button>
        </div>
    </div>
    <div class="card-body p-3">
        <div class="row mb-3 g-2">
            <div class="col-12 d-flex flex-wrap gap-2">
                <div class="form-check form-check-inline">
                    <input class="form-check-input filter-category" type="checkbox" id="cat-reminder" value="reminder" checked>
                    <label class="form-check-label small" for="cat-reminder"><span class="badge bg-info p-1 me-1"></span>Reminder</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input filter-category" type="checkbox" id="cat-task" value="task" checked>
                    <label class="form-check-label small" for="cat-task"><span class="badge bg-primary p-1 me-1"></span>Task</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input filter-category" type="checkbox" id="cat-meeting" value="meeting" checked>
                    <label class="form-check-label small" for="cat-meeting"><span class="badge bg-warning p-1 me-1"></span>Meeting</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input filter-category" type="checkbox" id="cat-deadline" value="deadline" checked>
                    <label class="form-check-label small" for="cat-deadline"><span class="badge bg-danger p-1 me-1"></span>Deadline</label>
                </div>
            </div>
        </div>
        <div id="calendar" style="min-height: 500px;"></div>
    </div>
</div>

<!-- Add Event Modal -->
<div class="modal fade" id="addEventModal" tabindex="-1" aria-labelledby="addEventModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <form id="eventForm">
                <input type="hidden" name="id" id="eventId">
                <div class="modal-header border-bottom py-3">
                    <h5 class="modal-title fw-bold" id="addEventModalLabel">Add New Event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Basic Information Section -->
                    <div class="mb-4">
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

                    <hr class="text-muted opacity-25 my-4">

                    <!-- Schedule Section -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-0" style="letter-spacing: 0.05em;">Schedule</label>
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" name="all_day" id="allDaySwitch">
                                <label class="form-check-label small fw-bold" for="allDaySwitch">All Day</label>
                            </div>
                        </div>

                        <div id="timeInputsContainer" class="row g-3">
                            <div class="col-md-6 mt-0">
                                <label class="form-label small fw-bold">Starts At</label>
                                <input type="datetime-local" name="start_at" class="form-control rounded-3 shadow-sm border-light">
                            </div>
                            <div class="col-md-6 mt-0">
                                <label class="form-label small fw-bold">Ends At (Optional)</label>
                                <input type="datetime-local" name="end_at" class="form-control rounded-3 shadow-sm border-light">
                            </div>
                        </div>

                        <div id="dateInputsContainer" class="row d-none g-3">
                            <div class="col-md-6 mt-0">
                                <label class="form-label small fw-bold">Start Date</label>
                                <input type="date" name="start_date" class="form-control rounded-3 shadow-sm border-light">
                            </div>
                            <div class="col-md-6 mt-0">
                                <label class="form-label small fw-bold">End Date (Optional)</label>
                                <input type="date" name="end_date" class="form-control rounded-3 shadow-sm border-light">
                            </div>
                        </div>
                    </div>

                    <hr class="text-muted opacity-25 my-4">

                    <!-- Settings Section -->
                    <div class="mb-0">
                        <label class="form-label small fw-bold text-muted text-uppercase mb-3" style="letter-spacing: 0.05em;">Notifications & Details</label>
                        
                        <div class="row align-items-center mb-3">
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="send_email" id="sendEmailSwitch" checked>
                                    <label class="form-check-label small fw-bold" for="sendEmailSwitch">Email Reminder</label>
                                </div>
                            </div>
                            <div class="col-md-6 d-none" id="emailInputContainer">
                                <input type="email" name="notification_email" id="notificationEmail" class="form-control form-control-sm rounded-3 shadow-sm border-light" value="{{ auth()->user()->email }}" placeholder="Notification target">
                            </div>
                        </div>

                        <div class="mb-0">
                            <label class="form-label small fw-bold">Description</label>
                            <textarea name="description" class="form-control rounded-3 shadow-sm border-light" rows="2" placeholder="Add some notes..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top p-3">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Save Event</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Event Detail Pop-up (Tooltip replacement for more detail) -->
<div id="eventPopover" class="popover shadow-lg border-0" style="display: none; position: absolute; z-index: 1060; max-width: 250px; border-radius: 12px; background: white;">
    <div class="popover-header fw-bold border-bottom p-2 d-flex justify-content-between align-items-center bg-light" style="border-radius: 12px 12px 0 0;">
        <span id="popoverTitle"></span>
        <button type="button" class="btn-close small" id="btnClosePopover" style="font-size: 0.6rem;"></button>
    </div>
    <div class="popover-body p-3">
        <div class="small mb-2 text-muted" id="popoverTime"></div>
        <div class="small mb-3" id="popoverDesc"></div>
        <div class="d-flex justify-content-between">
            <button class="btn btn-sm btn-outline-danger border-0" id="btnDeleteEvent"><i class="bi bi-trash"></i></button>
            <button class="btn btn-sm btn-primary rounded-pill px-3" id="btnEditEvent">Edit</button>
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
    .badge-info { background-color: #0dcaf0; }
    .badge-primary { background-color: #0d6efd; }
    .badge-warning { background-color: #ffc107; }
    .badge-danger { background-color: #dc3545; }
    
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
</style>
