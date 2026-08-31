<?php

namespace App\Mail;

use App\Models\CashAdvance;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CashAdvanceSubmittedMail extends Mailable
{
    use Queueable, SerializesModels;

    public CashAdvance $cashAdvance;
    public User $employee;
    public bool $isAdminNotification;

    /**
     * Create a new message instance.
     */
    public function __construct(CashAdvance $cashAdvance, User $employee, bool $isAdminNotification = false)
    {
        $this->cashAdvance         = $cashAdvance;
        $this->employee            = $employee;
        $this->isAdminNotification = $isAdminNotification;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->isAdminNotification
            ? 'Pengajuan Kasbon Baru - ' . $this->employee->name . ' (' . $this->cashAdvance->title . ')'
            : 'Konfirmasi Pengajuan Kasbon Berhasil - ' . config('app.name', 'CIO Keuangan');

        return new Envelope(subject: $subject);
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.cash-advance-submitted',
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
