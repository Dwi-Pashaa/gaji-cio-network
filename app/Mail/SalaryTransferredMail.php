<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SalaryTransferredMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $employee;
    public string $monthYearStr;
    public float $baseSalary;
    public float $totalAllowance;
    public float $totalCashAdvance;
    public float $netSalary;
    public string $bankName;
    public string $accountNumber;
    public string $accountHolderName;
    public string $dateStr;
    public ?string $invoiceUrl;
    public ?string $paymentType;

    /**
     * Create a new message instance.
     */
    public function __construct(
        User $employee,
        string $monthYearStr,
        float $baseSalary,
        float $totalAllowance,
        float $totalCashAdvance,
        float $netSalary,
        string $bankName,
        string $accountNumber,
        string $accountHolderName,
        string $dateStr,
        ?string $invoiceUrl = null,
        ?string $paymentType = 'xendit'
    ) {
        $this->employee          = $employee;
        $this->monthYearStr      = $monthYearStr;
        $this->baseSalary        = $baseSalary;
        $this->totalAllowance    = $totalAllowance;
        $this->totalCashAdvance  = $totalCashAdvance;
        $this->netSalary         = $netSalary;
        $this->bankName          = $bankName;
        $this->accountNumber     = $accountNumber;
        $this->accountHolderName = $accountHolderName;
        $this->dateStr           = $dateStr;
        $this->invoiceUrl        = $invoiceUrl;
        $this->paymentType       = $paymentType;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Slip Pembayaran Gaji Periode ' . $this->monthYearStr . ' - ' . $this->employee->name . ' (' . config('app.name', 'CIO Keuangan') . ')',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.salary-transferred',
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
