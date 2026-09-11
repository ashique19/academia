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
