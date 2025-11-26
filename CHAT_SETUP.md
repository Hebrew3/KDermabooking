# Real-Time Chat System Setup Guide

## Overview
The chat system allows clients and staff to communicate in real-time when an appointment is confirmed. It uses Laravel Broadcasting with Pusher for real-time messaging and optionally integrates with Google Chat API for notifications.

## Features
- ✅ Real-time messaging between clients and assigned staff
- ✅ Chat automatically created when appointment is confirmed
- ✅ Unread message count indicators
- ✅ Google Chat API integration for notifications (optional)
- ✅ Beautiful, responsive chat widget UI

## Setup Instructions

### 1. Install Dependencies
The required packages have been installed:
- `laravel-echo` - Laravel's broadcasting client
- `pusher-js` - Pusher JavaScript client

### 2. Configure Broadcasting

#### Option A: Using Pusher (Recommended)
1. Sign up for a free account at [Pusher](https://pusher.com/)
2. Create a new app and get your credentials
3. Add to your `.env` file:
```env
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=mt1

VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```

#### Option B: Using Laravel Reverb (Alternative)
If you prefer a self-hosted solution, you can use Laravel Reverb.

### 3. Configure Google Chat API (Optional)

1. Go to Google Chat API in Google Cloud Console
2. Create a webhook for your Google Chat space
3. Add to your `.env` file:
```env
GOOGLE_CHAT_ENABLED=true
GOOGLE_CHAT_WEBHOOK_URL=https://chat.googleapis.com/v1/spaces/SPACE_ID/messages?key=KEY&token=TOKEN
```

### 4. Build Assets
```bash
npm run build
# or for development
npm run dev
```

### 5. Run Migrations
The migrations have already been run. If you need to run them again:
```bash
php artisan migrate
```

## Usage

### For Clients
1. When an appointment is confirmed, a chat button appears on the appointment details page
2. Click the chat button to open the chat window
3. Start messaging with your assigned staff member
4. Messages are delivered in real-time

### For Staff
1. When viewing a confirmed appointment, the chat widget appears
2. Staff can respond to client messages in real-time
3. Unread message counts are displayed

### For Admins
1. Admins can view and participate in any conversation
2. Chat is available on appointment detail pages

## How It Works

1. **Appointment Confirmation**: When an appointment status changes to "confirmed", a chat conversation is automatically created
2. **Real-Time Messaging**: Messages are broadcast using Laravel Echo and Pusher
3. **Notifications**: If Google Chat is enabled, notifications are sent to Google Chat when new messages arrive
4. **Message Persistence**: All messages are stored in the database for history

## API Endpoints

- `GET /chat/appointment/{appointmentId}` - Get or create conversation for appointment
- `GET /chat/{conversationId}/messages` - Get all messages in a conversation
- `POST /chat/{conversationId}/send` - Send a new message
- `POST /chat/{conversationId}/read` - Mark messages as read
- `GET /chat/conversations` - Get all user's conversations

## Troubleshooting

### Chat widget not appearing
- Ensure the appointment status is "confirmed" or "in_progress"
- Check that both client_id and staff_id are set
- Verify Alpine.js is loaded (check browser console)

### Messages not sending in real-time
- Verify Pusher credentials are correct
- Check browser console for errors
- Ensure broadcasting is enabled in `.env`
- Verify Laravel Echo is properly initialized

### Google Chat notifications not working
- Check `GOOGLE_CHAT_ENABLED` is set to `true`
- Verify webhook URL is correct
- Check Laravel logs for errors

## Security

- All chat routes are protected with authentication middleware
- Users can only access conversations they're part of
- Broadcasting channels are authorized per user
- Messages are validated before saving

## Future Enhancements

- File/image attachments
- Typing indicators
- Message reactions
- Chat history search
- Group chats for multiple staff members

