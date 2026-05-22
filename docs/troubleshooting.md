# NectarPress Troubleshooting Guide

> Before contacting support, run `wp nectarpress doctor` and include the output in your email. This resolves ~60% of issues before a reply is needed.

---

## Quick Diagnostics

```bash
# Full health check (safe, no data modified)
wp nectarpress doctor

# JSON output for automated parsing
wp nectarpress doctor --format=json

# License state
wp nectarpress license status

# Wizard state
wp nectarpress setup
```

---

## Common Issues

### Theme options not saving

**Symptom:** Changes in Theme Options don't persist after clicking Save.

**Causes & fixes:**

1. **Nonce expired** — stay on the options page for <12 hours before saving
2. **Caching plugin** — add `/wp-admin/` to the "Never Cache" list in your caching plugin
3. **PHP memory limit** — check `wp nectarpress doctor` for memory warnings; increase `memory_limit` to 256M
4. **`WP_DEBUG` active with notices** — notices can corrupt JSON responses; set `WP_DEBUG_DISPLAY = false`

---

### License activation fails

**Symptom:** "Activation failed" or "Domain already active" error during wizard or license page.

**Fixes:**

1. **Check for extra spaces** in the key — copy-paste can sometimes include invisible characters
2. **Domain mismatch** — the key activates against the exact domain you're on. `www.example.com` and `example.com` are different domains. Use whichever matches your WordPress `siteurl`.
3. **Already activated** — the key may be active on another domain. Log in to [nectardigit.com/account](https://nectardigit.com/account) and deactivate the other domain first, or contact support if the domain no longer exists.
4. **Firewall blocking outbound connections** — your server may be blocking connections to `license.nectardigit.com`. Ask your host to whitelist outbound HTTPS to that domain.
5. **Evaluation expired** — evaluation tokens expire after 14 days. Purchase a license at [nectardigit.com](https://nectardigit.com).

---

### Wizard keeps reappearing

**Symptom:** The setup wizard redirects you to it every time you log in to admin.

**Fixes:**

1. **Complete all required steps** — the wizard only marks itself complete at Step 7 (Done screen)
2. **AJAX blocked** — your security plugin or firewall may be blocking the AJAX calls that save wizard state. Check the browser console for 403 errors on `admin-ajax.php`
3. **Multiple admin accounts** — each admin user has their own wizard state. If another admin hasn't completed the wizard, they'll see it on their login. Force-complete via CLI: `wp nectarpress setup --reset` then complete as that user, or mark complete: `wp option update nectarpress_wizard_completed "$(date -u +%Y-%m-%dT%H:%M:%SZ)"`

---

### Demo content import stuck

**Symptom:** Progress bar stops at N% and nothing happens.

**Fixes:**

1. **PHP timeout** — increase `max_execution_time = 300` in `php.ini`
2. **Memory exhausted** — check PHP error log for `Fatal error: Allowed memory size`; increase to 256M
3. **Use WP-CLI instead** — AJAX imports time out on shared hosting; CLI imports don't:
   ```bash
   wp nectarpress demo import enterprise-daily --yes
   ```
4. **Resume interrupted import** — re-run the import command; idempotency means already-created items are skipped

---

### Khalti payment not completing

**Symptom:** User is redirected back from Khalti but payment shows as pending.

**Root cause:** NectarPress always calls Khalti's `/lookup/` endpoint on return to verify payment server-side. This can fail if:

1. **Khalti credentials are wrong** — verify at NectarPress → Payments → Khalti Settings
2. **Test mode in production** — the test key (`test_public_key_...`) only works against Khalti's sandbox. Switch to live credentials for production
3. **Return URL mismatch** — the URL you registered in Khalti's merchant dashboard must match your WordPress `siteurl`
4. **Firewall blocking Khalti API** — your server must be able to reach `https://khalti.com/api/v2/`

Check the payment log at **NectarPress → Orders → Failed Payments** for the exact error code from Khalti's API response.

---

### Breaking news ticker not showing

**Symptom:** The trending strip at the top is empty or hidden.

**Fixes:**

1. **No recent posts** — the ticker shows posts from the last 3 days by default. Publish a post or change the "days" setting in Theme Options → Tab 2 Breaking News
2. **Ticker disabled** — check Theme Options → Tab 1 General → Show Trending Strip
3. **Header preset** — some minimal presets hide the ticker; check Header Builder settings
4. **Transient cache** — the ticker is cached for 1 hour. Clear: `wp transient delete --all` or `wp cache flush`

---

### File integrity alert (modified files)

**Symptom:** Red admin notice about modified theme files.

**When it's expected:**
- After a theme update (re-baseline after verifying the update is legitimate)
- After manual hotfixes to theme files

**When it's a problem:**
- Unexpected modifications with unknown timestamps
- Modifications during off-hours when no updates were applied

**To rebaseline after a legitimate update:**
```bash
wp nectarpress doctor  # confirm the modifications listed look expected
```
Then: NectarPress → Security → File Integrity → **Rebaseline**

**If you suspect intrusion:** See [security.md — What To Do If You Suspect Compromise](security.md#what-to-do-if-you-suspect-compromise).

---

### Theme update fails / stuck in maintenance mode

**Symptom:** The site shows "Briefly unavailable for scheduled maintenance" after an update attempt.

**Fix:**

1. Delete the `.maintenance` file in your WordPress root: `rm /path/to/public_html/.maintenance`
2. If the update partially applied, use the rollback: `wp nectarpress update rollback`
3. If rollback is unavailable, restore from backup

**Prevention:** Always ensure `wp-content/themes/` is writeable and has at least 50 MB free space before applying updates. `wp nectarpress doctor` warns on low disk space.

---

### Admin dashboard very slow

**Symptom:** WordPress admin loads in 5–10 seconds.

**Diagnosis:**

1. Install Query Monitor plugin temporarily
2. Load the slow admin page with QM active
3. Check the Queries panel for slow queries (>100ms) or high query counts (>50)

**Common culprits:**

- **Autoloaded options table** — `wp nectarpress doctor` warns when the options table has large autoloaded data
- **Missing object cache** — editorial dashboard aggregations without Redis run 3× more queries
- **N+1 in custom homepage sections** — each section widget running individual post queries instead of batched ones

**Quick fix:** Install Redis Object Cache plugin and configure Redis on your server.

---

## Enabling Debug Mode

Add to `wp-config.php` (development only — NEVER on production):

```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);   // logs to /wp-content/debug.log
define('WP_DEBUG_DISPLAY', false); // don't show errors on screen
define('SCRIPT_DEBUG', true);      // loads unminified JS/CSS
define('SAVEQUERIES', true);       // enables query log in Query Monitor
```

NectarPress respects `SCRIPT_DEBUG` and loads unminified versions of its own scripts when it's set to `true`.

---

## Contacting Support

**Email:** support@nectardigit.com

**Include in your email:**
1. Output of `wp nectarpress doctor --format=json`
2. WordPress version (`wp core version`)
3. PHP version (`php --version`)
4. Your NectarPress plan/tier
5. Exact error message or screenshot
6. Steps to reproduce

**Response times:**
- Enterprise: same business day
- Business: next business day
- Professional: 2 business days
- Starter: 3 business days

**Do not include** your full license key, database credentials, or wp-config.php in your email. The doctor output is safe to share.
