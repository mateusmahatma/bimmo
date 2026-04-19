function initCalendar() {
    const calendarEl = document.getElementById('calendar');
    if (!calendarEl) {
        console.warn('Calendar element not found');
        return;
    }

    // Prevent double initialization on the same element
    if (calendarEl.dataset.initialized) return;
    calendarEl.dataset.initialized = 'true';

    // Use dynamic URL provided from Blade, fallback to '/events'
    const eventsBaseUrl = window.eventsUrl || '/events';

    const isMobile = window.innerWidth < 768;
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: isMobile ? 'listMonth' : 'dayGridMonth',
        firstDay: 1, // Start week on Monday
        headerToolbar: {
            left: isMobile ? 'prev,next' : 'prev,next today',
            center: 'title',
            right: isMobile ? 'dayGridMonth,listMonth' : 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
        },
        themeSystem: 'bootstrap5',
        editable: true,
        droppable: true,
        selectable: true,
        height: 'auto',
        events: eventsBaseUrl,

        eventClick: function (info) {
            // Center modal detail when agenda clicked
            info.jsEvent?.preventDefault?.();
            openEventDetailModal(info.event);
        },

        select: function (info) {
            openEventModal({
                start: info.startStr,
                end: info.endStr,
                allDay: false
            });
        },

        eventDrop: function (info) {
            updateEventAjax(info.event);
        },

        eventResize: function (info) {
            updateEventAjax(info.event);
        },

        eventDidMount: function (info) {
            // Backend now provides colors, but we can add subtle styles if needed
            info.el.style.borderRadius = '5px';
            info.el.classList.add('shadow-sm');

            // Completed marker: small green pill with white check (per-event or per-occurrence for recurring)
            const target = info.el.querySelector('.fc-event-title') || info.el.querySelector('.fc-event-main') || info.el;
            const existing = info.el.querySelector('.event-done-icon');
            const completed = isEventCompleted(info.event);

            if (!completed) {
                info.el.classList.remove('event-completed');
                if (existing) existing.remove();
                return;
            }

            info.el.classList.add('event-completed');
            if (target && !existing) {
                const badge = document.createElement('span');
                badge.className = 'event-done-icon d-inline-flex align-items-center justify-content-center bg-success text-white rounded-circle';
                badge.style.width = '14px';
                badge.style.height = '14px';
                badge.style.marginLeft = '6px';
                badge.style.verticalAlign = 'middle';
                badge.innerHTML = '<i class="bi bi-check" style="font-size: .75rem; line-height: 1;"></i>';
                target.appendChild(badge);
            }
        }
    });

    setTimeout(() => {
        calendar.render();
    }, 10);

    // (Agenda list removed)

    window.addEventListener('resize', function () {
        if (calendar && calendar.view) {
            if (window.innerWidth < 768 && calendar.view.type !== 'listMonth' && calendar.view.type !== 'dayGridMonth') {
                calendar.changeView('listMonth');
            }
        }
    });

    // New Event Button
    const btnNewEvent = document.getElementById('btnNewEvent');
    if (btnNewEvent) {
        btnNewEvent.addEventListener('click', function () {
            openEventModal();
        });
    }

    const modalEl = document.getElementById('addEventModal');
    if (!modalEl) return;

    const bootstrapModal = bootstrap.Modal.getOrCreateInstance(modalEl);
    const form = document.getElementById('eventForm');
    const allDaySwitch = document.getElementById('allDaySwitch');
    const timeInputs = document.getElementById('timeInputsContainer');
    const dateInputs = document.getElementById('dateInputsContainer');

    if (allDaySwitch && timeInputs && dateInputs) {
        allDaySwitch.addEventListener('change', function () {
            if (this.checked) {
                timeInputs.classList.add('d-none');
                dateInputs.classList.remove('d-none');
                const startVal = timeInputs.querySelector('[name="start_at"]')?.value;
                if (startVal) dateInputs.querySelector('[name="start_date"]').value = startVal.split('T')[0];
            } else {
                timeInputs.classList.remove('d-none');
                dateInputs.classList.add('d-none');
            }
        });
    }

    // Helper to get local ISO string without 'Z' for datetime-local
    function toLocalISO(date) {
        if (!date) return null;
        const tzOffset = date.getTimezoneOffset() * 60000; // offset in milliseconds
        const localISOTime = (new Date(date - tzOffset)).toISOString().slice(0, 16);
        return localISOTime;
    }

    const emailSwitch = document.getElementById('sendEmailSwitch');
    const emailContainer = document.getElementById('emailInputContainer');
    const emailInput = document.getElementById('notificationEmail');
    const userEmail = emailInput ? emailInput.value : '';

    if (emailSwitch && emailContainer) {
        emailSwitch.addEventListener('change', function () {
            if (this.checked) {
                emailContainer.classList.remove('d-none');
            } else {
                emailContainer.classList.add('d-none');
            }
        });
    }

    const recurringSwitch = document.getElementById('recurringSwitch');
    const recurringInputs = document.getElementById('recurringInputs');
    const rruleFreq = document.getElementById('rruleFreq');
    const rruleUntil = document.getElementById('rruleUntil');

    console.log('Calendar UI Elements:', {
        recurringSwitch: !!recurringSwitch,
        recurringInputs: !!recurringInputs
    });

    if (recurringSwitch) {
        recurringSwitch.addEventListener('change', function () {
            console.log('Recurring Switch Changed:', this.checked);
            if (this.checked) {
                recurringInputs.classList.remove('d-none');
                console.log('Class d-none removed from recurringInputs');
            } else {
                recurringInputs.classList.add('d-none');
                console.log('Class d-none added to recurringInputs');
            }
        });

        // Trigger once on load to ensure sync if browser autocomplete is on
        if (recurringSwitch.checked) {
            recurringInputs.classList.remove('d-none');
        } else {
            recurringInputs.classList.add('d-none');
        }
    }

    function openEventModal(data = null) {
        form.reset();
        document.getElementById('eventId').value = '';
        document.getElementById('addEventModalLabel').innerText = 'Add New Event';

        // Reset default UI state
        allDaySwitch.checked = false;
        timeInputs.classList.remove('d-none');
        dateInputs.classList.add('d-none');
        emailSwitch.checked = true;
        emailContainer.classList.remove('d-none');
        emailInput.value = userEmail;
        if (recurringSwitch) {
            recurringSwitch.checked = false;
            if (recurringInputs) recurringInputs.classList.add('d-none');
            if (rruleFreq) rruleFreq.value = 'DAILY';
            if (rruleUntil) rruleUntil.value = '';
        }

        if (data) {
            if (data.id) {
                document.getElementById('eventId').value = data.id;
                document.getElementById('addEventModalLabel').innerText = 'Edit Event';
                form.querySelector('[name="title"]').value = data.title;
                form.querySelector('[name="category"]').value = data.category;
                form.querySelector('[name="description"]').value = data.description || '';

                // Email Notification Fields
                const extended = data.extendedProps || {};
                emailSwitch.checked = extended.send_email !== false && extended.send_email !== 0;
                emailInput.value = extended.notification_email || userEmail;

                if (emailSwitch.checked) {
                    emailContainer.classList.remove('d-none');
                } else {
                    emailContainer.classList.add('d-none');
                }

                // Recurrence
                if (data.rrule) {
                    recurringSwitch.checked = true;
                    if (recurringInputs) recurringInputs.classList.remove('d-none');

                    // Extract RRULE part if it contains DTSTART
                    let rulePart = data.rrule;
                    if (rulePart.includes('RRULE:')) {
                        rulePart = rulePart.split('RRULE:')[1];
                    }

                    const parts = rulePart.split(';');
                    parts.forEach(p => {
                        const [key, val] = p.split('=');
                        if (key === 'FREQ' && rruleFreq) rruleFreq.value = val;
                        if (key === 'UNTIL' && rruleUntil) {
                            const ymd = val.split('T')[0];
                            rruleUntil.value = ymd.replace(/(\d{4})(\d{2})(\d{2})/, '$1-$2-$3');
                        }
                    });
                }
            }

            const isAllDay = data.allDay === true || data.allDay === 1;
            allDaySwitch.checked = isAllDay;

            // Date processing
            let startVal = data.start;
            let endVal = data.end;

            // If it's a Date object (from FullCalendar), convert to local string
            if (startVal instanceof Date) startVal = toLocalISO(startVal);
            if (endVal instanceof Date) endVal = toLocalISO(endVal);

            // Helper to format string for datetime-local (YYYY-MM-DDTHH:mm)
            const formatForDateTimeLocal = (str) => {
                if (!str) return '';
                // If it's just YYYY-MM-DD, add default time
                if (str.length === 10) return `${str}T09:00`;
                // Otherwise take first 16 chars (YYYY-MM-DDTHH:mm)
                return str.slice(0, 16);
            };

            // Helper to format string for date (YYYY-MM-DD)
            const formatForDate = (str) => {
                if (!str) return '';
                return str.split('T')[0];
            };

            if (isAllDay) {
                timeInputs.classList.add('d-none');
                dateInputs.classList.remove('d-none');
                if (startVal) dateInputs.querySelector('[name="start_date"]').value = formatForDate(startVal);
                if (endVal) dateInputs.querySelector('[name="end_date"]').value = formatForDate(endVal);
            } else {
                timeInputs.classList.remove('d-none');
                dateInputs.classList.add('d-none');
                if (startVal) timeInputs.querySelector('[name="start_at"]').value = formatForDateTimeLocal(startVal);
                if (endVal) timeInputs.querySelector('[name="end_at"]').value = formatForDateTimeLocal(endVal);
            }
        }
        bootstrapModal.show();
    }

    // Handle Form Submission (Store or Update)
    if (form) form.addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        const eventId = document.getElementById('eventId').value;
        const isAllDay = !!allDaySwitch?.checked;

        const startVal = isAllDay ? formData.get('start_date') : formData.get('start_at');
        const endVal = isAllDay ? formData.get('end_date') : formData.get('end_at');

        if (!startVal) {
            alert('Silakan tentukan tanggal/jam mulai.');
            return;
        }

        let rruleStr = null;
        if (recurringSwitch?.checked) {
            // Include DTSTART in the string for maximum compatibility with RRule plugin
            const dtStart = startVal.replace(/[-:]/g, '');
            // If it's all day, we just need the date part
            const dtStartFormatted = isAllDay ? dtStart.split('T')[0] : dtStart.replace('T', 'T') + '00Z';

            rruleStr = `DTSTART:${dtStartFormatted}\nRRULE:FREQ=${rruleFreq.value}`;
            if (rruleUntil.value) {
                const untilClean = rruleUntil.value.replace(/-/g, '');
                rruleStr += `;UNTIL=${untilClean}T235959Z`;
            }
        }

        const data = {
            title: formData.get('title'),
            category: formData.get('category'),
            description: formData.get('description') || null,
            all_day: isAllDay ? 1 : 0,
            start_at: startVal,
            end_at: endVal || null,
            send_email: emailSwitch?.checked ? 1 : 0,
            notification_email: emailInput?.value || null,
            rrule: rruleStr
        };

        // Use POST with _method spoofing for maximum compatibility (PUT often fails in some setups)
        const url = eventId ? `${eventsBaseUrl}/${eventId}` : eventsBaseUrl;

        const payload = { ...data };
        if (eventId) {
            payload._method = 'PUT';
        }

        fetch(url, {
            method: 'POST',
            body: JSON.stringify(payload),
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
            .then(async resp => {
                const result = await resp.json().catch(() => ({}));
                if (!resp.ok) {
                    const errorDetail = result.message || result.error || resp.statusText || 'Unknown Server Error';
                    throw new Error(`${errorDetail} (Status: ${resp.status})`);
                }
                return result;
            })
            .then(() => {
                calendar.refetchEvents();
                bootstrapModal.hide();
                hidePopover();
            })
            .catch(err => {
                console.error('Event saving error:', err);
                alert('Save failed: ' + err.message);
            });
    });

    // Update Event (Drag/Resize)
    function updateEventAjax(event) {
        const data = {
            _method: 'PUT',
            start_at: toLocalISO(event.start),
            end_at: event.end ? toLocalISO(event.end) : null,
            all_day: event.allDay ? 1 : 0
        };

        fetch(`${eventsBaseUrl}/${event.id}`, {
            method: 'POST',
            body: JSON.stringify(data),
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
            .then(resp => resp.json())
            .catch(err => {
                console.error(err);
                calendar.refetchEvents();
            });
    }

    // Detail Popover functions
    window.showPopover = function (event, el) {
        const popover = document.getElementById('eventPopover');
        document.getElementById('popoverTitle').innerText = event.title;
        document.getElementById('popoverTime').innerText = formatEventTime(event);
        document.getElementById('popoverDesc').innerText = event.extendedProps.description || 'No description';

        const rect = el.getBoundingClientRect();
        popover.style.display = 'block';
        popover.style.top = (window.scrollY + rect.top - popover.offsetHeight - 5) + 'px';
        popover.style.left = (window.scrollX + rect.left + (rect.width / 2) - (popover.offsetWidth / 2)) + 'px';

        document.getElementById('btnDeleteEvent').onclick = function () {
            if (confirm('Delete this event?')) {
                fetch(`${eventsBaseUrl}/${event.id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                    .then(() => {
                        event.remove();
                        hidePopover();
                    });
            }
        };

        document.getElementById('btnEditEvent').onclick = function () {
            openEventModal({
                id: event.id,
                title: event.title,
                start: event.start, // Send Date object directly
                end: event.end, // Send Date object directly
                allDay: event.allDay,
                category: event.extendedProps.category,
                description: event.extendedProps.description,
                rrule: event.extendedProps.rrule
            });
            hidePopover();
        };
    };

    window.hidePopover = function () {
        document.getElementById('eventPopover').style.display = 'none';
    };

    document.getElementById('btnClosePopover').addEventListener('click', hidePopover);

    function formatEventTime(event) {
        const options = { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' };
        let timeStr = event.start.toLocaleString(undefined, options);
        if (event.end) timeStr += ' - ' + event.end.toLocaleString(undefined, options);
        return timeStr;
    }

    // Hide popover when clicking outside
    if (!window.calendarPopoverListenerAdded) {
        document.addEventListener('click', function (e) {
            const popover = document.getElementById('eventPopover');
            if (popover && popover.style.display === 'block' && !popover.contains(e.target) && !e.target.closest('.fc-event')) {
                hidePopover();
            }
        });
        window.calendarPopoverListenerAdded = true;
    }

    // Event detail modal (dashboard agenda click)
    const detailModalEl = document.getElementById('eventDetailModal');
    const detailModal = detailModalEl ? bootstrap.Modal.getOrCreateInstance(detailModalEl) : null;
    let currentDetailEventId = null;

    function categoryMeta(category) {
        switch ((category || '').toLowerCase()) {
            case 'reminder': return { label: 'Reminder', badge: 'bg-info' };
            case 'task': return { label: 'Task', badge: 'bg-primary' };
            case 'meeting': return { label: 'Meeting', badge: 'bg-warning text-dark' };
            case 'deadline': return { label: 'Deadline', badge: 'bg-danger' };
            default: return { label: category || '', badge: 'bg-secondary' };
        }
    }

    function getOccurrenceKey(event) {
        if (!event) return null;
        if (event.allDay) {
            // FullCalendar gives stable YYYY-MM-DD for all-day occurrences
            const ymd = event.startStr || (event.start ? event.start.toISOString().slice(0, 10) : null);
            return ymd ? `DATE:${ymd}` : null;
        }
        const iso = event.start ? event.start.toISOString() : null;
        return iso ? `TIME:${iso}` : null;
    }

    function isEventCompleted(event) {
        if (!event) return false;

        const list = event.extendedProps?.completed_occurrences || [];

        // Prefer per-occurrence completion when the backend provides it (works for recurring instances too)
        if (Array.isArray(list) && list.length > 0) {
            const key = getOccurrenceKey(event);
            if (key && list.includes(key)) return true;

            // Backward-compatible/fuzzy match for older stored values (iso strings)
            const eventMs = event.start ? event.start.getTime() : NaN;
            if (!Number.isFinite(eventMs)) return false;

            return list.some(s => {
                if (typeof s === 'string' && s.startsWith('DATE:') && event.allDay) {
                    return s === key;
                }
                if (typeof s === 'string' && (s.startsWith('TIME:'))) {
                    const ms = new Date(s.slice(5)).getTime();
                    return Number.isFinite(ms) && Math.abs(ms - eventMs) < 5 * 60000;
                }
                const ms = new Date(s).getTime();
                return Number.isFinite(ms) && Math.abs(ms - eventMs) < 5 * 60000; // tolerate TZ/seconds drift
            });
        }

        // Fallback: single status flag (non-recurring)
        return event.extendedProps?.status === 'completed';
    }

    function openEventDetailModal(event) {
        if (!detailModal) return;

        currentDetailEventId = event.id;
        const titleEl = document.getElementById('eventDetailModalLabel');
        const timeEl = document.getElementById('detailTime');
        const descEl = document.getElementById('detailDesc');
        const badgeEl = document.getElementById('detailCategoryBadge');

        if (titleEl) titleEl.innerText = event.title || 'Agenda';
        if (timeEl) timeEl.innerText = formatEventTime(event);
        if (descEl) descEl.innerText = event.extendedProps?.description || 'No description';

        const meta = categoryMeta(event.extendedProps?.category);
        if (badgeEl) {
            if (meta.label) {
                badgeEl.style.display = '';
                badgeEl.className = `badge rounded-pill ${meta.badge}`;
                badgeEl.innerText = meta.label;
            } else {
                badgeEl.style.display = 'none';
            }
        }

        const btnDone = document.getElementById('btnDetailDone');
        const isCompleted = isEventCompleted(event);
        if (btnDone) {
            btnDone.disabled = !!isCompleted;
            btnDone.innerHTML = isCompleted
                ? '<i class="bi bi-check-circle-fill me-1"></i>Sudah Done'
                : '<i class="bi bi-check2-circle me-1"></i>Done';
            btnDone.onclick = function () {
                if (isCompleted) return;

                const isRecurring = Array.isArray(event.extendedProps?.completed_occurrences) || !!event.extendedProps?.rrule;
                const occurrenceKey = isRecurring ? getOccurrenceKey(event) : null;
                fetch(`${eventsBaseUrl}/${event.id}`, {
                    method: 'POST',
                    body: JSON.stringify({
                        _method: 'PUT',
                        status: 'completed',
                        ...(occurrenceKey ? { occurrence_key: occurrenceKey } : {})
                    }),
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                }).then(resp => resp.json())
                    .then(() => {
                        if (isRecurring) {
                            const current = Array.isArray(event.extendedProps?.completed_occurrences)
                                ? [...event.extendedProps.completed_occurrences]
                                : [];
                            if (occurrenceKey) current.push(occurrenceKey);
                            event.setExtendedProp('completed_occurrences', current);
                        } else {
                            event.setExtendedProp('status', 'completed');
                        }
                        if (calendar) {
                            if (typeof calendar.rerenderEvents === 'function') {
                                calendar.rerenderEvents();
                            }
                            // Ensure UI updates even for recurring instances (rebuild from server)
                            if (typeof calendar.refetchEvents === 'function') {
                                calendar.refetchEvents();
                            }
                        }
                        detailModal.hide();
                    })
                    .catch(err => console.error(err));
            };
        }

        const btnEdit = document.getElementById('btnDetailEdit');
        if (btnEdit) {
            btnEdit.onclick = function () {
                openEventModal({
                    id: event.id,
                    title: event.title,
                    start: event.start,
                    end: event.end,
                    allDay: event.allDay,
                    category: event.extendedProps?.category,
                    description: event.extendedProps?.description,
                    rrule: event.extendedProps?.rrule,
                    extendedProps: event.extendedProps
                });
                detailModal.hide();
            };
        }

        const btnDelete = document.getElementById('btnDetailDelete');
        if (btnDelete) {
            btnDelete.onclick = function () {
                if (!confirm('Delete this event?')) return;
                fetch(`${eventsBaseUrl}/${event.id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                }).then(() => {
                    event.remove();
                    detailModal.hide();
                });
            };
        }

        detailModal.show();
    }

    // (Agenda list removed)

    // Simple calendar filtering/search logic
    const calendarSearch = document.getElementById('calendarSearch');
    if (calendarSearch) {
        calendarSearch.addEventListener('input', function (e) {
            const term = e.target.value.toLowerCase();
            calendar.getEvents().forEach(ev => {
                const match = ev.title.toLowerCase().includes(term) || (ev.extendedProps.description && ev.extendedProps.description.toLowerCase().includes(term));
                ev.setProp('display', match ? 'auto' : 'none');
            });
        });
    }

    const filterCategories = document.querySelectorAll('.filter-category');
    if (filterCategories) {
        filterCategories.forEach(cb => {
            cb.addEventListener('change', () => {
                const activeCats = Array.from(document.querySelectorAll('.filter-category:checked')).map(c => c.value);
                calendar.getEvents().forEach(ev => {
                    const match = activeCats.includes(ev.extendedProps.category);
                    ev.setProp('display', match ? 'auto' : 'none');
                });
            });
        });
    }
    // Initial end load in case script loads after page partially ready
}

document.addEventListener('DOMContentLoaded', initCalendar);
document.addEventListener('livewire:navigated', initCalendar);
