# Service Level Agreement (SLA) — NectarPress

**Nectar Digit Pvt. Ltd.**  
**Effective date:** 2026-05-22  
**Applies to:** Business and Enterprise plan customers

---

## 1. Scope

This SLA covers:
- **License Server** (license.nectardigit.com) — activation, verification, update distribution
- **Support response time** — email support at support@nectardigit.com

This SLA does **not** cover:
- Your WordPress hosting provider
- Third-party services (Khalti, Google Analytics, etc.)
- Issues caused by customer-modified theme files or incompatible plugins
- Outages caused by DDoS attacks or force majeure events

---

## 2. License Server Uptime

| Plan | Uptime Target | Measurement Period |
|------|--------------|-------------------|
| Business | 99.0% | Monthly |
| Enterprise | 99.5% | Monthly |

Uptime is measured as: `(total minutes − downtime minutes) / total minutes × 100`

**Downtime** is defined as the license server returning 5xx errors for more than 5 consecutive minutes.

**Scheduled maintenance** (announced 48h in advance via status.nectardigit.com) is excluded from downtime calculations. Scheduled maintenance windows are Sunday 01:00–03:00 NPT.

**Graceful degradation:** Even if the license server is unreachable, the NectarPress theme continues operating normally for up to 30 days (see EULA §6). Your live news site front-end is NEVER broken by a license server outage.

---

## 3. Support Response Time

| Plan | First response | Resolution target |
|------|---------------|-----------------|
| Starter | 3 business days | Best effort |
| Professional | 2 business days | Best effort |
| Business | 1 business day | 5 business days for critical |
| Enterprise | 4 business hours | 2 business days for critical |

**Business hours:** Monday–Friday, 10:00 AM – 6:00 PM Nepal Standard Time (UTC+5:45)  
**Response = human acknowledgement**, not resolution. Complex issues may take longer.

**Critical issues** for Business/Enterprise: security vulnerabilities, data loss, site-down scenarios where NectarPress is the root cause (verified by the doctor command).

---

## 4. Support Channels

| Channel | Plans | Hours |
|---------|-------|-------|
| Email (support@nectardigit.com) | All | Business hours |
| Priority queue (tagged by domain) | Business+ | Business hours |
| Emergency escalation (direct Amrit contact) | Enterprise only | 24/7 for P0 incidents |

---

## 5. Exclusions & Customer Responsibilities

Nectar Digit is not responsible for:
- Outages caused by changes to your WordPress installation, database, or server
- Performance issues on your hosting provider
- Conflicts with plugins not listed in our compatibility matrix
- Issues arising from failure to apply theme updates
- Data loss caused by customer's failure to maintain backups

Customer responsibilities:
- Maintain HTTPS on the licensed domain
- Keep WordPress and PHP within supported version ranges
- Provide accurate information when reporting issues (doctor output, error logs)

---

## 6. SLA Credits (Enterprise only)

For Enterprise customers with a signed contract:

| Monthly uptime | Credit |
|---------------|--------|
| 99.0% – 99.49% | 10% of monthly fee |
| 98.0% – 98.99% | 25% of monthly fee |
| < 98.0% | 50% of monthly fee |

Credits are applied to the next renewal invoice. Credits are not cash refunds. Maximum credit is 50% of monthly fee per incident.

To claim a credit: email billing@nectardigit.com within 30 days of the incident with the incident date/time and reference to the status.nectardigit.com incident report.

---

## 7. Incident Communication

All incidents are reported at [status.nectardigit.com](https://status.nectardigit.com).

- **Detection to first status update:** ≤ 15 minutes
- **Ongoing updates:** every 30 minutes during active incident
- **Post-incident report:** within 3 business days for incidents exceeding 30 minutes

Subscribe to incident notifications at status.nectardigit.com.

---

## 8. Contact

SLA questions: billing@nectardigit.com  
Incident escalation (Enterprise): Direct contact provided in welcome email
