<?php

declare(strict_types=1);

namespace App\Mail;

use App\Domain\Catalogue\Models\Course;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers;
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

    /**
     * Mailto only. This is one outline to the person who asked for it,
     * not a list, so there is no one-click List-Unsubscribe-Post URL.
     */
    public function headers(): Headers
    {
        return new Headers(
            text: [
                'List-Unsubscribe' => '<mailto:info@academiatraining.eu>',
            ],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.course-outline',
            text: 'mail.course-outline-text',
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
