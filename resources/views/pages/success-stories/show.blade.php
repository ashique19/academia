<x-layouts.app
    :title="$study->title.' | Academia Success Stories'"
    :description="$study->summary"
>
    <article>
        <header class="bg-sand-900 py-16 text-white">
            <div class="wrap max-w-[760px]">
                @if ($study->sector)
                    <p class="eyebrow !text-gold-400">{{ $study->sector }}</p>
                @endif
                <h1 class="mt-3 text-white">{{ $study->title }}</h1>
                @if ($study->displayClient())
                    <p class="mt-4 text-sm text-white/70">{{ $study->displayClient() }}</p>
                @endif
            </div>
        </header>

        <div class="wrap max-w-[760px] py-12">
            <p class="lede text-sand-700">{{ $study->summary }}</p>

            @if ($study->background)
                <h2 class="mt-10 text-xl">Background</h2>
                <p class="mt-3 leading-relaxed text-sand-700">{{ $study->background }}</p>
            @endif

            @if (! empty($study->approach_points))
                <h2 class="mt-10 text-xl">Approach</h2>
                <ul class="mt-4 space-y-3 text-sand-700">
                    @foreach ($study->approach_points as $point)
                        <li class="flex gap-3">
                            <svg class="mt-1 h-4 w-4 flex-none text-green-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                            <span>{{ $point }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif

            @if ($study->client_approved && $study->client_quote)
                <blockquote class="mt-10 border-l-4 border-orange-500 bg-sand-50 p-6 text-sand-800">
                    <p class="text-lg leading-relaxed">“{{ $study->client_quote }}”</p>
                    @if ($study->client_quote_attribution)
                        <footer class="mt-3 text-sm font-semibold text-sand-600">
                            — {{ $study->client_quote_attribution }}
                            @if ($study->displayClient())
                                , {{ $study->displayClient() }}
                            @endif
                        </footer>
                    @endif
                </blockquote>
            @endif

            <p class="mt-12">
                <a href="{{ route('success-stories.index') }}" class="font-semibold text-orange-600" wire:navigate>
                    ← All success stories
                </a>
            </p>
        </div>
    </article>

    <x-cta-band />
</x-layouts.app>
