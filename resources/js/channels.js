module.exports = [
    {
        "name": "Slack",
        "type": "Illuminate\\Notifications\\Channels\\SlackWebhookChannel"
    },
    {
        "name": "Discord",
        "type": "NotificationChannels\\Discord\\DiscordChannel"
    },
    {
        "name": "Webhook",
        "type": "NotificationChannels\\Webhook\\WebhookChannel"
    },
    {
        "name": "Email",
        "type": "Illuminate\\Notifications\\Channels\\MailChannel"
    },
    {
        "name": "Nexmo",
        "type": "Illuminate\\Notifications\\Channels\\NexmoSmsChannel"
    },
    {
        "name": "In-site notification",
        "type": "Illuminate\\Notifications\\Channels\\DatabaseChannel"
    }
]