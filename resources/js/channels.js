module.exports = [
    {
        "type": "Illuminate\\Notifications\\Channels\\SlackWebhookChannel",
        "name": "Slack",
        "service": "webhook"
    },
    {
        "type": "NotificationChannels\\Discord\\DiscordChannel",
        "name": "Discord",
        "service": "webhook"
    },
    {
        "type": "NotificationChannels\\Webhook\\WebhookChannel",
        "name": "Webhook",
        "service": "webhook"
    },
    {
        "type": "Illuminate\\Notifications\\Channels\\MailChannel",
        "name": "Email",
        "service": "email"
    },
    {
        "type": "Illuminate\\Notifications\\Channels\\NexmoSmsChannel",
        "name": "Nexmo",
        "service": "sms"
    },
    {
        "type": "Illuminate\\Notifications\\Channels\\DatabaseChannel",
        "name": "In-site notification",
        "service": "in-site"
    }
]