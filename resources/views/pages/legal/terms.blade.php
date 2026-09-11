<x-layouts.app title="Terms &amp; Conditions | Academia Training Solutions">
    <div class="wrap max-w-[760px] py-14">
        <p class="eyebrow">Legal</p>
        <h1 class="mt-3">Terms &amp; Conditions</h1>
        <p class="lede mt-3">The contract between us</p>
        <p class="mt-3 text-xs text-sand-500">
            Last updated {{ now()->format('j F Y') }} ·
            {{ config('academia.trade_name') }} is a trade name of {{ config('academia.legal_entity') }}
            · KvK 00000000 · VAT NL000000000B01
        </p>

        {{-- Visible on the page, not buried in a code comment, so it cannot be
             published unnoticed. Delete once the review is done. --}}
        <div class="card mt-6 border-dashed p-5">
            <p class="text-sm font-semibold">Have a lawyer read this before you publish it</p>
            <p class="mt-1.5 text-sm text-sand-600">
                This text is drafted to be accurate to how the site actually behaves, which is the
                hard part and the part a downloaded template gets wrong. It is not legal advice. A
                Dutch-qualified lawyer should review it against your final processor list and
                insurance position.
            </p>
        </div>

        <div class="mt-10 space-y-8">
            @include('pages.legal.partials.terms')
        </div>
    </div>
</x-layouts.app>
