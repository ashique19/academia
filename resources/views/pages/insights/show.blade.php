<x-layouts.app
    :title="$post->seo?->title ?? ($post->title.' | Academia Insights')"
    :description="$post->seo?->description ?? $post->excerpt"
>
    <article>
        <header class="bg-sand-900 py-16 text-white">
            <div class="wrap max-w-[760px]">
                @if ($post->category)
                    <p class="eyebrow !text-gold-400">{{ $post->category->name }}</p>
                @endif
                <h1 class="mt-3 text-white">{{ $post->title }}</h1>
                <p class="mt-4 text-sm text-white/70">
                    {{ $post->published_at?->format('j F Y') }}
                    · {{ $post->reading_time }} min read
                    @if ($post->author)
                        · {{ $post->author->name }}
                    @endif
                </p>
            </div>
        </header>

        <div class="wrap max-w-[760px] py-12">
            @if ($post->excerpt)
                <p class="lede text-sand-700">{{ $post->excerpt }}</p>
            @endif

            <div class="prose mt-8 max-w-none text-sand-800">
                {!! \Illuminate\Support\Str::markdown($post->body) !!}
            </div>

            <p class="mt-12">
                <a href="{{ route('insights.index') }}" class="font-semibold text-orange-600" wire:navigate>
                    ← All insights
                </a>
            </p>
        </div>
    </article>

    <x-cta-band />
</x-layouts.app>
