<x-layouts.app title="Training Glossary — Plain-Language Definitions | Academia">
    <section class="bg-sand-900 py-14 text-white">
        <div class="wrap">
            <h1 class="text-white">Plain-language definitions, written by practitioners</h1>
            <p class="lede mt-3 max-w-[65ch] text-white/75">
                Every term links to the training that covers it properly. If we cannot explain it in
                a paragraph, we have not understood it well enough to teach it.
            </p>
        </div>
    </section>

    <div class="wrap grid gap-5 py-14 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($terms as $term)
            <a href="{{ route('glossary.term', $term) }}" wire:navigate class="card card-hover p-5">
                <h2 class="text-base">{{ $term->term }}</h2>
                <p class="mt-2 line-clamp-3 text-sm text-sand-600">{{ $term->definition }}</p>
            </a>
        @endforeach
    </div>
</x-layouts.app>
