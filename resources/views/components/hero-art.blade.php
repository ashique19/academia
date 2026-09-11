{{--
    Placeholder hero illustration.

    Deliberately an inline SVG rather than a stock photograph: it is on-brand,
    weightless, and honest. Replace with commissioned photography of a real
    session — see the photography brief in the project documentation.
--}}
<svg viewBox="0 0 560 460" class="w-full drop-shadow-2xl" role="img"
     aria-label="Illustration of online, classroom and in-company training">
    <rect x="70" y="20" width="330" height="215" rx="18" fill="#FBF3EE"/>
    <rect x="140" y="55" width="190" height="90" rx="8" fill="#241C15"/>
    <rect x="158" y="72" width="120" height="7" rx="3.5" fill="#E0A526"/>
    <rect x="158" y="88" width="155" height="6" rx="3" fill="#7E7663"/>
    <path d="M158 128l24-20 20 14 26-24" stroke="#6FAF91" stroke-width="4" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
    <rect x="70" y="150" width="330" height="40" fill="#F2F0E3"/>
    @foreach ([[128, '#2E6B4F'], [225, '#B14705'], [322, '#E0A526']] as [$x, $fill])
        <circle cx="{{ $x }}" cy="152" r="17" fill="#8B6B52"/>
        <path d="M{{ $x - 22 }} 196a22 22 0 0 1 44 0z" fill="{{ $fill }}"/>
    @endforeach

    <rect x="330" y="245" width="215" height="170" rx="16" fill="#241C15"/>
    @foreach ([[348, 262, 110, 68, '#2E6B4F'], [468, 262, 62, 68, '#B14705'], [348, 340, 62, 58, '#E0A526'], [420, 340, 58, 58, '#6FAF91'], [486, 340, 44, 58, '#5C5647']] as [$x, $y, $w, $h, $fill])
        <rect x="{{ $x }}" y="{{ $y }}" width="{{ $w }}" height="{{ $h }}" rx="7" fill="{{ $fill }}"/>
    @endforeach

    <rect x="360" y="35" width="185" height="70" rx="12" fill="#FFFDF8"/>
    <circle cx="392" cy="70" r="18" fill="#C48C15"/>
    <path d="M385 70l5 5 10-11" stroke="#fff" stroke-width="3.4" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
    <rect x="422" y="60" width="100" height="7" rx="3.5" fill="#241C15"/>
    <rect x="422" y="76" width="72" height="6" rx="3" fill="#A9A18A"/>
</svg>
