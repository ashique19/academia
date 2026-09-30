<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $course->display_title }} — course outline</title>
</head>
<body>
    <p>The outline for <strong>{{ $course->display_title }}</strong> is attached as a PDF.</p>

    <p>
        You can also download it here. The link expires in 14 days:<br>
        <a href="{{ $downloadUrl }}">{{ $downloadUrl }}</a>
    </p>

    <p>One email with the PDF. No sales sequence unless you ask for one.</p>

    <p>{{ \App\Support\PublicSite::name() }}</p>
</body>
</html>
