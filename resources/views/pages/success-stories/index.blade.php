<x-layouts.app
    title="Success Stories | Academia Training Solutions"
    description="Client programmes and capability outcomes. Named clients appear only where approval is on record."
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
            @if ($studies->isEmpty())
                <p class="text-sand-600">No published case studies yet.</p>
            @else
                <div class="grid gap-6 md:grid-cols-2">
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
