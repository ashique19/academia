<x-layouts.app :title="$subcategory->name . ' Courses | Academia'">
    <section class="bg-sand-900 py-14 text-white">
        <div class="wrap">
            <nav aria-label="Breadcrumb" class="text-xs text-white/60">
                <a href="{{ route('courses.index') }}" wire:navigate class="hover:text-white">Catalogue</a>
                <span class="mx-1">›</span>
                <a href="{{ route('courses.category', $category) }}" wire:navigate class="hover:text-white">{{ $category->name }}</a>
                <span class="mx-1">›</span><span>{{ $subcategory->name }}</span>
            </nav>
            <h1 class="mt-3 text-white">{{ $subcategory->name }}</h1>
        </div>
    </section>

    <livewire:public.course-catalogue :subcategory="$subcategory->slug" />
</x-layouts.app>
