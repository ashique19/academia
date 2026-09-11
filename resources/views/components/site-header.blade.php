<div class="bg-sand-900 text-sand-300">
    <div class="wrap flex flex-wrap items-center justify-between gap-2 py-2 text-xs">
        <div class="flex flex-wrap items-center gap-x-5 gap-y-1">
            <span>Training delivered in 20+ European cities</span>
            {{-- Corporate fact, not a technology endorsement. --}}
            <span class="hidden sm:inline">A trade name of {{ str_replace(' B.V.', '', config('academia.legal_entity')) }}</span>
        </div>
        <div class="flex flex-wrap items-center gap-x-5 gap-y-1">
            <a href="mailto:{{ config('academia.email') }}" class="hover:text-white">{{ config('academia.email') }}</a>
            <a href="tel:{{ preg_replace('/\s+/', '', config('academia.phone')) }}" class="hover:text-white">{{ config('academia.phone') }}</a>
        </div>
    </div>
</div>

<header
    x-data="{ open: false, mega: null }"
    class="sticky top-0 z-40 border-b border-sand-200 bg-white/95 backdrop-blur"
>
    <div class="wrap flex items-center justify-between gap-4 py-3.5">

        <a href="{{ route('home') }}" class="flex items-center gap-2.5" wire:navigate>
            <svg class="h-10 w-10 flex-none" viewBox="0 0 48 48" aria-hidden="true">
                <defs>
                    <linearGradient id="lg" x1="0" y1="0" x2="1" y2="1">
                        <stop offset="0" stop-color="#E0A526"/>
                        <stop offset="55%" stop-color="#B14705"/>
                        <stop offset="100%" stop-color="#2E6B4F"/>
                    </linearGradient>
                </defs>
                <rect width="48" height="48" rx="12" fill="#241C15"/>
                <path d="M13 34 24 12l11 22" fill="none" stroke="url(#lg)" stroke-width="3.4" stroke-linejoin="round" stroke-linecap="round"/>
                <path d="M18.4 27.5h11.2" stroke="#F2F0E3" stroke-width="2.6" stroke-linecap="round"/>
                <circle cx="24" cy="12" r="3" fill="#E0A526"/>
            </svg>
            <span class="leading-tight">
                <span class="block font-display text-lg font-semibold">Academia</span>
                <span class="block text-[9.5px] font-bold uppercase tracking-[0.19em] text-sand-500">Training Solutions</span>
            </span>
        </a>

        <nav class="hidden items-center gap-1 lg:flex" aria-label="Main">
            @foreach ([
                ['Training Catalogue', 'courses.index'],
                ['Schedule', 'schedule'],
                ['Classroom Locations', 'locations'],
                ['Corporate Training', 'corporate'],
                ['Online Training', 'online'],
            ] as [$label, $routeName])
                <a href="{{ route($routeName) }}" wire:navigate
                   @class([
                       'rounded-lg px-3 py-2 text-sm font-semibold transition hover:bg-sand-50',
                       'text-orange-600' => request()->routeIs($routeName . '*'),
                       'text-sand-700'   => ! request()->routeIs($routeName . '*'),
                   ])>{{ $label }}</a>
            @endforeach
        </nav>

        <div class="flex items-center gap-2">
            <a href="{{ route('corporate') }}#proposal" wire:navigate
               class="btn-green hidden sm:inline-flex">Request Proposal</a>

            <button type="button" @click="open = !open"
                    class="rounded-lg border border-sand-300 p-2 lg:hidden"
                    :aria-expanded="open.toString()" aria-controls="mobile-nav"
                    aria-label="Open menu">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <path d="M4 7h16M4 12h16M4 17h16"/>
                </svg>
            </button>
        </div>
    </div>

    <div x-show="open" x-cloak id="mobile-nav" class="border-t border-sand-200 bg-white lg:hidden">
        <nav class="wrap grid gap-1 py-4" aria-label="Mobile">
            @foreach ([
                ['Training Catalogue', 'courses.index'], ['Schedule', 'schedule'],
                ['Classroom Locations', 'locations'], ['Corporate Training', 'corporate'],
                ['Online Training', 'online'], ['Offers', 'offers'], ['FAQ', 'faq'],
            ] as [$label, $routeName])
                <a href="{{ route($routeName) }}" wire:navigate
                   class="rounded-lg px-3 py-2.5 text-sm font-semibold text-sand-800 hover:bg-sand-50">{{ $label }}</a>
            @endforeach
            <a href="{{ route('corporate') }}#proposal" class="btn-green mt-2" wire:navigate>Request Proposal</a>
        </nav>
    </div>
</header>
