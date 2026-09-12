<x-filament-panels::page>
    {{ $this->content }}

    @if (filled($this->lastOutput))
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 mt-6">
            <div class="fi-section-content-ctn p-6">
                <h3 class="text-sm font-semibold text-gray-950 dark:text-white mb-2">Last import output</h3>
                <pre class="overflow-x-auto whitespace-pre-wrap text-xs text-gray-700 dark:text-gray-300">{{ $this->lastOutput }}</pre>
            </div>
        </div>
    @endif
</x-filament-panels::page>
