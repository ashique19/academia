<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 28px 36px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #241C15; line-height: 1.45; }
        h1 { font-size: 20px; line-height: 1.2; margin: 0 0 6px; }
        h2 { font-size: 13px; margin: 16px 0 6px; }
        p { margin: 0 0 8px; }
        ul { margin: 0 0 8px; padding-left: 16px; }
        li { margin: 0 0 3px; }
        .meta { color: #5C5647; font-size: 10px; }
        .rule { border-top: 2px solid #B14705; margin: 10px 0 14px; }
        table { width: 100%; border-collapse: collapse; }
        td { vertical-align: top; padding: 3px 8px 3px 0; }
    </style>
</head>
<body>
    <p class="meta">{{ $brand }} · Course outline</p>
    <h1>{{ $course->display_title }}</h1>
    @if ($course->subcategory)
        <p class="meta">{{ $course->subcategory->name }}</p>
    @endif
    <div class="rule"></div>

    @if (filled($course->summary))
        <p>{{ $course->summary }}</p>
    @endif

    <p><strong>Price:</strong> {{ $priceLabel }}</p>
    <p class="meta">
        {{ rtrim(rtrim(number_format((float) $course->duration_days, 1), '0'), '.') }}
        {{ (float) $course->duration_days === 1.0 ? 'day' : 'days' }}
        · {{ $course->level->label() }}
        · Maximum {{ $course->max_participants }} participants
    </p>

    <h2>What you will be able to do</h2>
    @if (filled($course->learning_objectives))
        <ul>
            @foreach ($course->learning_objectives as $objective)
                <li>{{ $objective }}</li>
            @endforeach
        </ul>
    @else
        <p>Objectives are confirmed with your advisor.</p>
    @endif

    <h2>Programme</h2>
    @forelse ($course->modules as $module)
        <p><strong>{{ $loop->iteration }}. {{ $module->title }}</strong></p>
        @if (filled($module->bullets))
            <ul>
                @foreach ($module->bullets as $bullet)
                    <li>{{ $bullet }}</li>
                @endforeach
            </ul>
        @endif
    @empty
        <p>The module list is confirmed with your advisor.</p>
    @endforelse

    <h2>Who should attend</h2>
    <p>{{ $course->target_audience ? str_replace('|', ', ', $course->target_audience) : 'Open to anyone building capability in this subject.' }}</p>

    <h2>Prerequisites</h2>
    <p>{{ $course->prerequisites ?: 'None. This course starts from first principles.' }}</p>

    <h2>Upcoming dates</h2>
    @if ($course->schedules->isEmpty())
        <p>No public dates are on the calendar yet. Ask us and we will send the next one.</p>
    @else
        <table>
            @foreach ($course->schedules as $session)
                <tr>
                    <td>{{ $session->starts_at->timezone(config('app.timezone'))->format('j M Y') }}</td>
                    <td>{{ $session->location_label }}</td>
                    <td>{{ $session->deliveryMode?->name }}</td>
                </tr>
            @endforeach
        </table>
    @endif

    <p class="meta" style="margin-top: 18px;">
        Prices exclude VAT. This outline is the version you can forward internally.
        It is not a confirmed booking.
    </p>
</body>
</html>
