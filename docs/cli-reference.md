# NectarPress WP-CLI Reference

> All commands require WP-CLI 2.8+ and `wp nectarpress` as the base namespace.
> Commands are safe to run in production — none modify data without `--yes` or explicit confirmation.

---

## Table of Contents

- [`wp nectarpress setup`](#wp-nectarpress-setup)
- [`wp nectarpress license`](#wp-nectarpress-license)
- [`wp nectarpress demo`](#wp-nectarpress-demo)
- [`wp nectarpress update`](#wp-nectarpress-update)
- [`wp nectarpress doctor`](#wp-nectarpress-doctor)

---

## `wp nectarpress setup`

Show setup wizard status or reset it.

```
wp nectarpress setup [--reset]
```

**Options:**

| Flag | Description |
|------|-------------|
| `--reset` | Clears wizard completion flag; wizard reruns on next admin login |

**Examples:**

```bash
# Show current wizard state
wp nectarpress setup

# Reset wizard so it runs again (useful after a fresh demo install)
wp nectarpress setup --reset
```

**Output:**
```
Wizard completed: 2026-05-22T10:30:00+05:45

Available commands:
  wp nectarpress setup --reset     Reset wizard to run again
  wp nectarpress license status    Check license state
  wp nectarpress demo list         See available demo packs
  wp nectarpress doctor            Run full health check
```

---

## `wp nectarpress license`

Manage the NectarPress license key.

### `wp nectarpress license status`

```
wp nectarpress license status [--format=<format>]
```

Shows current license state, plan, expiry, and feature list.

```bash
wp nectarpress license status
wp nectarpress license status --format=json
```

### `wp nectarpress license activate`

```
wp nectarpress license activate <key> [--domain=<domain>]
```

Activates a license key against the license server. On success, fetches and stores the signed JWT.

```bash
wp nectarpress license activate NP-XXXX-XXXX-XXXX-XXXX
```

### `wp nectarpress license deactivate`

```
wp nectarpress license deactivate [--yes]
```

De-binds the current domain from the license server. The key becomes available to activate on another domain.

```bash
wp nectarpress license deactivate --yes
```

---

## `wp nectarpress demo`

Manage demo content packs.

### `wp nectarpress demo list`

```
wp nectarpress demo list [--format=<format>]
```

Lists all available demo packs with their plan requirements and content counts.

```bash
wp nectarpress demo list
wp nectarpress demo list --format=json
```

**Output:**
```
+------------------+--------------------------------+----------+-------+-------+
| slug             | name                           | min_plan | posts | users |
+------------------+--------------------------------+----------+-------+-------+
| solo-blog        | Solo Blogger                   | starter  | 20    | 3     |
| sme-portal       | SME News Portal                | pro      | 100   | 5     |
| enterprise-daily | Enterprise Daily / राष्ट्रिय  | business | 500   | 10    |
+------------------+--------------------------------+----------+-------+-------+
```

### `wp nectarpress demo preview`

```
wp nectarpress demo preview <slug> [--format=<format>]
```

Shows exactly what a pack will create before importing.

```bash
wp nectarpress demo preview enterprise-daily
```

### `wp nectarpress demo import`

```
wp nectarpress demo import <slug> [--force] [--yes]
```

Imports a demo pack. Asks for confirmation unless `--yes` is passed. The `--force` flag re-imports even if the pack was already imported.

```bash
# Interactive (confirms before importing)
wp nectarpress demo import sme-portal

# Non-interactive (CI/automation)
wp nectarpress demo import enterprise-daily --yes

# Force re-import
wp nectarpress demo import solo-blog --force --yes
```

**On success:**
```
Success: Pack 'enterprise-daily' imported. Rollback token: 7f3a9c2b1e4d8f5a
  Created 20 categories
  Created 10 users
  Imported 5 media items
  Created 500 posts
  Applied 1,250 bylines
  Created 3 ePaper editions
  Created 5 ad zones
  Created 1 poll
```

### `wp nectarpress demo rollback`

```
wp nectarpress demo rollback <token> [--force] [--yes]
```

Deletes all content from a previous import, identified by its rollback token. Only works within 24 hours of import (use `--force` to override the time window).

**Safety guarantee:** Only items tagged with `_nectarpress_demo_import_id = <token>` are deleted. Customer-authored content is NEVER touched.

```bash
wp nectarpress demo rollback 7f3a9c2b1e4d8f5a --yes
wp nectarpress demo rollback 7f3a9c2b1e4d8f5a --force --yes  # after 24h window
```

### `wp nectarpress demo list-imports`

```
wp nectarpress demo list-imports [--format=<format>]
```

Lists all previous demo imports with their rollback availability status.

```bash
wp nectarpress demo list-imports
wp nectarpress demo list-imports --format=json
```

---

## `wp nectarpress update`

Manage theme updates with atomic swap and rollback.

### `wp nectarpress update check`

```
wp nectarpress update check [--format=<format>]
```

Checks for available theme updates without downloading anything.

```bash
wp nectarpress update check
wp nectarpress update check --format=json
```

### `wp nectarpress update apply`

```
wp nectarpress update apply [<version>] [--yes]
```

Downloads and atomically applies a theme update. Creates a rollback point automatically.

```bash
wp nectarpress update apply
wp nectarpress update apply 2.8.0 --yes
```

### `wp nectarpress update rollback`

```
wp nectarpress update rollback [<version>]
```

Rolls back to a previous theme version within the 7-day retention window.

```bash
wp nectarpress update rollback
wp nectarpress update rollback 2.6.0
```

### `wp nectarpress update list`

```
wp nectarpress update list [--format=<format>]
```

Lists available rollback versions stored locally.

---

## `wp nectarpress doctor`

Run a comprehensive site health check.

```
wp nectarpress doctor [--fix] [--format=<format>]
```

**Options:**

| Flag | Description |
|------|-------------|
| `--fix` | Auto-remediate fixable issues (schedule cron, set filesystem perms) |
| `--format=table\|json\|csv` | Output format. Default: table |

**Checks performed:**

| # | Check | Auto-fix |
|---|-------|----------|
| 1 | WordPress version (≥ 6.2) | — |
| 2 | PHP version (≥ 7.4, recommends 8.1+) | — |
| 3 | Theme version + update available | — |
| 4 | License status (active/expired/eval) | — |
| 5 | File integrity (modified files since baseline) | — |
| 6 | WP-Cron scheduled correctly | Yes |
| 7 | `wp-content/` writeable | Yes (chmod) |
| 8 | Disk space (warns <500 MB) | — |
| 9 | PHP memory limit (warns <128 MB) | — |
| 10 | MySQL version (≥ 5.7) | — |
| 11 | Khalti gateway reachability | — |
| 12 | Email delivery test | — |
| 13 | HTTPS enforced | — |
| 14 | CSP header present | — |
| 15 | Required PHP extensions | — |

**Example output:**
```
wp nectarpress doctor

+---+----------------------------------+--------+-------------------------------------------+
| # | Check                            | Status | Message                                   |
+---+----------------------------------+--------+-------------------------------------------+
| 1 | WordPress version                | PASS   | 6.7.1                                     |
| 2 | PHP version                      | PASS   | 8.2.18                                    |
| 3 | Theme version                    | PASS   | 2.6.0 (latest)                            |
| 4 | License                          | PASS   | Active — Business (expires 2027-05-21)    |
| 5 | File integrity                   | PASS   | 1,247 files verified, 0 modified          |
| 6 | WP-Cron                          | PASS   | license_verify next in 18h                |
| 7 | Filesystem                       | PASS   | wp-content/ writeable                     |
| 8 | Disk space                       | WARN   | 420 MB free — consider cleaning up        |
| 9 | PHP memory                       | PASS   | 256M                                      |
|10 | MySQL                            | PASS   | 8.0.35                                    |
|11 | Khalti gateway                   | PASS   | Reachable (245ms)                         |
|12 | Email                            | PASS   | Test email delivered                      |
|13 | HTTPS                            | PASS   | Valid certificate, expires 2027-01-01     |
|14 | CSP header                       | PASS   | Content-Security-Policy present           |
|15 | PHP extensions                   | PASS   | All required extensions loaded            |
+---+----------------------------------+--------+-------------------------------------------+
1 warning found. Run with --fix to auto-remediate fixable issues.
```

**JSON output (for automation):**
```bash
wp nectarpress doctor --format=json | jq '.[] | select(.status=="FAIL")'
```

**Safe to paste into support tickets** — no license keys, passwords, or secrets appear in output.

---

## Automation Recipes

### Nightly health check via cron

```bash
# /etc/cron.d/nectarpress-health
30 2 * * * www-data cd /var/www/html && wp nectarpress doctor --format=json --allow-root > /var/log/np-health.json 2>&1
```

### Demo deploy for staging environment

```bash
#!/bin/bash
wp nectarpress setup --reset
wp nectarpress license activate "$NP_LICENSE_KEY" --allow-root
wp nectarpress demo import enterprise-daily --yes --allow-root
echo "Staging ready."
```

### Watch for integrity failures in CI

```bash
result=$(wp nectarpress doctor --format=json | jq -r '.[] | select(.check=="File integrity") | .status')
if [ "$result" != "PASS" ]; then
  echo "INTEGRITY FAILURE" && exit 1
fi
```
