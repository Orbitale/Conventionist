/**
 * @param {string}  eventCalendarConfig.elements.eventModalElementId
 * @param {string}  eventCalendarConfig.elements.eventModalElementNewSlotId
 * @param {string}  eventCalendarConfig.elements.calendarElementId
 * @param {string}  eventCalendarConfig.locale
 * @param {string}  eventCalendarConfig.timezone
 * @param {boolean} eventCalendarConfig.allowSelection
 * @param {Object}  eventCalendarConfig.data
 * @param {Array<{
 *     end: string|Date,
 *     start: string|Date,
 *     color: string|null|undefined,
 *     extendedProps: Object,
 *     id: string,
 *     resourceId: string,
 *     title: string|null|undefined,
 * }>}   eventCalendarConfig.data.events
 * @param {Array}   eventCalendarConfig.data.resources
 * @param {Object}  eventCalendarConfig.event
 * @param {string}  eventCalendarConfig.event.startsAt
 * @param {string}  eventCalendarConfig.event.endsAt
 * @param {string}  eventCalendarConfig.translations.unconfigured_timeslot
 */
export default function displayCalendar(eventCalendarConfig) {
    "use strict";

    /** @var {HTMLElement} eventModalElement */
    const eventModalElement = document.getElementById(eventCalendarConfig.elements.eventModalElementId);
    const eventModal = new bootstrap.Modal(eventModalElement);

    /** @var {HTMLElement} calendarElement */
    const calendarElement = document.getElementById(eventCalendarConfig.elements.calendarElementId);

    if (!calendarElement) {
        throw new Error('No calendar element.');
    }

    const newSlotContainer = eventModalElement.querySelector('#'+eventCalendarConfig.elements.eventModalElementNewSlotId);

    let currentTimeSlot;

    const dateFormatter = new Intl.DateTimeFormat(eventCalendarConfig.locale, {
        dateStyle: 'medium',
        timeStyle: 'short',
        timeZone: eventCalendarConfig.timezone
    });

    function resetModalContents () {
        eventModalElement.querySelector('#event_modal_label').style.display = 'none';
        eventModalElement.querySelector('#slot_modal_label').style.display = 'none';
        eventModalElement.querySelector('#empty_slot').style.display = 'none';
        eventModalElement.querySelector('#event_form').style.display = 'none';
        newSlotContainer.style.display = 'none';
        newSlotContainer.querySelector('input[name="start"]').value = "";
        newSlotContainer.querySelector('input[name="end"]').value = "";
        newSlotContainer.querySelector('input[name="booth_id"]').value = "";
        eventModalElement.querySelectorAll('[data-formaction-template]').forEach(btn => btn.setAttribute('formaction', ''));
    }

    function resizeCalendar() {
        calendarElement.style.width = '0';
        calendarElement.style.width = getComputedStyle(calendarElement.parentElement).width.replace('px', '') - 25 + 'px'
    }

    const events = [...eventCalendarConfig.data.events].map(event => {
        if (typeof event.start === 'string') {
            event.start = new Date(Date.parse(event.start));
        }
        if (typeof event.end === 'string') {
            event.end = new Date(Date.parse(event.end));
        }
        return event;
    });

    window.addEventListener('resize', resizeCalendar);

    const ec = EventCalendar.create(calendarElement, {
        initialView: 'resourceTimelineDay',
        view: 'resourceTimelineDay',
        height: 'auto',
        locale: eventCalendarConfig.locale,
        eventTimeFormat: {
            timeStyle: 'short',
            timeZone: eventCalendarConfig.timezone
        },
        slotLabelFormat: {
            timeStyle: 'short',
            timeZone: eventCalendarConfig.timezone
        },
        nowIndicator: false,
        selectable: eventCalendarConfig.allowSelection,
        editable: false,
        eventStartEditable: false,
        eventDurationEditable: false,
        eventResizableFromStart: false,
        pointer: true,
        flexibleSlotTimeLimits: false,
        resources: eventCalendarConfig.data.resources,
        eventSources: [{events: () => events}],
        date: eventCalendarConfig.event.startsAt,
        highlightedDates: [
            eventCalendarConfig.event.startsAt,
            eventCalendarConfig.event.endsAt,
        ],
        validRange: {
            start: eventCalendarConfig.event.startsAt,
            end: eventCalendarConfig.event.endsAt,
        },
        headerToolbar: {
            start: 'title',
            center: '',
            end: 'prev,next',
        },
        eventDidMount: () => {
            resizeCalendar();
        },
        select: function (info) {
            if (info.allDay) {
                return;
            }
            if (info.resource.extendedProps?.slot_type !== 'booth') {
                ec.unselect();
                return;
            }
            const slot = ec.addEvent({
                start: info.start,
                end: info.end,
                resourceIds: [info.resource.id],
                title: "⚠ "+eventCalendarConfig.translations.unconfigured_timeslot,
                extendedProps: {
                    type: 'empty_slot'
                },
            });
            events.push(slot);
            ec.unselect();
        },
        unselect: function () {
            const latest = events[events.length - 1];
            if (!latest || !latest.id) {
                return;
            }
            if (latest.id.match(/\{generated-/gi)) {
                if (latest.resourceIds.length > 1) {
                    throw new Error('Error: more than one resource.');
                }
                const resourceId = latest.resourceIds[0];
                const resources = ec.getOption('resources').filter(r => r.id === resourceId);
                if (!resources.length) {
                    throw new Error(`No resource with id "${resourceId}".`);
                }
            } else {
                console.error('Apparently, ID is not a generated one and you created an event anyway: are your sure you did not hack the project? ;)');
            }
            currentTimeSlot = null;
        },
        eventClick: function (info) {
            resetModalContents();

            eventModalElement.querySelector('#event_title').innerHTML = info.event.title;
            eventModalElement.querySelector('#event_start').innerHTML = dateFormatter.format(info.event.start);
            eventModalElement.querySelector('#event_end').innerHTML = dateFormatter.format(info.event.end);

            if (info.event.extendedProps?.type === 'activity') {
                eventModalElement.querySelector('#event_modal_label').style.display = 'block';
                if (info.event.extendedProps?.can_be_validated) {
                    eventModalElement.querySelector('#event_form').style.display = 'block';
                    eventModalElement.querySelectorAll('[data-formaction-template]')
                        .forEach(btn => btn.setAttribute('formaction', btn.getAttribute('data-formaction-template').replace(/__ENTITY_ID__/, info.event.id)));
                }
            }

            if (info.event.extendedProps?.type === 'empty_slot') {
                if (info.event.id.match(/\{generated-/gi)) {
                    currentTimeSlot = info.event;
                    // Time slot being created
                    newSlotContainer.style.display = 'block';
                    newSlotContainer.querySelector('input[name="start"]').value = new Date(Date.parse(info.event.start)).toISOString();
                    newSlotContainer.querySelector('input[name="end"]').value = new Date(Date.parse(info.event.end)).toISOString();
                    newSlotContainer.querySelector('input[name="booth_id"]').value = info.event.resourceIds[0];
                } else {
                    // Existing time slot
                    eventModalElement.querySelector('#slot_modal_label').style.display = 'block';
                    eventModalElement.querySelector('#empty_slot').style.display = 'block';
                    eventModalElement.querySelectorAll('[data-basehref]')
                        .forEach(btn => btn.setAttribute('href', btn.getAttribute('data-basehref').replace(/__SLOT_ID__/, info.event.id)));
                }
            }

            eventModal.show();
        }
    });

    newSlotContainer.querySelector('button[data-remove]').addEventListener('click', function () {
        if (!currentTimeSlot) {
            throw new Error('Clicked "remove" on time slot, but no time slot is selected.');
        }

        eventModal.hide();
        ec.removeEventById(currentTimeSlot.id);
    });

}
