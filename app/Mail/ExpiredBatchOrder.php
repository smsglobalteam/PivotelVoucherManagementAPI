<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ExpiredBatchOrder extends Mailable
{
    use Queueable, SerializesModels;

    public $batchOrders;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($batchOrders)
    {
        $this->batchOrders = $batchOrders;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Expiring Batch Order')
                    ->markdown('emails.expired_batch-order_alert')
                    ->with('batchOrders', $this->batchOrders);
    }
}
