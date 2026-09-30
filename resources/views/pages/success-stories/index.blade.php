<x-layouts.app
    :title="'Success Stories | '.\App\Support\PublicSite::name()"
    description="Career pathways these courses are built for. Named clients appear only where approval is on record."
>
    <section class="bg-sand-900 py-16 text-white">
        <div class="wrap max-w-[820px]">
            <p class="eyebrow !text-gold-400">Success stories</p>
            <h1 class="mt-3 text-white">Programmes that changed how teams work</h1>
            <p class="lede mt-4 text-white/80">
                Case studies from corporate programmes. Client names and quotes appear only when
                the client has signed both off — empty slots stay empty.
            </p>
        </div>
    </section>

    <section class="py-14">
        <div class="wrap">
            {{-- Ported from the live success-stories page. These are programme
                 designs, not named participants, and no quote is attributed. --}}
            <p class="eyebrow">Career pathways</p>
            <h2 class="mt-3">Career pathways these courses are built for</h2>
            <p class="lede mt-3 max-w-[70ch]">
                Each pathway is the progression a course sequence is designed to support.
                Timeframes are planning guidance, not a promise, and no participant names
                are published here.
            </p>

            <div class="mt-8 grid gap-6 lg:grid-cols-3">
                @foreach ([
                    ['Financial analyst → Data analyst', 'Finance function, logistics sector · Netherlands', 'Data Analytics Foundations + Power BI Advanced', 'Can build a pivot table, but cannot explain a number to the board without three days of preparation.', 'Owns a commercial dashboard suite; the monthly board pack is automated rather than rebuilt.', '6–9 months'],
                    ['Project coordinator → Programme manager', 'Manufacturing, multi-site · France', 'PRINCE2 Practitioner preparation + Stakeholder Management', 'Several years coordinating projects without a formal method, passed over for programme roles for lack of certification.', 'Certification passed and a programme-level remit — typically a site consolidation or a systems rollout.', '4–6 months'],
                    ['Warehouse supervisor → Supply chain planner', 'Distribution, two DCs · Germany', 'S&OP + Inventory Optimisation + SAP MM', 'Knows the warehouse floor better than anyone but has never touched planning, and the ERP is a black box.', 'Runs the demand plan across both distribution centres, with excess stock measurably reduced.', '9–12 months'],
                ] as $pathway)
                    <article class="card p-6">
                        <h3 class="text-lg">{{ $pathway[0] }}</h3>
                        <p class="mt-1 text-xs text-sand-500">{{ $pathway[1] }}</p>
                        <p class="mt-3 text-sm font-semibold text-orange-700">{{ $pathway[2] }}</p>
                        <p class="mt-4 text-sm text-sand-700"><span class="font-semibold">Starting point.</span> {{ $pathway[3] }}</p>
                        <p class="mt-2 text-sm text-sand-700"><span class="font-semibold">Target · {{ $pathway[5] }}.</span> {{ $pathway[4] }}</p>
                    </article>
                @endforeach
            </div>
            <p class="mt-6 text-sm text-sand-500">These describe programme design, not individual participants.</p>
        </div>
    </section>

    <section class="bg-brand-cream py-14">
        <div class="wrap">
            <h2>Client case studies</h2>
            @if ($studies->isEmpty())
                <p class="mt-3 max-w-[65ch] text-sand-600">
                    Case studies will appear here once a client has signed them off. We do not
                    publish a name or a quote without that approval.
                </p>
                <p class="mt-4 text-sm">
                    <a href="{{ route('contact') }}" class="font-semibold text-orange-600 hover:underline" wire:navigate>Talk to a training advisor</a>
                    about a programme for your team.
                </p>
            @else
                <div class="mt-8 grid gap-6 md:grid-cols-2">
                    @foreach ($studies as $study)
                        <article class="card p-7">
                            @if ($study->sector)
                                <span class="chip">{{ $study->sector }}</span>
                            @endif
                            <h2 class="mt-3 text-xl">
                                <a href="{{ route('success-stories.show', $study) }}" class="hover:text-orange-600" wire:navigate>
                                    {{ $study->title }}
                                </a>
                            </h2>
                            <p class="mt-3 text-sm text-sand-600">{{ $study->summary }}</p>
                            @if ($study->displayClient())
                                <p class="mt-4 text-xs font-semibold text-sand-500">{{ $study->displayClient() }}</p>
                            @endif
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <x-cta-band />
</x-layouts.app>
