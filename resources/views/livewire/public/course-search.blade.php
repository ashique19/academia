<form wire:submit="search"
      class="flex flex-col gap-2 rounded-card bg-white p-2 shadow-lg sm:flex-row sm:items-center">

    <label class="sr-only" for="hero-q">What do you want to learn?</label>
    <input id="hero-q" type="search" wire:model="query"
           placeholder="What do you want to learn? e.g. Power BI, IFRS, leadership…"
           class="min-w-0 flex-1 rounded-lg border-0 px-3 py-2.5 text-sm text-sand-900 placeholder:text-sand-400 focus:ring-0">

    <label class="sr-only" for="hero-cat">Subject area</label>
    <select id="hero-cat" wire:model="category"
            class="rounded-lg border-0 bg-sand-50 px-3 py-2.5 text-sm text-sand-900 focus:ring-0">
        <option value="">All subjects</option>
        @foreach ($this->categories as $category)
            <option value="{{ $category->slug }}">{{ $category->name }}</option>
        @endforeach
    </select>

    <label class="sr-only" for="hero-city">City</label>
    <select id="hero-city" wire:model="city"
            class="rounded-lg border-0 bg-sand-50 px-3 py-2.5 text-sm text-sand-900 focus:ring-0">
        <option value="">Anywhere</option>
        @foreach ($this->cities as $city)
            <option value="{{ $city->slug }}">{{ $city->name }}</option>
        @endforeach
    </select>

    <button type="submit" class="btn-primary shrink-0">
        <span wire:loading.remove wire:target="search">Find training</span>
        <span wire:loading wire:target="search">Searching…</span>
    </button>
</form>
