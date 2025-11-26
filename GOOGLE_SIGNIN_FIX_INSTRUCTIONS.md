# Google Sign-In Fix Instructions (Tagalog/English)

## Problem
Hindi functional ang Google Sign-In - Error 400: redirect_uri_mismatch

## Solution Steps

### Step 1: I-check ang .env file

Buksan ang `.env` file at siguraduhing mayroon ng:

```env
APP_URL=http://127.0.0.1:8000
# O kung localhost:
# APP_URL=http://localhost:8000

GOOGLE_CLIENT_ID=your-google-client-id-here
GOOGLE_CLIENT_SECRET=your-google-client-secret-here
GOOGLE_REDIRECT_URI=http://127.0.0.1:8000/auth/google/callback
```

**IMPORTANTE:** 
- Ang `GOOGLE_REDIRECT_URI` ay dapat EXACTLY match sa Google Cloud Console
- Walang trailing slash (/)
- Kasama ang protocol (http:// o https://)
- Kasama ang port number kung hindi standard (8000)

### Step 2: I-update ang Google Cloud Console

1. Pumunta sa [Google Cloud Console](https://console.cloud.google.com/)
2. Piliin ang iyong project
3. Pumunta sa **APIs & Services** > **Credentials**
4. I-click ang iyong OAuth 2.0 Client ID
5. Sa **Authorized redirect URIs**, i-add:
   - `http://127.0.0.1:8000/auth/google/callback`
   - `http://localhost:8000/auth/google/callback` (kung gumagamit ka ng localhost)
   - Para sa production: `https://yourdomain.com/auth/google/callback`

**CRITICAL:** 
- Dapat EXACTLY match ang redirect URI
- Walang trailing slash
- Case-sensitive
- Kasama ang full path: `/auth/google/callback`

### Step 3: Clear configuration cache

Pagkatapos i-update ang `.env`, i-run:

```bash
php artisan config:clear
php artisan cache:clear
```

### Step 4: I-verify ang redirect URI

Para makita kung ano ang ginagamit na redirect URI, i-check ang logs:

```bash
# Windows (PowerShell)
Get-Content storage\logs\laravel.log -Tail 50

# O buksan ang file:
# storage\logs\laravel.log
```

Hanapin ang entry na:
```
Google OAuth redirect: {"redirect_uri":"http://127.0.0.1:8000/auth/google/callback",...}
```

**Dapat match ito sa Google Cloud Console!**

### Step 5: Testing

1. I-clear ang browser cache
2. Subukan ulit ang Google Sign-In
3. Kung may error pa, i-check ang Laravel logs

## Common Issues at Solutions

### Issue: redirect_uri_mismatch pa rin
**Solution:**
- Siguraduhing EXACTLY match ang redirect URI sa Google Cloud Console
- Maghintay ng 1-2 minutes pagkatapos i-update ang Google Cloud Console
- I-clear ang browser cache at cookies
- Subukan sa incognito/private browsing mode

### Issue: "Configuration missing"
**Solution:**
- I-check kung may `GOOGLE_CLIENT_ID` at `GOOGLE_CLIENT_SECRET` sa `.env`
- I-run `php artisan config:clear`

### Issue: Gumagana sa local pero hindi sa production
**Solution:**
- Siguraduhing may production URL sa Google Cloud Console
- I-check kung ang `APP_URL` sa production `.env` ay match sa domain
- I-verify kung gumagamit ng HTTPS sa production (dapat match sa Google Console)

## Quick Checklist

- [ ] May `GOOGLE_CLIENT_ID` sa `.env`
- [ ] May `GOOGLE_CLIENT_SECRET` sa `.env`
- [ ] May `GOOGLE_REDIRECT_URI` sa `.env` (o `APP_URL` ay naka-set)
- [ ] Naka-add ang redirect URI sa Google Cloud Console
- [ ] EXACTLY match ang redirect URI (walang trailing slash, kasama ang protocol at port)
- [ ] Na-run ang `php artisan config:clear`
- [ ] Na-clear ang browser cache

## Need Help?

Kung may problema pa rin:
1. I-check ang `storage/logs/laravel.log` para sa detailed error messages
2. I-verify ang Google OAuth credentials
3. I-check kung properly configured ang OAuth consent screen
4. I-verify na enabled ang OAuth client sa Google Cloud Console

