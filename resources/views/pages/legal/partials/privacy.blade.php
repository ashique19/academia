@foreach ([
    ['Who is the controller', 'Academia Training Solutions, a trade name of ' . config('academia.legal_entity') . ', registered in the Netherlands. Our data protection contact is privacy@academiatraining.eu.'],
    ['What we collect and why', 'Booking data (name, work email, employer, dietary requirements where you give them) to deliver the training and issue your certificate. Enquiry data to answer you. Aggregate analytics to see which pages help people decide.'],
    ['Where it is processed', 'Inside the EEA. Our hosting, email and payment processors are EU-based or covered by a signed data processing agreement with EU-adequate safeguards.'],
    ['How long we keep it', 'Booking and certificate records for seven years, because Dutch tax and administration law requires it. Marketing consent until you withdraw it. Enquiries that do not become bookings, 24 months.'],
    ['Your rights', 'Access, correction, erasure, restriction, portability and objection. Email the address above and you get a substantive answer within 30 days. You may also complain to the Autoriteit Persoonsgegevens.'],
    ['Fonts and third parties', 'Our web fonts are self-hosted, so loading a page on this site does not send your IP address to a third-party font provider. Where a page embeds an independent review widget or a video, that provider is named at the point of embedding.'],
] as [$heading, $body])
    <section>
        <h2 class="text-xl">{{ $heading }}</h2>
        <p class="mt-2 leading-relaxed text-sand-700">{{ $body }}</p>
    </section>
@endforeach
