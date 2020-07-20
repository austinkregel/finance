module.exports = {
    "Illuminate\\Notifications\\Channels\\SlackWebhookChannel": {
        "name": "Slack",
        "fields": [
            "webhook_url",
            "channel"
        ]
    },
    "NotificationChannels\\Discord\\DiscordChannel": {
        "name": "Discord",
        "fields": [
            "webhook_url",
            "channel"
        ]
    },
    "NotificationChannels\\Webhook\\WebhookChannel": {
        "name": "Webhook",
        "fields": [
            "webhook_url",
            "payload"
        ]
    },
    "Illuminate\\Notifications\\Channels\\MailChannel": {
        "name": "Email",
        "fields": [
            "email"
        ]
    },
    "Illuminate\\Notifications\\Channels\\NexmoSmsChannel": {
        "name": "Nexmo",
        "fields": [
            "sms_number"
        ]
    },
    "Illuminate\\Notifications\\Channels\\DatabaseChannel": {
        "name": "In-site notification",
        "fields": []
    }
}