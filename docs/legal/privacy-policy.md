# Privacy Policy — NectarPress & nectardigit.com

**Nectar Digit Pvt. Ltd.**  
Kathmandu, Nepal  
**Effective date:** 2026-05-22  
**Last reviewed:** 2026-05-22

> This policy covers two related but distinct contexts:
> 1. **nectardigit.com and its sub-domains** (storefront, customer account, docs, demo site)
> 2. **The NectarPress theme installed on your WordPress site** (what the theme sends to Nectar Digit from your server)

---

## Part A — nectardigit.com Website

### What we collect

When you visit nectardigit.com:

| Data | Why | Retention |
|------|-----|-----------|
| IP address | Security, fraud prevention | 90 days in server logs |
| Browser + OS (user-agent string) | Compatibility analytics | 12 months (aggregated) |
| Pages visited, referrer | Understanding which docs are useful | 12 months (aggregated) |
| Email address (if you contact us or subscribe) | Replies, announcements | Until you unsubscribe or request deletion |
| Payment data (name, email, phone, country, PAN/VAT) | Order processing, invoice generation | 7 years (Nepal tax law) |

### What we do NOT collect

- We do not profile individual visitors
- We do not sell or share your data with advertisers
- We do not use third-party advertising cookies
- We do not track you across other websites

### Cookies

nectardigit.com uses:
- **Session cookie** (authentication, your account session) — deleted on logout
- **Preference cookie** (language, theme) — 1 year
- **Analytics cookie** (first-party analytics via Plausible/Fathom, no cross-site tracking)

We do NOT use Google Analytics cookies on our own storefront.

---

## Part B — NectarPress Theme (installed on your server)

### What the theme sends to Nectar Digit

The NectarPress theme contacts `license.nectardigit.com` **once per day** for license verification.

**Data transmitted in every daily verify:**

| Field | Example |
|-------|---------|
| Site URL (domain only) | `nepaliportal.com` |
| WordPress version | `6.7.1` |
| PHP version | `8.1.28` |
| NectarPress version | `1.0.0` |
| License key (HMAC-hashed, not plaintext) | `sha256:a3f2b...` |
| Last verified timestamp | `2026-05-22T03:15:00+05:45` |

**Data NOT transmitted:**

- No reader/visitor data
- No post content, titles, or metadata
- No user accounts, passwords, or email addresses
- No analytics about your readers
- No cookies set on your readers' browsers by Nectar Digit

### File integrity tamper reports

When the file integrity monitor detects modified theme files (Business+ plan), it sends a tamper report containing:

- Site URL
- List of changed file paths + SHA-256 hashes
- Theme version

**No article content, reporter names, or reader data is ever included in tamper reports.**

### Demo content import

When you use the demo importer, all data (import tokens, content, media) is stored **exclusively in your own WordPress database on your own server**. Nectar Digit does not receive any demo import data.

### Deactivating data transmission

To stop daily license verification:
1. Go to **NectarPress → License → Deactivate**
2. This immediately de-binds your domain from the license server
3. No further data is transmitted

After deactivation, the theme enters graceful degradation mode (admin features pause after 31 days; your live news site front-end is never broken).

---

## Your Rights

Under Nepal's Individual Privacy Act 2075 (and GDPR principles for international customers):

- **Access:** Request a copy of all data we hold about you
- **Correction:** Request correction of inaccurate data
- **Deletion:** Request deletion of your data (subject to legal retention requirements)
- **Portability:** Receive your data in a machine-readable format
- **Objection:** Object to processing based on legitimate interests

**To exercise any right:** Email info@nectardigit.com with subject line "Privacy Request". We will respond within 14 days.

**To request deletion of all data associated with a domain:** Email info@nectardigit.com with subject "Data Deletion Request" and your site URL. We process within 14 days.

---

## Data Storage & Security

- All data transmitted between your site and license.nectardigit.com is encrypted in transit (TLS 1.3)
- License data stored on Cloudflare R2 + MySQL database (hosted in Singapore/India region)
- Access to production databases is restricted to Nectar Digit founders with hardware 2FA
- Database backups encrypted at rest, 30-day retention, stored on Cloudflare R2

---

## Contact

**Nectar Digit Pvt. Ltd.**  
Email: info@nectardigit.com  
Website: https://nectardigit.com  
Jurisdiction: Kathmandu, Nepal

For security vulnerabilities: security@nectardigit.com
