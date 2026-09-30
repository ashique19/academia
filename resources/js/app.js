/**
 * Show every session start time in the visitor's own zone.
 *
 * Sessions are stored UTC with an IANA zone alongside. A participant who joins
 * an hour late because the site showed CET without saying so is a refund, so
 * the conversion happens client-side where the real zone is known.
 *
 * Runs on first load and again after every Livewire navigation, because
 * wire:navigate swaps the DOM without a full page load.
 */
function localiseSessionTimes() {
    const viewerZone = Intl.DateTimeFormat().resolvedOptions().timeZone;

    document.querySelectorAll('[data-utc]:not([data-localised])').forEach((el) => {
        const utc = el.dataset.utc;
        const sourceZone = el.dataset.zone;

        el.setAttribute('data-localised', 'true');

        if (!utc || !sourceZone || sourceZone === viewerZone) return;

        try {
            const local = new Date(utc).toLocaleTimeString([], {
                hour: '2-digit', minute: '2-digit', timeZone: viewerZone,
            });

            const note = document.createElement('span');
            note.className = 'text-sand-500';
            note.textContent = ` (${local} your time)`;
            el.appendChild(note);
        } catch {
            // An unknown zone is not worth breaking the page over.
        }
    });
}

document.addEventListener('DOMContentLoaded', localiseSessionTimes);
document.addEventListener('livewire:navigated', localiseSessionTimes);

/**
 * Stop an invalid public form before Livewire starts a request.
 *
 * Livewire disables the submit button and swaps in "Sending…" as soon as
 * submit fires. HTML5 validation that loses that race leaves the button
 * stuck, with no message under the fields. This listener runs in the
 * capture phase, so an invalid form never reaches that handler.
 */
document.addEventListener('invalid', (event) => {
    const field = event.target;

    if (!(field instanceof HTMLElement)) {
        return;
    }

    const form = field.closest('form');

    if (!form?.hasAttribute('data-validate')) {
        return;
    }

    // The invalid event fires before submit, and it does not bubble. Swallow
    // the browser tooltip and show the message that sits in the form.
    event.preventDefault();

    form.querySelectorAll(':invalid').forEach((el) => {
        el.setAttribute('aria-invalid', 'true');
    });

    const banner = form.querySelector('[data-form-error]');

    if (banner) {
        banner.hidden = false;
    }

    field.focus();
}, true);

document.addEventListener('submit', (event) => {
    const form = event.target;

    if (!(form instanceof HTMLFormElement) || !form.hasAttribute('data-validate')) {
        return;
    }

    form.querySelectorAll('[aria-invalid="true"]').forEach((el) => {
        el.removeAttribute('aria-invalid');
    });

    const banner = form.querySelector('[data-form-error]');

    if (form.checkValidity()) {
        if (banner) {
            banner.hidden = true;
        }

        return;
    }

    event.preventDefault();
    event.stopImmediatePropagation();

    form.querySelectorAll(':invalid').forEach((el) => {
        el.setAttribute('aria-invalid', 'true');
    });

    if (banner) {
        banner.hidden = false;
    }

    form.querySelector(':invalid')?.focus();
}, true);
