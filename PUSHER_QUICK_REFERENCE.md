# Pusher Quick Reference Card

## 📝 Form Fields to Fill

### When Creating Pusher App:

| Field | Value |
|-------|-------|
| **App Name** | `K-Derma Booking - Development` |
| **Cluster** | `ap1` (Asia Pacific - Singapore) |
| **Backend** | ✅ Laravel |
| **Frontend** | ✅ Vanilla JavaScript |

---

## 🔑 Credentials You'll Get

After creating the app, you'll receive:

```
App ID:    1234567
Key:       abc123def456ghi789
Secret:    xyz789secret123keep_this_private
Cluster:   ap1
```

---

## 📋 .env Configuration Template

Copy this to your `.env` file and replace with your actual credentials:

```env
# Broadcasting
BROADCAST_CONNECTION=pusher
BROADCAST_DRIVER=pusher

# Pusher Credentials (Development)
PUSHER_APP_ID=PASTE_YOUR_APP_ID_HERE
PUSHER_APP_KEY=PASTE_YOUR_KEY_HERE
PUSHER_APP_SECRET=PASTE_YOUR_SECRET_HERE
PUSHER_APP_CLUSTER=ap1

# Vite Pusher Config (Frontend)
VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```

---

## ✅ Quick Checklist

- [ ] Created Pusher account
- [ ] Created Development app
- [ ] Selected cluster: `ap1`
- [ ] Checked: Laravel (Backend)
- [ ] Checked: Vanilla JavaScript (Frontend)
- [ ] Copied credentials to .env
- [ ] Set `VITE_PUSHER_APP_KEY` = `PUSHER_APP_KEY`
- [ ] Ran `php artisan config:clear`
- [ ] Ran `npm run build`

---

## 🎯 Recommended Settings Summary

- **App Name**: K-Derma Booking - Development
- **Cluster**: ap1 (best for Philippines)
- **Backend**: ✅ Laravel
- **Frontend**: ✅ Vanilla JavaScript
- **Environments**: Create separate apps for dev and prod

