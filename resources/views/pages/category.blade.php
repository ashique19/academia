<x-layouts.app :title="$category->name . ' Training Courses | Academia'"
               :description="$category->summary">

    <section class="bg-sand-900 py-14 text-white">
        <div class="wrap">
            <nav aria-label="Breadcrumb" class="text-xs text-white/60">
                <a href="{{ route('home') }}" wire:navigate class="hover:text-white">Home</a>
                <span class="mx-1">›</span>
                <a href="{{ route('courses.index') }}" wire:navigate class="hover:text-white">Catalogue</a>
                <span class="mx-1">›</span><span>{{ $category->name }}</span>
            </nav>
            <h1 class="mt-3 text-white">{{ $category->name }} training</h1>
            @if ($category->summary)
                <p class="lede mt-3 max-w-[65ch] text-white/75">{{ $category->summary }}</p>
            @endif
        </div>
    </section>

    <div class="wrap py-14">
        <h2>Browse by topic</h2>
        <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($subcategories as $subcategory)
                <a href="{{ route('courses.subcategory', [$category, $subcategory]) }}" wire:navigate
                   class="card card-hover p-5">
                    <h3 class="text-base">{{ $subcategory->name }}</h3>
                    <p class="mt-1.5 text-sm text-sand-600">
                        {{ $subcategory->published_courses_count }}
                        {{ \Illuminate\Support\Str::plural('course', $subcategory->published_courses_count) }}
                    </p>
                </a>
            @endforeach
        </div>

        <div class="mt-10">
            <a href="{{ route('courses.index', ['category' => $category->slug]) }}" wire:navigate class="btn-primary btn-lg">
                See all {{ $category->name }} courses
            </a>
        </div>
    </div>

    <x-cta-band />
</x-layouts.app>
