<?php

namespace App\Mail;

use App\Models\Proposal;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue; // <--- Importante para Fila
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProposalAwaitingApproval extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $proposal;
    public $formattedValue;

    /**
     * Create a new message instance.
     * Recebe o objeto Proposta e o Valor já formatado (string)
     */
    public function __construct(Proposal $proposal, string $formattedValue)
    {
        $this->proposal = $proposal;
        $this->formattedValue = $formattedValue;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Aprovação Necessária: Proposta #' . $this->proposal->proposal_number,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.proposals.awaiting_approval',
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