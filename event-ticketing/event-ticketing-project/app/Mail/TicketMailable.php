<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\IssuedTicket;

class TicketMailable extends Mailable
{
    use Queueable, SerializesModels;

    public $ticket;
    public $event;
    public $qrCodeBase64;

    /**
     * Create a new message instance.
     */
    public function __construct(IssuedTicket $ticket)
    {
        $this->ticket = $ticket;
        $this->event = $ticket->eventTicketType->event;
        
        // Generate QR code as SVG (more compatible as it doesn't require GD/Imagick for generation)
        $qrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
            ->margin(1)
            ->size(200)
            ->generate($ticket->unique_code);
            
        $this->qrCodeBase64 = base64_encode($qrCode);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your E-Ticket: ' . $this->event->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.ticket',
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
