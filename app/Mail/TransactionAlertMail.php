<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\AlertLog;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TransactionAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public AlertLog $alertLog;

    public function __construct(AlertLog $alertLog)
    {
        $this->alertLog = $alertLog;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->alertLog->load([
            'transaction.account',
            'tag',
        ]);

        return $this->markdown('mail.transaction', [
            'alertLog' => $this->alertLog,
            'transaction' => $this->alertLog->transaction,
            'tag' => $this->alertLog->tag,
        ]);
    }
}
