# NectarPress Onboarding Email Sequence

> All emails sent from `Nectar Digit <hello@nectardigit.com>` with reply-to `support@nectardigit.com`.
> Templates are stored in Mailgun/SendGrid template store. Variable placeholders use `{{double_brace}}` syntax.
> Plain-text versions required for every HTML email (deliverability + accessibility).

---

## Email 0 — License Activation (Day 0, auto-sent on payment)

**Subject:** Your NectarPress license key — {{plan_name}} plan  
**Trigger:** Payment confirmed (any gateway)  
**Delay:** Immediate (< 60 seconds)

### HTML Body

```html
<p>नमस्ते {{customer_name}},</p>

<p>Your NectarPress <strong>{{plan_name}}</strong> license is active. Here is your key:</p>

<div style="background:#f6f3ff;border-left:4px solid #420c6c;padding:16px 20px;font-family:monospace;font-size:18px;letter-spacing:2px;margin:20px 0;">
  {{license_key}}
</div>

<p><strong>How to activate:</strong></p>
<ol>
  <li>Install NectarPress on your WordPress site</li>
  <li>The setup wizard opens automatically on first admin login</li>
  <li>Paste your key on Step 2 (License) — or go to <strong>NectarPress → License</strong></li>
  <li>Click Activate — takes about 3 seconds</li>
</ol>

<p><a href="https://docs.nectardigit.com/nectarpress/onboarding/" style="background:#420c6c;color:#fff;padding:12px 24px;border-radius:4px;text-decoration:none;display:inline-block;">Read the setup guide →</a></p>

<hr>
<p style="font-size:13px;color:#666;">
  Plan: {{plan_name}} | Expiry: {{expiry_date}} | Domain limit: {{domain_count}}<br>
  Manage your license at <a href="https://nectardigit.com/account">nectardigit.com/account</a>
</p>
```

---

## Email 1 — Day 1: How's your first day?

**Subject:** Getting started with NectarPress 🗞️  
**Trigger:** 24 hours after Email 0  
**Delay:** 24h

### Body

```
नमस्ते {{customer_name}},

Yesterday you activated NectarPress — today, let's get your portal live.

The three things most customers do on day one:

1. Complete the setup wizard (5 minutes)
   → https://docs.nectardigit.com/nectarpress/onboarding/

2. Import demo content to see the theme populated
   → wp nectarpress demo import sme-portal --yes

3. Add their logo and site name
   → Appearance → Customizer → Site Identity

If you hit any snags, reply to this email — we read every one.

— The NectarPress team
```

---

## Email 2 — Day 3: Did you publish your first post?

**Subject:** 3 tips for your first week with NectarPress  
**Trigger:** 3 days after Email 0  
**Delay:** 72h

### Body

```
नमस्ते {{customer_name}},

Three things that make the biggest difference in your first week:

1. Add your reporters' profiles
   Each journalist gets their own page at /reporters/<username>/ automatically.
   Go to Users → Add New → set role to "Reporter (NectarPress)" and fill in their bio.

2. Set up your homepage sections
   Appearance → Homepage Builder — drag Breaking Stack, Category Section, and
   the NEPSE widget into place. Save. Done.

3. Enable the trending news ticker
   Theme Options → Tab 2 → Breaking News → Show Trending Strip: Yes.
   It shows the last 3 days' most-viewed stories automatically.

Quick question: have you published your first story yet?
Reply with your portal URL — we'd love to see it.

— NectarPress team
```

---

## Email 3 — Day 7: First-week check-in

**Subject:** One week with NectarPress — how is it going?  
**Trigger:** 7 days after Email 0  
**Delay:** 7 days

### Body

```
नमस्ते {{customer_name}},

You've had NectarPress for a week. This is the point where customers either
feel comfortable or are quietly struggling.

If things are going well — great! Consider:
  → Setting up your Khalti reader subscription (if you're on Business+)
  → Adding co-bylines for multi-reporter stories
  → Booking a 30-minute onboarding call (free, I'll walk you through anything)

If something's not working:
  → Run: wp nectarpress doctor
    (paste the output in a reply and we'll debug together)

Book your free onboarding call:
→ https://nectardigit.com/onboarding-call?license={{license_key_last4}}

— Amrit
  NectarPress, Nectar Digit
```

---

## Email 4 — Day 14: Power features

**Subject:** Features you might not have found yet  
**Trigger:** 14 days after Email 0  
**Delay:** 14 days

### Body

```
नमस्ते {{customer_name}},

Two weeks in! Here are some features customers often discover late:

🔍 WP-CLI doctor
   wp nectarpress doctor
   15 health checks in 5 seconds. Safe to run in production anytime.

📰 ePaper viewer (Business+)
   Post Type → ePaper Editions → upload your PDF cover + file.
   Appears as a widget on your homepage automatically.

🔒 Corrections & retractions
   On any published post → Corrections → add a correction or clarification.
   Shown with a prominent notice, logged in the audit trail.

📊 Reporter directory
   All your reporters are at /reporters/ automatically.
   Readers can filter by district or beat.

Any of these interesting to you? Just reply — happy to do a quick walk-through.

— NectarPress team
```

---

## Email 5 — Day 30: First-month feedback

**Subject:** 30 days of NectarPress — can we ask for feedback?  
**Trigger:** 30 days after Email 0  
**Delay:** 30 days

### Body

```
नमस्ते {{customer_name}},

You've been using NectarPress for 30 days. That makes your opinion worth more
than any beta tester's.

We have two questions:

1. What's working really well?
2. What's frustrating or missing?

Reply to this email with whatever's on your mind. We use real customer feedback
to decide what goes into the next version.

If you're happy with NectarPress, we'd be grateful for a testimonial:
→ https://nectardigit.com/testimonials/submit

— Amrit
  Nectar Digit
```

---

## Email 6 — Day 60: Upgrade nudge (if on Starter/Professional)

**Subject:** What you're missing on {{plan_name}}  
**Trigger:** 60 days after Email 0, only if plan = Starter or Professional  
**Delay:** 60 days  
**Condition:** `plan_slug IN ('starter', 'professional')`

### Body

```
नमस्ते {{customer_name}},

On {{plan_name}}, there are features locked to higher tiers that customers on
your level frequently ask about. Here's the short list:

{{#if plan_starter}}
Professional adds:
• Multi-reporter bylines (credit your photographer + translator separately)
• Corrections & clarifications system
• Reader subscription paywall
• Priority email support
{{/if}}

{{#if plan_professional}}
Business adds:
• Full 7-state editorial workflow (Reporter → Editor → Chief Editor → Publish)
• Audit trail for every editorial decision
• ePaper PDF viewer
• Demo content pack (500-post Enterprise pack)
• 5 simultaneous activated domains
{{/if}}

Upgrade takes 2 minutes. Your existing activation carries over:
→ https://nectardigit.com/account/upgrade?license={{license_key_last4}}

— NectarPress team
```

---

## Email 7 — Day 330: Renewal reminder (35 days before expiry)

**Subject:** Your NectarPress license renews in 35 days  
**Trigger:** 330 days after Email 0 (= 35 days before 365-day expiry)  
**Delay:** 330 days

### Body

```
नमस्ते {{customer_name}},

Your NectarPress {{plan_name}} license expires on {{expiry_date}} — that's
35 days from now.

Renew now to:
• Keep auto-updates flowing (v{{latest_version}} is already available)
• Maintain license server verification (no front-end breakage, but admin features pause)
• Lock in your current renewal price before any upcoming price adjustments

Renew now (Khalti):
→ https://nectardigit.com/account/renew?license={{license_key_last4}}

If you prefer bank deposit, email billing@nectardigit.com with your license
key last 4 characters and we'll send bank details.

— NectarPress team
```

---

## Email 8 — Day 358: Final renewal reminder (7 days before expiry)

**Subject:** FINAL REMINDER: NectarPress expires in 7 days  
**Trigger:** 358 days after Email 0  
**Delay:** 358 days

### Body

```
नमस्ते {{customer_name}},

Your NectarPress license expires in 7 days on {{expiry_date}}.

After expiry:
• Days 1–7:   Admin notice only — everything works
• Days 8–30:  Red admin banner — front-end fully functional
• Day 31+:    Gated admin features disabled — your live news site never breaks

Renew in 2 minutes:
→ https://nectardigit.com/account/renew?license={{license_key_last4}}

Need help? Reply to this email or call +977-XXXX-XXXX (Mon–Fri, 10 AM–6 PM NPT).

— NectarPress team
```

---

## Implementation Notes

- **Mailgun** or **SendGrid** recommended (deliverability in Nepal is better than raw SMTP)
- All templates stored as versioned template IDs in the provider — update without code deploy
- All variables validated server-side before injection (no XSS risk in transactional emails)
- Plain-text fallback required for every HTML email
- Unsubscribe link required in every email except Email 0 (transactional receipt)
- Track opens/clicks per email to identify which ones drive onboarding calls
