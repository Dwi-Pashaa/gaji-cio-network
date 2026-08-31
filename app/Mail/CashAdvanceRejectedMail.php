<?php

namespace App\Mail;

use App\Models\CashAdvance;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CashAdvanceRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public CashAdvance $cashAdvance;
    public User $employee;

    /**
     * Create a new message instance.
     */
    public function __construct(CashAdvance $cashAdvance, User $employee)
    {
        $this->cashAdvance = $cashAdvance;
        $this->employee    = $employee;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pemberitahuan Kasbon Ditolak - ' . $this->cashAdvance->title . ' (' . config('app.name', 'CIO Keuangan') . ')',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.cash-advance-rejected',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
