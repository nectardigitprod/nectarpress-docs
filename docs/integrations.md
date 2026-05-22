# NectarPress Integrations Guide

> All integrations are optional. Configure only the ones you use — NectarPress only loads scripts for configured integrations and only emits `preconnect` hints for origins you've actually enabled.

---

## Khalti Payment Gateway

Khalti is Nepal's most widely used digital payment platform. NectarPress uses Khalti for both reader subscriptions (Flow B) and as a payment option for agency customers (Flow A).

### Setup

1. Register a merchant account at [khalti.com](https://khalti.com)
2. Go to your Khalti merchant dashboard → API Keys
3. Copy your **Live Public Key** (starts with `live_public_key_`) and **Live Secret Key**
4. In WordPress: **NectarPress → Payments → Khalti Settings**
5. Paste the keys and save
6. Set your **Return URL** in Khalti's dashboard to exactly: `https://yourdomain.com/np-payment/khalti/return/`

**Critical:** NectarPress always calls Khalti's `/lookup/` endpoint on the return URL to verify payment server-side. Never trust the redirect parameters alone — they can be forged.

### Testing

Use Khalti's sandbox keys (`test_public_key_...`) for development:
- Test eSewa number: `9800000000` (use the Khalti test portal)
- All sandbox transactions show in your Khalti test dashboard

### Troubleshooting

See [troubleshooting.md — Khalti payment not completing](troubleshooting.md#khalti-payment-not-completing).

---

## Bank Deposit (Manual Verification)

For customers who prefer traditional bank transfers.

### Setup

1. **NectarPress → Payments → Bank Deposit Settings**
2. Enter your bank account details:
   - Bank Name
   - Account Number
   - Account Name
   - Bank Address (optional)
3. Upload a QR code for your bank's quick-pay option (optional)
4. Set the **verification deadline**: default 72 hours. Orders auto-cancel after this window if not verified.

### Workflow

1. Customer selects "Bank Deposit" at checkout
2. NectarPress shows payment instructions with your account details
3. Customer transfers money and uploads a **deposit slip screenshot**
4. Admin receives notification at **NectarPress → Orders → Pending Verification**
5. Admin verifies the slip and marks as Paid or Rejected
6. Customer's membership activates immediately on approval

---

## QR Payment (Fonepay / Connect IPS)

For quick QR-based payments common in Nepal.

### Setup

1. **NectarPress → Payments → QR Payment Settings**
2. Upload your Fonepay or Connect IPS merchant QR code image
3. Set the **QR validity period** (default: 15 minutes per transaction)

### Workflow

Similar to bank deposit — customer scans QR, pays, uploads confirmation screenshot, admin verifies.

---

## Google Analytics 4

### Setup

1. Create a GA4 property at [analytics.google.com](https://analytics.google.com)
2. Get your **Measurement ID** (format: `G-XXXXXXXXXX`)
3. **NectarPress → Integrations → Google Analytics** → paste ID → Save

NectarPress loads GA4 lazily after first user interaction. Sessions are attributed correctly because the interaction event fires the analytics load before any navigation.

### Privacy compliance

NectarPress does **not** set GA4 cookies before user consent. If you need a cookie consent banner (required by GDPR), use one of these compatible plugins:
- Cookie Yes
- Complianz
- CookieBot

When using a consent management platform, disable NectarPress's built-in GA4 loading and load GA4 from the CMP instead.

---

## Google Tag Manager

### Setup

1. Create a GTM container at [tagmanager.google.com](https://tagmanager.google.com)
2. Get your **Container ID** (format: `GTM-XXXXXXX`)
3. **NectarPress → Integrations → Google Tag Manager** → paste ID → Save

**Note:** If both GTM and GA4 IDs are set, only GTM loads — GA4 should be configured inside GTM as a tag.

---

## Facebook Pixel

### Setup

1. Get your **Pixel ID** from [business.facebook.com](https://business.facebook.com) → Events Manager
2. **NectarPress → Integrations → Facebook Pixel** → paste 15-digit Pixel ID → Save

NectarPress fires `PageView` on every page. For purchase tracking (when a subscription activates), the `Purchase` event is fired automatically with subscription plan value.

---

## Disqus Comments

### Setup

1. Create a Disqus account and site at [disqus.com](https://disqus.com)
2. Get your **shortname** from Disqus → Site Settings → General
3. **NectarPress → Integrations → Disqus** → enter shortname → Save

This replaces WordPress's native comments on all posts. The native comment template is preserved as a fallback if Disqus fails to load.

---

## Mailchimp Newsletter

### Setup

1. Generate a **Mailchimp API key** at [mailchimp.com](https://mailchimp.com) → Account → Extras → API Keys
2. Get your **List/Audience ID** from Mailchimp → Audience → Settings → Audience name and defaults
3. **NectarPress → Integrations → Mailchimp** → paste API key + List ID → Save

### Newsletter widget

After setup, a "Subscribe to Our Newsletter" widget is available in Appearance → Widgets. It submits email addresses directly to your Mailchimp audience via the API (no redirect required).

---

## Push Notifications (FCM / OneSignal)

NectarPress can send push notifications when breaking news is published.

### FCM (Firebase Cloud Messaging) Setup

1. Create a Firebase project at [firebase.google.com](https://firebase.google.com)
2. Enable Cloud Messaging
3. Get your **Server Key** from Firebase console → Project Settings → Cloud Messaging
4. **NectarPress → Integrations → Push Notifications → FCM** → paste Server Key

FCM integration sends push notifications when a post is published with the "Breaking News" workflow flag.

### OneSignal Setup

1. Create an account at [onesignal.com](https://onesignal.com)
2. Get your **App ID** and **REST API Key**
3. **NectarPress → Integrations → Push Notifications → OneSignal** → paste credentials

---

## SMTP (Transactional Email)

WordPress's default PHP mail is unreliable. Configure an SMTP provider for editorial notifications, password resets, and subscriber emails.

| Provider | Free tier | Nepali IP reputation |
|----------|-----------|---------------------|
| Brevo (formerly Sendinblue) | 300/day | Good |
| SendGrid | 100/day | Good |
| Mailgun | 1,000/month | Good |
| Postmark | 100/month | Excellent |

### Setup via WP Mail SMTP Plugin

NectarPress works with [WP Mail SMTP](https://wordpress.org/plugins/wp-mail-smtp/) — configure your provider there. NectarPress uses WordPress's `wp_mail()` function for all emails, so it benefits automatically from WP Mail SMTP.

---

## Newsroom Webhook (Slack / Viber / Telegram)

NectarPress can POST JSON to any webhook URL when editorial events occur (story approved, workflow override, correction filed).

### Setup

1. **NectarPress → Newsroom → Notifications → Webhook URL** → paste your webhook URL
2. Test: click "Send Test Notification"

**Payload format:**
```json
{
  "event": "workflow_approved",
  "post_id": 1234,
  "post_title": "New bill passed in parliament",
  "actor": "demo_editor1",
  "from_state": "editor_review",
  "to_state": "approved",
  "timestamp": "2026-05-22T10:30:00+05:45",
  "url": "https://yoursite.com/?p=1234"
}
```

### Slack

Create an Incoming Webhook at your Slack workspace → Apps → Incoming Webhooks. Paste the `https://hooks.slack.com/services/...` URL in NectarPress.

### Telegram

Use a Telegram Bot + `https://api.telegram.org/bot<TOKEN>/sendMessage` with a middleware script to convert the NectarPress JSON payload to Telegram's format. (A lightweight middleware script is available in our GitHub repository under `docs/recipes/telegram-webhook.php`.)
