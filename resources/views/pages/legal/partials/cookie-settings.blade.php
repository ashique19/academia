@foreach ([
    ['Strictly necessary', 'Session, basket and security cookies. These cannot be turned off because the site does not function without them, and they carry no analytics identifiers.'],
    ['Analytics', 'Aggregate page and funnel measurement, EU-hosted, IP truncated before storage. Off until you accept. Declining changes nothing about what you can see or book.'],
    ['Marketing', 'Off by default. If you accept it we may measure which campaign led to a booking. We do not sell or share your data with advertising networks.'],
    ['Changing your mind', 'The preference panel is reachable from this page and from the footer on every page. Withdrawing consent takes effect immediately and deletes the identifiers already set.'],
    ['No dark patterns', '“Accept” and “Decline” are the same size, in the same place, on the first layer. There is no pre-ticked box and no cookie wall.'],
] as [$heading, $body])
    <section>
        <h2 class="text-xl">{{ $heading }}</h2>
        <p class="mt-2 leading-relaxed text-sand-700">{{ $body }}</p>
    </section>
@endforeach
