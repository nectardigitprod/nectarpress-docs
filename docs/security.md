# NectarPress Security Guide

> NectarPress implements security-in-depth. This page explains what the theme does automatically, what you must do as the site owner, and what to do if you suspect a compromise.

---

## What NectarPress Does Automatically

### File Integrity Monitor

Every night (02:00–05:00 NST), NectarPress scans its own theme files and compares SHA-256 hashes against a cryptographically signed baseline. If any file is modified:

1. An admin alert appears immediately in your WordPress dashboard
2. An email is sent to the admin address
3. A tamper report is sent to `license.nectardigit.com` containing only: site URL, changed file paths + hashes, theme version. **No article content, reporter names, or reader data is included.**

To rebaseline after a legitimate update: **NectarPress → Security → Rebaseline**.

### Content Security Policy

NectarPress generates and enforces a Content Security Policy header. The default policy:
- Blocks inline scripts (except the dark-mode FOUC prevention snippet and the WP nonce-secured inline blocks)
- Blocks external script sources except Google Analytics, GTM, Facebook Pixel, and Khalti (when configured)
- Runs in **report-only mode for 7 days** on a new install so you can review violations before enforcing

Configure at: **Theme Options → Tab 19 Security → CSP Settings**

### Rate Limiting

NectarPress rate-limits the following endpoints:
- Login page: 5 attempts per 10 minutes per IP
- Payment webhooks: 100 requests per minute per IP
- License verification: 10 per hour per IP
- Search: 20 per minute per IP (anonymous)

**Anonymous page views are never throttled.**

### Upload Sanitizer

All uploaded files are processed by NectarPress's upload sanitizer:
- **SVG files**: Stripped of `<script>`, `<foreignObject>`, event handlers, and remote resource references
- **PDF files**: PHP code injection check, metadata strip
- **JPEG/PNG**: EXIF data stripped (no GPS coordinates, device info, author metadata in reader-facing uploads)

### Rate-Limited WP-REST API

NectarPress registers a custom REST namespace (`/wp-json/nectarpress/v1/`) for its own AJAX operations. All endpoints require a nonce + are rate-limited per the table above.

---

## What You Must Do

### 1. HTTPS (required)

NectarPress requires HTTPS. The license server will not activate a key on an HTTP domain. Without HTTPS:
- Khalti payments don't work
- CSP headers include `upgrade-insecure-requests` but are ineffective
- Subscriber passwords are sent in plaintext

Use Let's Encrypt (free) or any commercial certificate. Cloudflare's free SSL is acceptable for most sites.

### 2. Strong passwords

All reporter, editor, and admin accounts should have:
- Minimum 16-character passwords (WordPress enforces this with the `password_strength_requirement` filter if you activate NectarPress's optional hardening)
- No shared accounts — each journalist has their own login
- Password manager recommended: Bitwarden, 1Password, or KeePass

### 3. Regular backups

NectarPress does not manage backups. You must:
- Daily database backups (UpdraftPlus, ManageWP, cPanel Backup)
- Weekly full-site backups (database + uploads)
- Off-site storage (S3, Google Drive, or Cloudflare R2)
- Test restoration at least quarterly

### 4. Host hardening

- Keep PHP updated (8.1+ strongly recommended; 7.4 reaches EOL and loses security patches)
- Keep WordPress core updated
- Disable XML-RPC if you don't use it (`add_filter('xmlrpc_enabled', '__return_false')`)
- Use a Web Application Firewall (Cloudflare Free tier includes one)

### 5. wp-config.php hardening

```php
// Move wp-config.php one level above document root if possible
// Force HTTPS
define('FORCE_SSL_ADMIN', true);
// Disable file editing in admin
define('DISALLOW_FILE_EDIT', true);
// Disable plugin/theme installation
define('DISALLOW_FILE_MODS', true);
// Limit post revisions (optional — reduce DB bloat)
define('WP_POST_REVISIONS', 10);
```

---

## Common Attack Vectors and How We Handle Them

| Attack | Protection |
|--------|-----------|
| Brute-force login | Rate limiting (5/10min per IP), optional 2FA |
| Malicious file upload | Upload sanitizer on all media uploads |
| XSS via comment/post | `wp_kses_post()` on all output; CSP blocks inline scripts |
| SQL injection | All queries use `$wpdb->prepare()` or WP_Query |
| CSRF | All AJAX actions check `check_ajax_referer()` with nonces |
| Theme file modification | File integrity monitor + alerts |
| License key theft | Keys are HMAC-signed; hashed before transmission |
| Open redirect | All redirects use `wp_safe_redirect()` |
| SSRF via webhook | Outgoing HTTP uses `wp_remote_get()` with `WP_Http_Block_IP` filter |

---

## What To Do If You Suspect Compromise

1. **Run the doctor command immediately:**
   ```bash
   wp nectarpress doctor --format=json > /tmp/health-$(date +%Y%m%d).json
   ```

2. **Check the file integrity report** in NectarPress → Security → Integrity Log. Modified files are listed with timestamps.

3. **Check the audit trail** at NectarPress → Newsroom → Audit Log for any unusual workflow actions (unexpected publishes, user role changes).

4. **Change all admin and editor passwords immediately.**

5. **Contact Nectar Digit security team:** `security@nectardigit.com`
   - Include the doctor output (JSON)
   - Include the integrity log export
   - Include your site URL and license key last 4 characters
   - We respond to Enterprise customers within 4 hours, others within 24 hours

6. **Do not delete modified files** until you've taken a snapshot — the modification timestamps and hashes are evidence.

7. **Restore from your last known-good backup** to a staging server, compare against the compromised site to understand the scope.

---

## Security Disclosure

Found a vulnerability in NectarPress? Please report responsibly:

**Email:** security@nectardigit.com  
**PGP key:** Available at [nectardigit.com/pgp](https://nectardigit.com/pgp)  
**Response SLA:** Initial response within 72 hours, fix within 30 days for critical issues  
**Scope:** Theme PHP/JS code, license server API, payment gateway integrations  
**Out of scope:** Third-party plugins, WordPress core, hosting infrastructure

We do not have a public bug bounty program but we credit researchers in the changelog and offer a lifetime Professional license for critical findings.
