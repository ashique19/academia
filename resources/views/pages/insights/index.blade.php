<x-layouts.app
    title="Insights | Academia Training Solutions"
    description="Practical articles on corporate training, capability building and course selection — written for L&D and hiring managers."
>
    <section class="bg-sand-900 py-16 text-white">
        <div class="wrap max-w-[820px]">
            <p class="eyebrow !text-gold-400">Insights</p>
            <h1 class="mt-3 text-white">Notes from the training floor</h1>
            <p class="lede mt-4 text-white/80">
                Short, practical pieces on briefing providers, choosing delivery modes, and
                building capability without a theatre of learning.
            </p>
        </div>
    </section>

    <section class="py-14">
        <div class="wrap">
            @if ($posts->isEmpty())
                <p class="text-sand-600">No articles published yet. Check back soon.</p>
            @else
                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($posts as $post)
                        <article class="card flex flex-col p-6">
                            @if ($post->category)
                                <span class="chip">{{ $post->category->name }}</span>
                            @endif
                            <h2 class="mt-3 text-lg">
                                <a href="{{ route('insights.show', $post) }}" class="hover:text-orange-600" wire:navigate>
                                    {{ $post->title }}
                                </a>
                            </h2>
                            <p class="mt-2 flex-1 text-sm text-sand-600">{{ $post->excerpt }}</p>
                            <p class="mt-4 text-xs text-sand-500">
                                {{ $post->published_at?->format('j M Y') }}
                                · {{ $post->reading_time }} min read
                            </p>
                        </article>
                    @endforeach
                </div>

                <div class="mt-10">
                    {{ $posts->links() }}
                </div>
            @endif
        </div>
    </section>
</x-layouts.app>
