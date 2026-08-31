<?php

namespace App\Mail;

use App\Models\CashAdvance;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CashAdvanceApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public CashAdvance $cashAdvance;
    public User $employee;
    public bool $transferred;
    public ?string $invoiceUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(CashAdvance $cashAdvance, User $employee, bool $transferred = false, ?string $invoiceUrl = null)
    {
        $this->cashAdvance = $cashAdvance;
        $this->employee    = $employee;
        $this->transferred = $transferred;
        $this->invoiceUrl  = $invoiceUrl;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $statusText = $this->transferred ? 'Disetujui & Berhasil Ditransfer' : 'Disetujui';

        return new Envelope(
            subject: 'Kasbon ' . $statusText . ' - ' . $this->cashAdvance->title . ' (' . config('app.name', 'CIO Keuangan') . ')',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.cash-advance-approved',
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
