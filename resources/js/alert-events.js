module.exports = [
    {
        "type": "App\\Events\\TransactionUpdated",
        "name": "When a transaction is updated (moving from pending to not pending, updating amounts, etc...)"
    },
    {
        "type": "App\\Events\\TransactionCreated",
        "name": "When a transaction is initially created (only fired once per transaction)"
    },
    {
        "type": "App\\Events\\TransactionGroupedEvent",
        "name": "When a transaction is added to a group (this gives you access to the `tag` variable in your title, body and payload.)"
    },
    {
        "type": "App\\Events\\BudgetBreachedEstablishedAmount",
        "name": "When a budget's total spend amount for a period exceeds the set amount."
    }
]