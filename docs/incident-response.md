# NectarPress — Incident Response Plan (Internal)

> This document is internal to Nectar Digit. Do not ship in the public theme zip.
> Keep a copy in the team's secure document storage (1Password Vault or equivalent).

## Severity Classification

| Level | Examples | SLA |
|-------|----------|-----|
| **P1 — Critical** | RCE in theme code, license server compromise, key leak, mass data exposure | Patch within 4 hours; notify all customers within 24 hours |
| **P2 — High** | Authentication bypass, privilege escalation, payment gateway manipulation | Patch within 24 hours; notify affected customers within 48 hours |
| **P3 — Medium** | XSS, CSRF, information disclosure | Patch within 7 days; changelog update |
| **P4 — Low** | Best-practice issues, rate-limit bypass | Patch in next scheduled release |

## P1/P2 Response Steps

1. **Contain** (within 1 hour of detection)
   - Lock the license server release pipeline (set `APP_MAINTENANCE_MODE=true` in .env)
   - If key compromise suspected: rotate ES256 key pair immediately (see `docs/operations.md#key-rotation`)
   - If R2 bucket compromise: revoke R2 API tokens, rotate
   - Notify `amrit@nectardigit.com` immediately via phone

2. **Assess** (within 2 hours)
   - Determine scope: which theme versions are affected?
   - Which customers are impacted?
   - Is the license server affected or just the theme?

3. **Patch** (within 4 hours for P1)
   - Develop and test fix on isolated environment
   - Pair-review: minimum two engineers must sign off
   - Build release zip, verify SHA-256

4. **Deploy emergency update**
   - Upload patched release to license server (marked as `emergency` channel)
   - All Business/Enterprise customers: push update notification on dashboard banner
   - Email all licensed customers: plain-text email from `security@nectardigit.com`
   - Subject: `[SECURITY] NectarPress emergency update v{version}`

5. **Notify** (within 24 hours)
   - Email all affected customers with:
     - What was the vulnerability
     - What was the potential impact
     - Steps they need to take (usually: apply the update)
     - Confirmation that Nectar Digit's internal data was/was not affected
   - Update status page at `status.nectardigit.com`
   - If personal data of readers was potentially exposed: follow Nepal IPA 2075 breach notification procedures

6. **Post-incident review** (within 7 days)
   - Root cause analysis
   - What controls failed?
   - What new controls are needed?
   - Public disclosure coordinate with reporter (90-day window)
   - Update this document with lessons learned

## Key Contacts

| Role | Name | Contact |
|------|------|---------|
| Lead developer | Amrit | amrit@nectardigit.com |
| Security reports inbox | — | security@nectardigit.com |
| Customer alerts | — | info@nectardigit.com |
| Hosting (DigitalOcean) | — | DO dashboard + support |
| Cloudflare (R2/DNS/WAF) | — | CF dashboard |

## Runbooks

- **License server down**: check DO droplet status → check nginx → check PHP-FPM → check MySQL
- **R2 unreachable**: fallback — disable update checks; theme continues to work
- **Key rotation**: see `docs/operations.md#key-rotation`
- **Tamper report flood** (>100 reports in 1 hour from one site): likely loop — disable beacon temporarily
