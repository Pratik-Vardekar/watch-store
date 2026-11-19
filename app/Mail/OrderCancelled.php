<?php
// app/Mail/OrderCancelled.php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class OrderCancelled extends Mailable
{
    use Queueable, SerializesModels;

    public Order $order;
    public $cancelledAt;

    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->order->loadMissing(['user', 'items.product', 'shippingAddress']);
        $this->cancelledAt = optional($order->cancelled_at ?? $order->cancel_approved_at)->format('F j, Y g:i A')
            ?: Carbon::now()->format('F j, Y g:i A');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Order Cancelled - #' . ($this->order->order_id ?? $this->order->id),
            from: new Address(config('mail.from.address'), config('mail.from.name')),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order_cancel_user',
            with: [
                'order' => $this->order,
                'cancelledAt' => $this->cancelledAt,
                'orderItems' => $this->order->items ?? [],
                'customerName' => $this->order->user?->name ?? 'Customer',
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}