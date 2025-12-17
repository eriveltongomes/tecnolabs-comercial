<?php

namespace App\Mail;

use App\Models\WorkOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue; // <--- Importante para Fila
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;

class WorkOrderToPilot extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $workOrder;
    public $pdfPath;

    /**
     * Create a new message instance.
     */
    public function __construct(WorkOrder $workOrder, $pdfPath = null)
    {
        $this->workOrder = $workOrder;
        $this->pdfPath = $pdfPath;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Sua Escalação: Ordem de Serviço #' . $this->workOrder->id,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.workorders.pilot_notification',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        // Verifica se o caminho foi passado e se o arquivo existe antes de anexar
        if ($this->pdfPath && file_exists($this->pdfPath)) {
            return [
                Attachment::fromPath($this->pdfPath)
                    ->as('OS-' . $this->workOrder->id . '.pdf')
                    ->withMime('application/pdf'),
            ];
        }
        
        return [];
    }
}