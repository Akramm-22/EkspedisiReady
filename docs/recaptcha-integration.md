# Google reCAPTCHA v2 Integration Guide

This guide explains how to properly configure and use the Google reCAPTCHA v2 widget integrated into the customer registration page.

## 1. Create reCAPTCHA Credentials
1. Go to the [Google reCAPTCHA Admin Console](https://www.google.com/recaptcha/admin/create).
2. Sign in with a Google account.
3. Register a new site:
   - **Label**: Enter a name (e.g., `EkspedisiOnline - Registration`).
   - **reCAPTCHA type**: Select **reCAPTCHA v2** -> **"I'm not a robot" Checkbox**.
   - **Domains**: Add your domains. For local development, add `localhost` and `127.0.0.1`.
4. Accept the reCAPTCHA Terms of Service and click **Submit**.
5. You will be provided with a **Site Key** and a **Secret Key**. Keep these safe.

## 2. Configure Environment Variables
Open the `.env` file at the root of your project and add the keys:

```env
RECAPTCHA_SITE_KEY=your_site_key_here
RECAPTCHA_SECRET_KEY=your_secret_key_here
```

## 3. Configuration in Laravel
The values from `.env` are automatically loaded into Laravel's configuration in `config/services.php`:

```php
'recaptcha' => [
    'site_key' => env('RECAPTCHA_SITE_KEY'),
    'secret_key' => env('RECAPTCHA_SECRET_KEY'),
],
```

## 4. Frontend Integration
The registration view includes the reCAPTCHA widget via a script tag. It dynamically loads the `RECAPTCHA_SITE_KEY` from the Laravel configuration.
When the user solves the reCAPTCHA, a hidden field `g-recaptcha-response` is injected into the form submission.

## 5. Backend Validation
In `App\Http\Controllers\Web\Auth\CustomerAuthController`, the incoming request is validated. A custom rule or direct API call is made to `https://www.google.com/recaptcha/api/siteverify` using the Secret Key and the `g-recaptcha-response` token.

If validation fails, the user is redirected back to the registration page with a user-friendly error message indicating that they need to complete the reCAPTCHA.

## 6. Testing Instructions
1. Load the registration page (`/customer/register`).
2. Verify that the "I'm not a robot" checkbox is visible.
3. Try submitting the form *without* checking the box. Ensure the form returns with an error.
4. Check the box, solve any challenges, and submit the form. Ensure the registration succeeds.
