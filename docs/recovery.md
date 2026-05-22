# NectarPress — Recovery Procedures

## Scenario: Auto-rollback failed, site is broken

If the atomic swap succeeded but the smoke-test failed AND the auto-rollback also failed,
the site may be in a broken state. Follow these steps via SSH:

```bash
cd /path/to/wp-content/themes
ls -la | grep nectarpress    # identify nectarpress-old-TIMESTAMP directories
mv nectarpress nectarpress-broken-$(date +%s)
mv nectarpress-old-TIMESTAMP nectarpress   # replace with actual timestamp from ls output
# Visit wp-admin to verify
```

## Scenario: Update Center is not accessible

If you cannot access wp-admin at all:

```bash
# Via WP-CLI:
cd /path/to/wordpress
wp nectarpress update rollback <version>

# Or manually via SSH (see above)
```

## Scenario: Integrity monitor raised a false alarm

If you intentionally modified a theme file (e.g. added custom CSS to functions.php):

1. Go to **wp-admin → NectarPress → License & Updates → Integrity**
2. Find the flagged file in the anomaly log
3. Click **Mark as Intentional**
4. The file's current hash is accepted and beaconed to Nectar Digit

## Finding retained backup directories

NectarPress retains old theme directories for 7 days after an update:
- `wp-content/themes/nectarpress-old-TIMESTAMP` — pre-update backup (safe to roll back to)
- `wp-content/themes/nectarpress-failed-TIMESTAMP` — post-rollback archive (for forensics)

These are auto-deleted 7 days after creation.

## Contacting Nectar Digit support

- Email: info@nectardigit.com
- Security incidents: security@nectardigit.com
- Website: https://nectardigit.com
