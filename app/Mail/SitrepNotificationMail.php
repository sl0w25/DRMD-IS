<?php

namespace App\Mail;

use App\Models\Sitrep;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

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
    }

    public function build()
    {
        Log::info('PDF generated', [
            'sitrep_id' => $this->sitrep->id,
            'filePath' => $this->filePath
        ]);

        // New subject
        $subject = 'Sitrep No. 1 on the '. $this->sitrep->incident_type .' in '. $this->sitrep->barangay .', '. $this->sitrep->municipality .', '. $this->sitrep->province;

        return $this->subject($subject)
                    ->view('sitrep_notification')
                    ->attach($this->filePath);
    }


    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: ('Sitrep No. 1 on the '. $this->sitrep->incident_type .' in '. $this->sitrep->barangay .', '. $this->sitrep->municipality .', '. $this->sitrep->province),
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
