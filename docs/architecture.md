# NectarPress Architecture Reference

> Internal reference for Nectar Digit engineers and contributors. Covers module structure, data flows, custom database tables, and API surfaces.

---

## Module Map

```
nectarpress/
├── functions.php              33-line bootloader: constants + require bootstrap.php
├── includes/
│   ├── bootstrap.php          Central module loader (all require_once in one place)
│   ├── helpers.php            Pure utilities (zero side-effects, loads first)
│   │
│   ├── license/               License engine (Sprint 2)
│   │   ├── client.php         NectarPress_License_Client — JWT verify, HMAC signing
│   │   ├── feature-gate.php   nectarpress_has_feature() + NECTARPRESS_PLAN_FEATURES
│   │   ├── cron.php           03:15 NPT daily heartbeat
│   │   ├── update-checker.php Atomic swap updater + WP update UI hooks
│   │   └── ui.php             Admin license tab
│   │
│   ├── security/              Security hardening (Sprint 3)
│   │   ├── integrity-monitor.php  NectarPress_Integrity_Monitor
│   │   ├── alerts.php             NectarPress_Alerts — email/dashboard/beacon
│   │   ├── csp-builder.php        NectarPress_CSP_Builder
│   │   ├── rate-limit.php         Token-bucket rate limiter
│   │   └── upload-sanitiser.php   SVG/PDF/JPEG sanitizer + EXIF strip
│   │
│   ├── payments/              Payment gateways (Sprint 2)
│   │   ├── currency.php           NPR/paisa helpers
│   │   ├── tax-calculator.php     13% VAT calculator
│   │   ├── orders-cpt.php         np_lic_order + np_sub_order CPTs
│   │   ├── webhook-handler.php    Payment callback handler
│   │   └── gateways/
│   │       ├── khalti.php         Khalti v2 (/lookup/ always called on return)
│   │       ├── bank-deposit.php   Manual verification, 72h auto-cancel
│   │       └── qr-payment.php     QR screenshot upload
│   │
│   ├── subscription/          Reader subscription engine (Sprint 2)
│   │   ├── plans-cpt.php          np_sub_plan CPT
│   │   ├── memberships.php        State machine + np_premium_reader role
│   │   └── paywall.php            Article count / scroll / time triggers
│   │
│   ├── newsroom/              Editorial workflow (Sprint 4)
│   │   ├── reporter-roles.php     5 roles, 11 caps, beat-based map_meta_cap
│   │   ├── editorial-workflow.php 7-state machine, all transitions
│   │   ├── byline-multi.php       N reporters per post, Schema.org author array
│   │   ├── corrections.php        4 types, retraction banner, CorrectionComment
│   │   ├── reporter-directory.php /reporters/ auto-page, region seeder
│   │   ├── revisions-audit.php    wp_nectarpress_audit custom table
│   │   ├── notifications.php      Throttled email + webhook
│   │   ├── editorial-dashboard.php 3 dashboards
│   │   └── plagiarism-flag.php    Pre-publish hook (no provider hardcoded)
│   │
│   ├── onboarding/            Setup wizard + demo content (Sprint 5)
│   │   ├── wizard.php             NectarPress_Setup_Wizard (7 steps, AJAX)
│   │   ├── wizard-steps.php       Step render + save callbacks
│   │   ├── importer.php           NectarPress_Demo_Importer (idempotent + rollback)
│   │   └── checklist.php          Post-wizard dashboard widget
│   │
│   ├── a11y/                  Accessibility (Sprint 6)
│   │   └── a11y-frontend.php      Skip link, lang attrs, live regions, alt warnings
│   │
│   ├── performance/           Performance optimization (Sprint 6)
│   │   ├── perf-assets.php        Font preloads, script defer, resource hints, CLS
│   │   ├── perf-cache.php         Object cache wrapper + DB indexes + cleanup cron
│   │   └── i18n-helpers.php       Date/number/currency formatters, time-ago
│   │
│   └── cli/                   WP-CLI commands (Sprint 3 + Sprint 5)
│       ├── command-update.php     wp nectarpress update
│       ├── command-setup.php      wp nectarpress setup
│       ├── command-demo.php       wp nectarpress demo
│       └── command-doctor.php     wp nectarpress doctor
│
├── assets/
│   ├── css/
│   │   ├── a11y.css               Skip link, focus indicators, live regions
│   │   ├── fonts.css              Self-hosted Mukta + Noto Devanagari declarations
│   │   ├── style-rtl.css          RTL overrides (rtlcss + manual)
│   │   ├── wizard.css             Setup wizard admin styles
│   │   ├── modern.css             Contemporary UI layer (Sprint 1)
│   │   ├── style-dark-mode.css    Dark mode overrides
│   │   └── print.css              Print stylesheet
│   ├── js/
│   │   ├── a11y.js                Focus trap, ticker live region, tab widget
│   │   ├── analytics-loader.js    Lazy GTM/GA4/Pixel loader
│   │   └── wizard.js              Setup wizard navigation + progress
│   ├── fonts/                     Self-hosted WOFF2 files (Mukta, Noto, Kalimati)
│   └── demo-packs/                JSON demo content manifests
│       ├── solo-blog.json
│       ├── sme-portal.json
│       └── enterprise-daily.json
│
├── languages/                 i18n POT + PO files
│   ├── nectarpress.pot
│   ├── ne_NP.po / .mo
│   └── ne_IN.po / .mo
│
├── tests/
│   ├── Unit/                  PHPUnit unit tests (no WP required)
│   └── bootstrap.php
│
└── docs/                      Documentation (you are here)
```

---

## Data Flow: License Verification

```
cron_04_15 NPT
    └── NectarPress_License_Client::verify()
            ├── Reads stored JWT from wp_options
            ├── POSTs HMAC-signed payload to license.nectardigit.com/api/verify
            │     Payload: { site_url, wp_version, php_version, theme_version,
            │                license_key_hash, last_verified_at }
            │     Response: { status, plan, features[], expires_at, new_jwt? }
            ├── Verifies response signature (ES256 / ECDSA P-256)
            ├── Stores new JWT + expiry in wp_options (autoload=no)
            └── Fires nectarpress/license/activated or nectarpress/license/verification_failed
```

---

## Data Flow: Demo Import

```
NectarPress_Demo_Importer::import($slug, $options)
    ├── Validates plan access (nectarpress_has_feature)
    ├── Loads JSON manifest from assets/demo-packs/<slug>.json
    ├── Generates import token (bin2hex(random_bytes(12)))
    ├── Import pipeline (order matters — dependencies first):
    │   1. theme_options   → update_option() for each key
    │   2. categories      → wp_insert_term() (idempotent via get_term_by)
    │   3. tags            → wp_insert_term()
    │   4. users           → wp_insert_user() (idempotent via get_user_by)
    │   5. media           → wp_insert_attachment() (placeholder)
    │   6. posts           → wp_insert_post() + update_post_meta(_nectarpress_demo_import_id)
    │   7. bylines         → update_post_meta(nr_bylines)
    │   8. ePaper CPTs     → wp_insert_post(post_type=np_epaper)
    │   9. ads             → wp_insert_post(post_type=np_ad)
    │  10. polls           → wp_insert_post(post_type=np_poll)
    ├── Stores external_id → wp_id map in nectarpress_demo_imports option
    └── Fires nectarpress/demo/import_complete
```

---

## Data Flow: Editorial Post Publish

```
Editor clicks "Approve for Publish" in Gutenberg
    └── AJAX → NectarPress_NR_Workflow::handle_transition_ajax()
            ├── check_ajax_referer('np_nr_transition_nonce')
            ├── Validates current_user_can(nr_approve_for_publish, $post_id)
            │     → map_meta_cap checks beat assignment if user is np_editor
            ├── NectarPress_NR_Workflow::transition($post_id, 'approved', $actor_id, $comment)
            │     ├── Updates nr_workflow_state post meta
            │     ├── Writes to wp_nectarpress_audit table
            │     ├── Fires nectarpress/nr/workflow_transition action
            │     └── Triggers NectarPress_NR_Notifications::notify_transition()
            └── Returns JSON { success, new_state, available_transitions[] }
```

---

## Custom Database Tables

### `wp_nectarpress_audit`

Created by `NectarPress_NR_Audit::maybe_create_table()` via `dbDelta`.

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint(20) unsigned | Auto-increment primary key |
| `post_id` | bigint(20) | Post being audited |
| `event_type` | varchar(50) | e.g., `workflow_transition`, `byline_change` |
| `from_state` | varchar(30) | Previous state |
| `to_state` | varchar(30) | New state |
| `actor_id` | bigint(20) | User who triggered the event |
| `comment_text` | text | Optional comment |
| `event_payload` | longtext (JSON) | Full event data |
| `ip_address` | varchar(45) | IPv4 or IPv6 |
| `created_at` | datetime | UTC timestamp |

Indexed on: `(post_id, created_at)`, `(actor_id, created_at)`, `(event_type, created_at)`.

---

## API Surface

### License Server (`license.nectardigit.com/api/`)

All endpoints require HMAC-SHA256 signature in `X-NP-Signature` header.

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/activate` | Activate key on domain |
| POST | `/verify` | Daily heartbeat verification |
| POST | `/deactivate` | Remove domain binding |
| GET | `/update-info` | Check for theme updates |

### Internal AJAX (`/wp-admin/admin-ajax.php`)

| Action | Auth | Description |
|--------|------|-------------|
| `np_wizard_step` | nonce | Advance/save wizard step |
| `np_wizard_import_status` | nonce | Poll demo import progress |
| `np_nr_transition` | nonce + cap check | Workflow state transition |
| `np_nr_byline_save` | nonce + cap check | Save multi-byline data |
| `np_license_activate` | nonce + admin | Activate license key |
| `np_license_deactivate` | nonce + admin | Deactivate license |

---

## Security Architecture

- **License keys**: stored encrypted via `nectarpress_encrypt()` (AES-256-CBC, key from `wp_salt('auth')`)
- **JWT verification**: ECDSA P-256 (ES256) — public key embedded in theme, fingerprint checked against `NECTARPRESS_PUBKEY_FINGERPRINT` constant to prevent on-disk key swap
- **Rate limiting**: token-bucket per IP — anonymous page views never throttled
- **File integrity**: SHA-256 hash baseline signed with Ed25519; verification runs in 100-file chunks during low-traffic window (02:00–05:00 NPT)
