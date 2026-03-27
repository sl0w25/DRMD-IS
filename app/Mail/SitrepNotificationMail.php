<?php

namespace App\Mail;

use App\Models\Sitrep;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SitrepNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public Sitrep $sitrep;
    public string $messageContent;
    public string $filePath;

  public function __construct(Sitrep $sitrep, string $messageContent, string $filePath)
    {
        $this->sitrep = $sitrep;
        $this->messageContent = $messageContent;
        $this->filePath = $filePath;
    }

  public function build()
    {
        return $this->subject("Sitrep #{$this->sitrep->id} Notification")
            ->view('emails.sitrep_notification')
            ->with([
                'sitrep' => $this->sitrep,
                'messageContent' => $this->messageContent,
            ])
            ->attach($this->filePath, [
                'as' => 'sitrep.pdf',
                'mime' => 'application/pdf',
            ]);
    }


    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Sitrep Notification Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'sitrep_notification',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
