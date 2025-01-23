<?php

namespace App\Mail;

use App\Models\Course;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewPurchasedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Course $course)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Purchased',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.new-purchased',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
