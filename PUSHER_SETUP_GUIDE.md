# Pusher Setup Guide - Step by Step Instructions

## 🎯 Quick Setup Checklist

When creating your Pusher app, use these exact settings:

### App Creation Form Fields:

#### 1. **App Name**
```
K-Derma Booking System
```
or
```
kderma-booking-ms
```

#### 2. **Cluster Selection**
Choose based on your location:
- **For Philippines/Asia**: Select `ap1` (Asia Pacific - Singapore) ⭐ **RECOMMENDED**
- **For Europe**: Select `eu` (Europe - Ireland)
- **For USA**: Select `us2` (United States - Ohio) or `us3` (Oregon)

**Recommended**: `ap1` for best performance in Philippines

#### 3. **Tech Stack Selection**
Check these boxes:
- ✅ **Backend**: `Laravel` (PHP framework)
- ✅ **Frontend**: `Vanilla JavaScript` or `JavaScript`

**Do NOT select**: React, Vue, Angular (we're using Alpine.js + Vanilla JS)

#### 4. **Multiple Environments**
Create **TWO separate apps**:

**App 1 - Development:**
- Name: `K-Derma Booking - Development`
- Cluster: `ap1`
- Backend: ✅ Laravel
- Frontend: ✅ Vanilla JavaScript

**App 2 - Production:**
- Name: `K-Derma Booking - Production`
- Cluster: `ap1` (or closest to your production server)
- Backend: ✅ Laravel
- Frontend: ✅ Vanilla JavaScript

---

## 📋 Detailed Step-by-Step Instructions

### Step 1: Sign Up / Login to Pusher
1. Go to: https://pusher.com/
2. Click "Sign Up" (or "Log In" if you have an account)
3. Complete registration (free plan is sufficient)

### Step 2: Create Development App

1. Click **"Create app"** or **"Channels"** → **"Create app"**

2. Fill in the form:
   ```
   App name: K-Derma Booking - Development
   ```

3. **Select Cluster:**
   - Click the cluster dropdown
   - Select: **`ap1`** (Asia Pacific - Singapore)
   - This is closest to Philippines for best performance

4. **Select Tech Stack:**
   - ✅ Check **"Laravel"** under Backend
   - ✅ Check **"Vanilla JavaScript"** or **"JavaScript"** under Frontend
   - Leave other options unchecked

5. Click **"Create app"**

6. **Copy Your Credentials:**
   After creation, you'll see:
   - **App ID**: `1234567` (copy this)
   - **Key**: `abc123def456` (copy this)
   - **Secret**: `xyz789secret` (copy this - keep this secret!)
   - **Cluster**: `ap1` (already selected)

### Step 3: Create Production App

1. Click **"Create app"** again

2. Fill in the form:
   ```
   App name: K-Derma Booking - Production
   ```

3. **Select Cluster:**
   - Select: **`ap1`** (same as development, or choose closest to your production server)

4. **Select Tech Stack:**
   - ✅ Check **"Laravel"** under Backend
   - ✅ Check **"Vanilla JavaScript"** or **"JavaScript"** under Frontend

5. Click **"Create app"**

6. **Copy Your Production Credentials** (save separately)

---

## 🔧 Configuration in Your Project

### For Development (.env file):

```env
# Broadcasting Configuration
BROADCAST_CONNECTION=pusher
BROADCAST_DRIVER=pusher

# Pusher Configuration (Development App)
PUSHER_APP_ID=your_development_app_id_here
PUSHER_APP_KEY=your_development_app_key_here
PUSHER_APP_SECRET=your_development_app_secret_here
PUSHER_APP_CLUSTER=ap1

# Optional - Usually not needed
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https

# Vite Configuration (for frontend - MUST match PUSHER_APP_KEY)
VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```

### For Production (.env file):

Use the production app credentials instead.

---

## ✅ Verification Checklist

After setup, verify:

- [ ] Pusher app created with correct name
- [ ] Cluster selected (ap1 recommended)
- [ ] Laravel checked in Backend
- [ ] Vanilla JavaScript checked in Frontend
- [ ] Credentials copied to .env file
- [ ] VITE_PUSHER_APP_KEY matches PUSHER_APP_KEY
- [ ] Run `php artisan config:clear`
- [ ] Run `npm run build`

---

## 🎨 Visual Guide - What to Select

```
┌─────────────────────────────────────────┐
│ Create a Channels app                   │
├─────────────────────────────────────────┤
│                                         │
│ App name:                               │
│ ┌─────────────────────────────────────┐ │
│ │ K-Derma Booking - Development       │ │
│ └─────────────────────────────────────┘ │
│                                         │
│ Select a cluster:                       │
│ ┌─────────────────────────────────────┐ │
│ │ ap1 (Asia Pacific - Singapore)  ✓   │ │ ← Select this
│ │ ap2 (Asia Pacific - Mumbai)         │ │
│ │ ap3 (Asia Pacific - Tokyo)          │ │
│ │ ap4 (Asia Pacific - Sydney)        │ │
│ │ eu (Europe - Ireland)                │ │
│ │ us2 (United States - Ohio)           │ │
│ │ us3 (United States - Oregon)         │ │
│ │ mt1 (United States - Montana)        │ │
│ └─────────────────────────────────────┘ │
│                                         │
│ Choose your tech stack:                 │
│                                         │
│ Backend:                                │
│ ☑ Laravel                              │ ← Check this
│ ☐ Node.js                              │
│ ☐ Python                               │
│ ☐ Ruby                                 │
│                                         │
│ Frontend:                               │
│ ☑ Vanilla JavaScript                   │ ← Check this
│ ☐ React                                │
│ ☐ Vue                                  │
│ ☐ Angular                              │
│                                         │
│         [Create app]                    │
└─────────────────────────────────────────┘
```

---

## 🚀 After Creating Apps

1. **Update your .env file** with the credentials
2. **Clear config cache**: `php artisan config:clear`
3. **Build assets**: `npm run build`
4. **Test the chat** on a confirmed appointment

---

## 📞 Need Help?

If you encounter issues:
1. Check browser console for errors
2. Verify all credentials in .env match Pusher dashboard
3. Ensure cluster name is exactly `ap1` (case-sensitive)
4. Make sure `VITE_PUSHER_APP_KEY` matches `PUSHER_APP_KEY`

---

## 💡 Pro Tips

- **Free Plan Limits**: 200,000 messages/day is plenty for most use cases
- **Cluster Selection**: Choose closest to your users for lowest latency
- **Separate Apps**: Always use different apps for dev/prod to avoid confusion
- **Security**: Never commit .env file - keep Pusher Secret safe
