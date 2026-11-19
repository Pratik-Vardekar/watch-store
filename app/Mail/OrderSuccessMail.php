<?php

namespace App\Mail;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class OrderSuccessMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order)
    {
        $this->order->loadMissing([
            'user',
            'items.product',
            'items.variant',
            'shippingAddress',
            'billingAddress',
            'addresses',
            'orderPayments',
            'refunds',
        ]);
    }

    public function envelope(): Envelope
    {
        $label = $this->order->invoice_no ?: $this->order->order_id;
        $isPaid = strtolower($this->order->payment_status ?? '') === 'paid';
        $subject = $isPaid ? 'Payment Receipt & Tax Invoice — ' . $label : 'Invoice & Order Confirmation — ' . $label;

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order_success',
        );
    }

    public function attachments(): array
    {
        try {
            $pdf = Pdf::loadView('admin.order.invoice', ['order' => $this->order])
                ->setPaper('a4');

            $filename = 'invoice-' . ($this->order->invoice_no ?: $this->order->order_id) . '.pdf';

            return [
                Attachment::fromData(fn () => $pdf->output(), $filename)
                    ->withMime('application/pdf'),
            ];
        } catch (\Throwable $e) {
            Log::error('Order invoice PDF attach failed: ' . $e->getMessage(), [
                'order_id' => $this->order->id,
            ]);

            return [];
        }
    }
}
