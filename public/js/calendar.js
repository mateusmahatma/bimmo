document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');
    if (!calendarEl) return;

    // Use dynamic URL provided from Blade, fallback to '/events'
    const eventsBaseUrl = window.eventsUrl || '/events';

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        firstDay: 1, // Start week on Monday
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
        },
        themeSystem: 'bootstrap5',
        editable: true,
        droppable: true,
        selectable: true,
        height: 'auto',
        events: eventsBaseUrl,

        eventClick: function (info) {
            showPopover(info.event, info.el);
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
        }
    });

    calendar.render();

    // New Event Button
    document.getElementById('btnNewEvent').addEventListener('click', function () {
        openEventModal();
    });

    const modalEl = document.getElementById('addEventModal');
    const bootstrapModal = bootstrap.Modal.getOrCreateInstance(modalEl);
    const form = document.getElementById('eventForm');
    const allDaySwitch = document.getElementById('allDaySwitch');
    const timeInputs = document.getElementById('timeInputsContainer');
    const dateInputs = document.getElementById('dateInputsContainer');

    allDaySwitch.addEventListener('change', function () {
        if (this.checked) {
            timeInputs.classList.add('d-none');
            dateInputs.classList.remove('d-none');
            const startVal = timeInputs.querySelector('[name="start_at"]').value;
            if (startVal) dateInputs.querySelector('[name="start_date"]').value = startVal.split('T')[0];
        } else {
            timeInputs.classList.remove('d-none');
            dateInputs.classList.add('d-none');
        }
    });

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

    emailSwitch.addEventListener('change', function () {
        if (this.checked) {
            emailContainer.classList.remove('d-none');
        } else {
            emailContainer.classList.add('d-none');
        }
    });

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
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        const eventId = document.getElementById('eventId').value;
        const isAllDay = allDaySwitch.checked;

        const startVal = isAllDay ? formData.get('start_date') : formData.get('start_at');
        const endVal = isAllDay ? formData.get('end_date') : formData.get('end_at');

        if (!startVal) {
            alert('Silakan tentukan tanggal/jam mulai.');
            return;
        }

        const data = {
            title: formData.get('title'),
            category: formData.get('category'),
            description: formData.get('description') || null,
            all_day: isAllDay ? 1 : 0,
            start_at: startVal,
            end_at: endVal || null,
            send_email: emailSwitch.checked ? 1 : 0,
            notification_email: emailInput.value
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
                description: event.extendedProps.description
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
    document.addEventListener('click', function (e) {
        const popover = document.getElementById('eventPopover');
        if (popover.style.display === 'block' && !popover.contains(e.target) && !e.target.closest('.fc-event')) {
            hidePopover();
        }
    });

    // Simple calendar filtering/search logic
    document.getElementById('calendarSearch').addEventListener('input', function (e) {
        const term = e.target.value.toLowerCase();
        calendar.getEvents().forEach(ev => {
            const match = ev.title.toLowerCase().includes(term) || (ev.extendedProps.description && ev.extendedProps.description.toLowerCase().includes(term));
            ev.setProp('display', match ? 'auto' : 'none');
        });
    });

    document.querySelectorAll('.filter-category').forEach(cb => {
        cb.addEventListener('change', () => {
            const activeCats = Array.from(document.querySelectorAll('.filter-category:checked')).map(c => c.value);
            calendar.getEvents().forEach(ev => {
                const match = activeCats.includes(ev.extendedProps.category);
                ev.setProp('display', match ? 'auto' : 'none');
            });
        });
    });

    // Dark Mode Observer
    const observer = new MutationObserver(function (mutations) {
        mutations.forEach(function (mutation) {
            if (mutation.attributeName === "class") {
                const isDarkMode = document.body.classList.contains('dark-mode');
                // FullCalendar handles some aspect of theme but we might need manual tweaks
            }
        });
    });
    observer.observe(document.body, { attributes: true });
});
