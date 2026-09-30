<x-layouts.app :title="'Cookie Settings | '.\App\Support\PublicSite::name()">
    <div class="wrap max-w-[760px] py-14">
        <p class="eyebrow">Legal</p>
        <h1 class="mt-3">Cookie Settings</h1>
        <p class="lede mt-3">What we set, and what you can turn off</p>
        <p class="mt-3 text-xs text-sand-500">
            Last updated {{ now()->format('j F Y') }} ·
            {{ config('academia.trade_name') }} is a trade name of {{ config('academia.legal_entity') }}
            · {{ \App\Support\PublicSite::registrationLine() }}
        </p>

        {{-- Counsel still needs to review this draft before it is treated as
             final legal advice. That note stays in source, not on the page. --}}

        <div class="mt-10 space-y-8">
            @include('pages.legal.partials.cookie-settings')
        </div>
    </div>
</x-layouts.app>
