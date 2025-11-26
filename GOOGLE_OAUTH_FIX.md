# Google OAuth Redirect URI Mismatch - Fix Guide

## Problem
Error 400: `redirect_uri_mismatch` occurs when the redirect URI in your request doesn't match what's configured in Google Cloud Console.

## Solution Steps

### 1. Check Your Current Configuration

Check your `.env` file and ensure you have:
```env
APP_URL=http://127.0.0.1:8000
# OR for production:
# APP_URL=https://yourdomain.com

GOOGLE_CLIENT_ID=your-client-id-here
GOOGLE_CLIENT_SECRET=your-client-secret-here
GOOGLE_REDIRECT_URI=http://127.0.0.1:8000/auth/google/callback
```

**Important:** The `GOOGLE_REDIRECT_URI` must exactly match what you configure in Google Cloud Console.

### 2. Update Google Cloud Console

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Select your project
3. Navigate to **APIs & Services** > **Credentials**
4. Click on your OAuth 2.0 Client ID
5. Under **Authorized redirect URIs**, add:
   - For local development: `http://127.0.0.1:8000/auth/google/callback`
   - Also add: `http://localhost:8000/auth/google/callback` (if you use localhost)
   - For production: `https://yourdomain.com/auth/google/callback`

**Critical Points:**
- Must match **exactly** (including http/https, port, and path)
- No trailing slash
- Case-sensitive
- Include the full path: `/auth/google/callback`

### 3. Clear Configuration Cache

After updating your `.env` file, run:
```bash
php artisan config:clear
php artisan cache:clear
```

### 4. Verify the Redirect URI

The application will automatically use:
- `GOOGLE_REDIRECT_URI` from `.env` if set
- Otherwise: `APP_URL/auth/google/callback`

To check what redirect URI is being used, check the Laravel logs:
```bash
tail -f storage/logs/laravel.log
```

Look for entries like:
```
Google OAuth redirect: {"redirect_uri":"http://127.0.0.1:8000/auth/google/callback",...}
```

### 5. Common Issues

**Issue:** Still getting redirect_uri_mismatch
- **Solution:** Wait 1-2 minutes after updating Google Cloud Console (changes need to propagate)
- Double-check the redirect URI matches exactly (no trailing slash, correct protocol)
- Clear browser cache and cookies
- Try in incognito/private browsing mode

**Issue:** Works locally but not in production
- **Solution:** Make sure you have the production URL in Google Cloud Console
- Ensure `APP_URL` in production `.env` matches your domain
- Check if you're using HTTPS in production (must match in Google Console)

**Issue:** Different ports
- **Solution:** If using a non-standard port (e.g., `:8000`), include it in both `.env` and Google Console

### 6. Testing

1. Clear browser cache
2. Try signing in with Google
3. Check Laravel logs for any errors
4. Verify the redirect URI in logs matches what's in Google Console

## Code Changes Made

The following improvements were made to ensure consistent redirect URI handling:

1. **GoogleController.php:**
   - Both `redirectToGoogle()` and `handleGoogleCallback()` now use the same logic to determine redirect URI
   - Ensures trailing slashes are removed
   - Better logging for debugging

2. **config/services.php:**
   - Improved redirect URI fallback logic
   - Handles missing `APP_URL` gracefully

## Need Help?

If issues persist:
1. Check `storage/logs/laravel.log` for detailed error messages
2. Verify your Google OAuth credentials are correct
3. Ensure your OAuth consent screen is properly configured
4. Check that the OAuth client is enabled in Google Cloud Console

