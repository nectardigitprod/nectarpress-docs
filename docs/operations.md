# NectarPress — Operations Guide

## Release Process

1. Develop on `feature/` branch → PR to `develop`
2. Merge `develop` → `release/vX.Y.Z` branch
3. Build: `npm run build && npm run zip`
4. Upload zip to license server admin (Filament → Releases → New Release)
5. Server generates SHA-256 + ES256-signed manifest → stores on R2
6. Tag: `git tag vX.Y.Z && git push origin vX.Y.Z`
7. Licensed sites see update in Dashboard → Updates within 12 hours

## Key Management

### ES256 Signing Key Pair

- **Private key**: `storage/keys/license-private.pem` on the license server
  - NEVER committed to git
  - Store in a secrets manager (Vault, AWS Secrets Manager, or 1Password Teams)
  - Backed up to an encrypted offline medium
- **Public key**: `assets/keys/license-public.pem` in the theme repo
  - COMMITTED to the theme — it is public
  - Update `NECTARPRESS_PUBKEY_FINGERPRINT` in `includes/security/integrity-monitor.php`
    whenever the key is rotated

### Key Rotation

1. Generate new key pair: `php artisan license:generate-keys`
2. Update `assets/keys/license-public.pem` in the theme
3. Update `NECTARPRESS_PUBKEY_FINGERPRINT` constant
4. Ship new theme version with updated public key
5. Revoke old private key from secrets manager

## R2 Storage Structure

```
nectarpress-releases/
  stable/vX.Y.Z/
    nectarpress-X.Y.Z.zip   ← signed download URL (10-min expiry)
    manifest.json            ← file integrity manifest
    manifest.json.sig        ← ES256 signature
    changelog.md
    SHA256SUMS
  beta/vX.Y.Z-beta/
    ...
```

## Monitoring

- Uptime: BetterStack (status.nectardigit.com)
- Logs: `storage/logs/laravel.log` on license server
- Tamper reports: license server admin → Verification Logs (filter by event=tamper_report)

## Backup

- MySQL: daily dump → R2 bucket `nectarpress-license-backups`, 30-day retention
- R2 releases bucket: versioned (Cloudflare R2 object versioning)

## Scaling Triggers

- >1,000 daily verification calls → move MySQL to managed DB
- >5,000 daily calls → add second app server behind Cloudflare load balancer
- >10 GB R2 storage → still zero egress cost on Cloudflare R2 (no CDN needed)
