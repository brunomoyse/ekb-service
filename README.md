## About the project

This app is a Laravel API endpoint to mainly make the link between a UI and the Meta API (WhatsApp messages).
The API is sending messages to an insurance company customers to notify them that their insurance is about to expire.
A Laravel job (CRON task) is made to notify them automatically a certain period before the expiration but the user can decide to notify earlier via the UI, or edit the customers data.
