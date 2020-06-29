<?php

namespace App\Notifications;

use App\Mail\TransactionAlertMail;
use App\Models\AlertLog;
use App\Models\Transaction;
use App\Tag;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class TransactionTagAlertNotification extends Notification
{
    use Queueable;

    public AlertLog $alertLog;

    public function __construct(AlertLog $alertLog)
    {
        $this->alertLog = $alertLog;
    }

    public function via($notifiable)
    {
        return $this->alertLog->alert->channels;
    }

    public function toArray($notifiable)
    {
        return [
            'title' => $this->renderField($this->alertLog->alert->title),
            'body' => $this->renderField($this->alertLog->alert->body),
            'payload' => $this->renderField($this->alertLog->alert->payload),
            'channels' => $this->alertLog->alert->channels,
            'webhook_url' => $this->alertLog->alert->webhook_url,
        ];
    }

    protected function renderField($field)
    {
        if (Str::contains($field, ['{{', '}}'])) {
            return $this->render($field, array_merge([
                'transaction' => $this->alertLog->transaction->toArray(),
            ], $this->alertLog->tag ? [
                'tag' => $this->alertLog->tag->toArray(),
            ] : []));
        }

        return $field;
    }

    protected function render($string, $data)
    {
        return app(\Mustache_Engine::class)->render($string, $data);
    }

    public function toMail($notifiable)
    {
        return (new TransactionAlertMail($this->alertLog))->to($notifiable->email);
    }
}
