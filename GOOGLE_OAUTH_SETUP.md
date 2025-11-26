# Google OAuth Setup Guide

## Error: redirect_uri_mismatch

This error occurs when the redirect URI configured in your Google Cloud Console doesn't match what your application is sending.

## Steps to Fix:

### 1. Get Your Application URL

Your application is running on: `http://127.0.0.1:8000` (or check your `.env` file for `APP_URL`)

### 2. Update Google Cloud Console

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Select your project (or create a new one)
3. Navigate to **APIs & Services** > **Credentials**
4. Click on your OAuth 2.0 Client ID
5. Under **Authorized redirect URIs**, add:
   - `http://127.0.0.1:8000/auth/google/callback`
   - `http://localhost:8000/auth/google/callback` (if you also use localhost)
   - For production: `https://yourdomain.com/auth/google/callback`

### 3. Update Your .env File

Make sure your `.env` file has:

```env
APP_URL=http://127.0.0.1:8000

GOOGLE_CLIENT_ID=your-client-id-here
GOOGLE_CLIENT_SECRET=your-client-secret-here
GOOGLE_REDIRECT_URI=http://127.0.0.1:8000/auth/google/callback
```

**Note:** If `GOOGLE_REDIRECT_URI` is not set, it will automatically use `APP_URL/auth/google/callback`

### 4. Clear Configuration Cache

After updating, run:
```bash
php artisan config:clear
```

### 5. Important Notes

- The redirect URI in Google Cloud Console must **exactly match** what your app sends
- Include the protocol (`http://` or `https://`)
- Include the port number if not using standard ports (80 for http, 443 for https)
- No trailing slash
- Case-sensitive

### 6. Testing

After updating:
1. Clear your browser cache
2. Try signing in with Google again
3. Check Laravel logs if issues persist: `storage/logs/laravel.log`

## Common Issues

### Issue: Still getting redirect_uri_mismatch
- **Solution:** Make sure the redirect URI in Google Cloud Console matches exactly (including http/https, port, and path)
- Wait a few minutes after updating Google Cloud Console (changes may take time to propagate)

### Issue: Invalid client
- **Solution:** Check that `GOOGLE_CLIENT_ID` and `GOOGLE_CLIENT_SECRET` in `.env` match your Google Cloud Console credentials

### Issue: Access blocked
- **Solution:** Make sure your Google account is added as a test user in Google Cloud Console (OAuth consent screen > Test users)

