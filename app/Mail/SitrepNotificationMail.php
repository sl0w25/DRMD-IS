<?php

namespace App\Mail;

use App\Models\Sitrep;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SitrepNotificationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Sitrep $sitrep;
    public string $messageContent;
    public string $filePath;

    public function __construct($sitrep, $messageContent, $filePath)
    {
        $this->sitrep = $sitrep;
        $this->messageContent = $messageContent;
        $this->filePath = $filePath;

        Log::info('PDF generated', [
            'sitrep_id' => $this->sitrep->id,
            'filePath' => $this->filePath
        ]);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Sitrep No. 1 on the ' . $this->sitrep->incident_type .
                     ' in ' . $this->sitrep->barangay . ', ' .
                     $this->sitrep->municipality . ', ' .
                     $this->sitrep->province
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'sitrep_notification',
            with: [
                'sitrep' => $this->sitrep,
                'messageContent' => $this->messageContent,
            ],
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromPath($this->filePath)
                ->as('Sitrep.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
