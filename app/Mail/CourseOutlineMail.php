<?php

declare(strict_types=1);

namespace App\Mail;

use App\Domain\Catalogue\Models\Course;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * The one email the outline form promises. No follow-up sequence.
 */
class CourseOutlineMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public Course $course,
        public string $downloadUrl,
        public string $pdf,
        public string $filename,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->course->display_title.' — course outline',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.course-outline',
        );
    }

    /**
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [
            Attachment::fromData(fn (): string => $this->pdf, $this->filename)
                ->withMime('application/pdf'),
        ];
    }
}
