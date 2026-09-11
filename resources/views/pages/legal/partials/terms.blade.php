@foreach ([
    ['Formation', 'A booking is confirmed when we send a written confirmation, not when the basket is submitted. Prices are per person, exclude VAT, and are the price published at the moment of booking including any promotional code applied and shown on your confirmation.'],
    ['Payment', 'By card, iDEAL, Bancontact or invoice at 14 days for corporate accounts.'],
    ['Promotions', 'Promotional codes are valid for new bookings inside the published window. Combined savings are capped at ' . config('academia.promotions.max_stack_percent') . '%. Codes are not retro-applied to bookings already confirmed, and a published promotion is not withdrawn before its stated end date.'],
    ['What is included', 'Tuition, materials, the toolkit, the certificate, and lunch and refreshments for classroom deliveries. Exam fees charged by a certification scheme owner are never included unless stated in writing on the course page.'],
    ['Certification wording', 'Where a course prepares you for a third-party certification we say so explicitly and name the scheme owner. Unless a course page states that we hold that scheme owner’s accreditation, we do not issue the scheme certificate and are not affiliated with or endorsed by its owner.'],
    ['Substitution and liability', 'We may substitute an equally qualified expert. Our liability is limited to the fee paid. Nothing here limits liability that cannot be limited under Dutch law.'],
    ['Governing law', 'Dutch law. Disputes go to the competent court in Amsterdam, without prejudice to your rights as a consumer to bring proceedings where you live.'],
] as [$heading, $body])
    <section>
        <h2 class="text-xl">{{ $heading }}</h2>
        <p class="mt-2 leading-relaxed text-sand-700">{{ $body }}</p>
    </section>
@endforeach
