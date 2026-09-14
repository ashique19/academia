<footer class="mt-24 bg-sand-900 text-sand-300">
    <div class="wrap grid gap-10 py-14 md:grid-cols-2 lg:grid-cols-5">

        <div class="lg:col-span-1">
            <p class="font-display text-xl font-semibold text-white">Academia</p>
            <p class="text-[9.5px] font-bold uppercase tracking-[0.19em] text-sand-400">Training Solutions</p>
            <p class="mt-4 max-w-[34ch] text-sm">
                Practical, expert-led professional training delivered online, onsite and in
                classrooms across Europe.
            </p>
            <p class="mt-3 text-xs font-bold text-gold-400">
                A trade name of {{ str_replace(' B.V.', '', config('academia.legal_entity')) }}
            </p>
        </div>

        <div>
            <h5 class="mb-3 text-xs font-bold uppercase tracking-widest text-white">Training areas</h5>
            <ul class="space-y-2 text-sm">
                @foreach (\App\Domain\Catalogue\Models\CourseCategory::active()->ordered()->take(6)->get() as $category)
                    <li><a href="{{ route('courses.category', $category) }}" class="hover:text-white" wire:navigate>{{ $category->name }}</a></li>
                @endforeach
                <li><a href="{{ route('courses.index') }}" class="font-semibold text-gold-400 hover:text-gold-300" wire:navigate>All courses →</a></li>
            </ul>
        </div>

        <div>
            <h5 class="mb-3 text-xs font-bold uppercase tracking-widest text-white">Delivery</h5>
            <ul class="space-y-2 text-sm">
                <li><a href="{{ route('online') }}" class="hover:text-white" wire:navigate>Live online training</a></li>
                <li><a href="{{ route('corporate') }}" class="hover:text-white" wire:navigate>Onsite corporate training</a></li>
                <li><a href="{{ route('locations') }}" class="hover:text-white" wire:navigate>Public classroom courses</a></li>
                <li><a href="{{ route('schedule') }}" class="hover:text-white" wire:navigate>Training schedule</a></li>
                <li><a href="{{ route('offers') }}" class="hover:text-white" wire:navigate>Offers &amp; discounts</a></li>
            </ul>
        </div>

        <div>
            <h5 class="mb-3 text-xs font-bold uppercase tracking-widest text-white">Popular cities</h5>
            <ul class="space-y-2 text-sm">
                @foreach (\App\Domain\Shared\Models\City::active()->hasUpcomingSessions()->with('country')->orderBy('name')->take(5)->get() as $city)
                    <li>
                        <a href="{{ route('locations.city', [$city->country, $city]) }}" class="hover:text-white" wire:navigate>
                            Training in {{ $city->name }}
                        </a>
                    </li>
                @endforeach
                <li><a href="{{ route('locations') }}" class="font-semibold text-gold-400 hover:text-gold-300" wire:navigate>All locations →</a></li>
            </ul>
        </div>

        <div>
            <h5 class="mb-3 text-xs font-bold uppercase tracking-widest text-white">Company</h5>
            <ul class="space-y-2 text-sm">
                <li><a href="{{ route('about') }}" class="hover:text-white" wire:navigate>About us</a></li>
                <li><a href="{{ route('contact') }}" class="hover:text-white" wire:navigate>Contact</a></li>
                <li><a href="{{ route('insights.index') }}" class="hover:text-white" wire:navigate>Insights</a></li>
                <li><a href="{{ route('success-stories.index') }}" class="hover:text-white" wire:navigate>Success stories</a></li>
                <li><a href="{{ route('skills-credits') }}" class="hover:text-white" wire:navigate>Skills Credits</a></li>
                <li><a href="{{ route('why-our-price') }}" class="hover:text-white" wire:navigate>Why our price</a></li>
                <li><a href="{{ route('glossary') }}" class="hover:text-white" wire:navigate>Glossary</a></li>
                <li><a href="{{ route('faq') }}" class="hover:text-white" wire:navigate>FAQ</a></li>
            </ul>
        </div>
    </div>


    <div class="wrap border-t border-white/10 py-10">
        <div class="grid items-start gap-6 lg:grid-cols-2">
            <div>
                <h5 class="text-xs font-bold uppercase tracking-widest text-white">Training updates</h5>
                <p class="mt-2 max-w-[40ch] text-sm text-sand-400">
                    Occasional notes on new dates, Skills Credits and capability programmes. No weekly noise.
                </p>
            </div>
            <div class="min-w-0">
                <livewire:public.newsletter-form />
            </div>
        </div>
    </div>

    <div class="border-t border-white/10">
        <div class="wrap flex flex-wrap items-center justify-between gap-3 py-5 text-xs">
            <span>
                © {{ date('Y') }} {{ config('academia.trade_name') }}, a trade name of
                <strong class="text-gold-400">{{ config('academia.legal_entity') }}</strong>
                · KvK 00000000 · VAT NL000000000B01
            </span>
            {{-- Every one of these resolves. In an earlier build of this
                 product they had no destination on any page of the site. --}}
            <span class="flex flex-wrap gap-4">
                <a href="{{ route('legal', 'privacy') }}" class="hover:text-white" wire:navigate>Privacy &amp; GDPR</a>
                <a href="{{ route('legal', 'terms') }}" class="hover:text-white" wire:navigate>Terms</a>
                <a href="{{ route('legal', 'cancellation-policy') }}" class="hover:text-white" wire:navigate>Cancellation policy</a>
                <a href="{{ route('legal', 'cookie-settings') }}" class="hover:text-white" wire:navigate>Cookie settings</a>
            </span>
        </div>
    </div>
</footer>
