@props(['course', 'price' => null])

@php
    // Resolved once here rather than in the loop, so the card, the course page,
    // the booking widget and the schema cannot disagree about the price.
    $price ??= app(\App\Domain\Catalogue\Services\PromotionService::class)
        ->priceFor($course, 1, $course->next_session_at);
@endphp

<article class="card card-hover flex flex-col overflow-hidden">
    <div class="h-1.5 bg-gradient-to-r from-orange-500 via-gold-400 to-green-500"></div>

    <div class="flex flex-1 flex-col p-5">
        @if ($course->subcategory?->category)
            <p class="text-xs font-bold uppercase tracking-wider text-sand-500">
                {{ $course->subcategory->category->name }}
            </p>
        @endif

        <h3 class="mt-2 text-base font-semibold leading-snug">
            <a href="{{ route('courses.show', $course) }}" wire:navigate
               class="hover:text-orange-600">{{ $course->display_title }}</a>
        </h3>

        <p class="mt-2 line-clamp-3 text-sm text-sand-600">{{ $course->summary }}</p>

        <div class="mt-3 flex flex-wrap gap-1.5">
            <span class="chip">{{ rtrim(rtrim(number_format((float) $course->duration_days, 1), '0'), '.') }} {{ (float) $course->duration_days === 1.0 ? 'day' : 'days' }}</span>
            <span class="chip">{{ $course->level->label() }}</span>
            @foreach ($course->deliveryModes->take(2) as $mode)
                <span class="chip-green">{{ $mode->name }}</span>
            @endforeach
        </div>

        @if ($course->next_session_at)
            <p class="mt-3 text-xs text-sand-500">
                Next: {{ $course->next_session_at->format('j M Y') }}
            </p>
        @endif

        <div class="mt-auto flex items-end justify-between gap-3 border-t border-sand-200 pt-4">
            <div>
                @if ($price->requiresQuote())
                    <span class="text-base font-bold text-sand-800">Request a quote</span>
                @elseif ($price->hasDiscount())
                    <span class="price-now">€{{ number_format($price->finalPriceCents / 100, 0, ',', '.') }}</span>
                    <span class="block">
                        <span class="price-was">€{{ number_format($price->referencePriceCents() / 100, 0, ',', '.') }}</span>
                        <span class="text-xs text-sand-500">excl. VAT</span>
                    </span>
                @else
                    <span class="text-xl font-bold">€{{ number_format($price->listPriceCents / 100, 0, ',', '.') }}</span>
                    <span class="block text-xs text-sand-500">per person, excl. VAT</span>
                @endif
            </div>

            @if ($price->hasDiscount())
                <span class="chip-orange">{{ $price->discountPercent }}% off</span>
            @endif
        </div>
    </div>
</article>
