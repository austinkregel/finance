<?php

namespace App\Notifications;

use App\Mail\TransactionAlertMail;
use App\Models\AlertLog;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;
use NotificationChannels\Discord\DiscordMessage;
use NotificationChannels\Webhook\WebhookMessage;

class AlertNotification extends Notification
{
    use Queueable;

    protected $alertLog;

    public function __construct(AlertLog $alertLog)
    {
        $alertLog->load(['alert', 'transaction', 'budget']);
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
        $this->alertLog->load(['transaction', 'tag', 'budget']);

        $transaction = $this->alertLog->transaction;
        $tag = $this->alertLog->tag;
        $budget = $this->alertLog->budget;

        if (Str::contains($field, ['{{', '}}'])) {
            return $this->render($field, array_merge(
                $transaction ? [
                'transaction' => $transaction->toArray(),
            ] : [],
                $tag ? [
                'tag' => $tag->toArray(),
            ] : [],
                $budget ? [
                'budget' => $budget->toArray() + ['total_spends' => $budget->findTotalSpends($budget->getStartOfCurrentPeriod()) ],
            ] : []
            ));
        }

        return $field;
    }

    protected function render($string, $data)
    {
        return app(\Mustache_Engine::class)->render($string, $data);
    }

    public function routeNotificationForDiscord()
    {
        return config('services.discord_webhook_url');
    }

    public function routeNotificationForSlack($notification)
    {
        return config('services.slack_webhook_url');
    }

    public function toMail($notifiable)
    {
        return (new TransactionAlertMail($this->alertLog))->to($notifiable->email);
    }

    public function toSlack($notifiable)
    {
        return (new SlackMessage)
            ->content(sprintf('We saw you were charged $%s by %s to your account %s', $this->alertLog->transaction->amount, $this->alertLog->transaction->name, $this->alertLog->transaction->account->name))
            ->to($this->alertLog->alert->messaging_service_channel)
            ->warning();
    }

    public function toDiscord($notifiable)
    {
        return (new DiscordMessage)
            ->body(sprintf('We saw you were charged $%s by %s to your account %s', $this->alertLog->transaction->amount, $this->alertLog->transaction->name, $this->alertLog->transaction->account->name));
    }

    public function toWebhook($notifiable)
    {
        return WebhookMessage::create()
            ->data([
                'payload' => $this->renderField($this->alertLog->alert->payload)
            ]);
    }
}
