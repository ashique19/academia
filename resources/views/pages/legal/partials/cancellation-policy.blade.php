@foreach ([
    ['If you cancel', 'More than 14 days before the start date: full refund or free transfer to another date, no questions. Between 14 and 7 days: transfer free of charge, or a refund less 25%. Inside 7 days: the fee stands, but you may send a colleague in your place at any time up to the start, including on the day.'],
    ['Sending a colleague instead', 'Always free, always allowed, no deadline. Email us the substitute’s name and dietary requirements.'],
    ['If we cancel', 'You get a full refund or a free transfer, and we reimburse non-refundable travel and accommodation you booked for that date on production of the receipt. We do not reserve a right to substitute a virtual course for a classroom one you paid for.'],
    ['Discounted bookings', 'A promotional discount does not change your cancellation rights. A discounted booking cancels and transfers on exactly the same terms as a full-price one — if we were only willing to offer that on full-price seats, the discount would not be real.'],
    ['Self-paced courses', 'Fourteen days to change your mind from the date access is granted, provided you have completed less than 20% of the modules.'],
    ['In-company programmes', 'Governed by the cancellation schedule in the signed proposal, which reflects the expert time and customisation already committed.'],
] as [$heading, $body])
    <section>
        <h2 class="text-xl">{{ $heading }}</h2>
        <p class="mt-2 leading-relaxed text-sand-700">{{ $body }}</p>
    </section>
@endforeach
