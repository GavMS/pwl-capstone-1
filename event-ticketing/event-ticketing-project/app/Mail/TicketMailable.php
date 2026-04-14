<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\IssuedTicket;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;

class TicketMailable extends Mailable
{
    use Queueable, SerializesModels;

    public $ticket;
    public $event;

    /**
     * Create a new message instance.
     */
    public function __construct(IssuedTicket $ticket)
    {
        $this->ticket = $ticket;
        $this->event = $ticket->eventTicketType->event;
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
     * Attach ticket as PDF file.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $svgContent = QrCode::size(160)->margin(1)->generate($this->ticket->unique_code);
        $qrDataUri = 'data:image/svg+xml;base64,' . base64_encode($svgContent);

        $pdf = Pdf::loadView('pdf.ticket', [
            'ticket' => $this->ticket,
            'event'  => $this->event,
            'qrDataUri' => $qrDataUri,
        ]);

        $filename = 'ticket-' . $this->ticket->unique_code . '.pdf';

        return [
            Attachment::fromData(fn () => $pdf->output(), $filename)
                ->withMime('application/pdf'),
        ];
    }
}
