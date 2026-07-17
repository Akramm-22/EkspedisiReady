# Midtrans Payment Integration Guide

This guide details how Midtrans is integrated into the EkspedisiOnline project using Snap Redirect.

## 1. Create a Midtrans Account
1. Register for a Midtrans account at [https://dashboard.midtrans.com/register](https://dashboard.midtrans.com/register).
2. Log in and navigate to the **Settings > Access Keys** section.
3. You will see keys for the **Sandbox** (Testing) environment. Later, you can switch to **Production**.

## 2. Configure Environment Variables
Copy your Sandbox Server Key and Client Key and add them to your `.env` file:

```env
MIDTRANS_SERVER_KEY=SB-Mid-server-YOUR_SERVER_KEY
MIDTRANS_CLIENT_KEY=SB-Mid-client-YOUR_CLIENT_KEY
MIDTRANS_IS_PRODUCTION=false
```

When moving to production, change the keys to your Production keys and set `MIDTRANS_IS_PRODUCTION=true`.

## 3. Installing the SDK
The Midtrans PHP SDK is used for backend operations. It was installed using:
```bash
composer require midtrans/midtrans-php
```

## 4. Payment Flow & Snap Integration
When a customer decides to pay for a shipment (e.g., via Transfer or E-Wallet), the application hits `PaymentController@store`.

1. The controller sets the Midtrans configuration (Server Key, Is Production).
2. It prepares the transaction details (`transaction_details`), including `order_id` and `gross_amount`.
3. It includes `customer_details` (name, email, phone).
4. It calls `\Midtrans\Snap::getSnapToken($params)` or `\Midtrans\Snap::createTransaction($params)->redirect_url`.
5. The user is redirected to the Midtrans hosted payment page.

## 5. Webhook / Notification Handling
After a user completes or fails a payment, Midtrans will asynchronously send an HTTP POST request to our Webhook endpoint.

1. **Configure Webhook URL in Midtrans**: Go to Midtrans Dashboard > Settings > Configuration. Set the "Payment Notification URL" to your live domain, e.g., `https://yourdomain.com/api/v1/payments/midtrans-notification`.
2. **Backend Route**: `POST /api/v1/payments/midtrans-notification` receives this request.
3. **Security Validation**: The controller calculates `hash("sha512", $orderId . $statusCode . $grossAmount . $serverKey)` and verifies it matches the `signature_key` sent by Midtrans.
4. **Status Update**: Based on `transaction_status`, the backend updates the local payment status to `settlement` (Paid), `pending`, `cancel`, or `expire`.

## 6. Payment States
- **Pending**: Payment created but not yet completed.
- **Settlement (Paid)**: Payment is verified and funds have been received.
- **Cancel / Expire**: Payment was cancelled by the user or expired after the time limit.
- **Deny / Failed**: Payment failed fraud detection or was declined.

## 7. Testing in Sandbox
1. Go through the shipment creation flow and choose Transfer/E-Wallet.
2. Follow the redirect to the Midtrans Sandbox payment page.
3. Use [Midtrans Sandbox Test Credentials](https://docs.midtrans.com/docs/testing-payments) to simulate successful or failed payments.
4. Use a tool like Ngrok or the Midtrans simulator to fire a mock webhook request to your local `/api/v1/payments/midtrans-notification` to verify state updates.
