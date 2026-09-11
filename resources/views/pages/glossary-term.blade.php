<x-layouts.app :title="$term->term . ' — Definition | Academia'" :description="$term->definition">
    @push('schema')
        <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org', '@type' => 'DefinedTerm',
            'name' => $term->term, 'description' => $term->definition,
            'url' => route('glossary.term', $term),
        ], JSON_UNESCAPED_SLASHES) !!}
        </script>
    @endpush

    <div class="wrap max-w-[760px] py-14">
        <nav aria-label="Breadcrumb" class="text-xs text-sand-500">
            <a href="{{ route('glossary') }}" wire:navigate class="hover:text-orange-600">Glossary</a>
            <span class="mx-1">›</span><span>{{ $term->term }}</span>
        </nav>
        <h1 class="mt-3">{{ $term->term }}</h1>
        <p class="lede mt-4">{{ $term->definition }}</p>
        @if ($term->body)
            <p class="mt-5 leading-relaxed text-sand-700">{{ $term->body }}</p>
        @endif

        @if ($courses->isNotEmpty())
            <h2 class="mt-12">Learn this properly</h2>
            <div class="mt-6 grid gap-5 sm:grid-cols-2">
                @foreach ($courses as $course)
                    <x-course-card :course="$course" />
                @endforeach
            </div>
        @endif
    </div>
    <x-cta-band />
</x-layouts.app>
