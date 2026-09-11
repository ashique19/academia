<x-layouts.app title="Frequently Asked Questions | Academia Training Solutions">
    @push('schema')
        <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $faqs->flatten()->map(fn ($faq) => [
                '@type' => 'Question',
                'name' => $faq->question,
                'acceptedAnswer' => ['@type' => 'Answer', 'text' => $faq->answer],
            ])->values()->all(),
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
        </script>
    @endpush

    <section class="bg-sand-900 py-14 text-white">
        <div class="wrap"><h1 class="text-white">Frequently asked questions</h1></div>
    </section>

    <div class="wrap max-w-[820px] py-14">
        @foreach ($faqs as $group => $groupFaqs)
            <h2 class="mt-10 first:mt-0 capitalize">{{ $group ?: 'General' }}</h2>
            <div class="mt-5 divide-y divide-sand-200 overflow-hidden rounded-card border border-sand-200">
                @foreach ($groupFaqs as $faq)
                    <details class="group">
                        <summary class="flex cursor-pointer items-center gap-3 p-5 font-semibold hover:bg-sand-50">
                            <span class="flex-1">{{ $faq->question }}</span>
                            <svg class="h-4 w-4 text-sand-400 transition group-open:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="m6 9 6 6 6-6"/></svg>
                        </summary>
                        <p class="border-t border-sand-100 bg-sand-50/50 p-5 text-sm text-sand-600">{{ $faq->answer }}</p>
                    </details>
                @endforeach
            </div>
        @endforeach
    </div>
    <x-cta-band />
</x-layouts.app>
