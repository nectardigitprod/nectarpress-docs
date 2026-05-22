# NectarPress Migration Guide

> Migrating from another theme carries the highest risk of SEO disruption. Read this guide fully before touching anything on a live site. Every migration should be done on a staging copy first.

---

## Migration Principles

1. **Staging first, always** — migrate on staging.yourdomain.com, verify thoroughly, then apply to production during off-peak hours
2. **Preserve URLs** — changing URL patterns loses SEO ranking. Use redirects where URL structure must change
3. **Export before starting** — full database + uploads backup before making any changes
4. **Verify with Search Console** — connect Google Search Console before and after; watch for crawl errors for 2 weeks post-migration

---

## Migrating from TagDiv Newspaper / JNews / Soledad

These themes store layout data in post meta and custom database tables. NectarPress has a different architecture, so you'll need to:

### 1. Export content (safe, non-destructive)

```bash
wp export --post_type=post,page,attachment --filename=content-export.xml
```

### 2. Identify and preserve custom URLs

```bash
# Get all post slugs from old site
wp post list --post_type=post --fields=post_name,ID --format=csv > old-slugs.csv
```

### 3. Install NectarPress on staging

Do not deactivate the old theme on production yet. Set up a staging copy:
- Clone your production database + files
- Install NectarPress on staging
- Complete the setup wizard

### 4. Import content

If migrating from the same WordPress installation:
- Your posts, pages, and media are already there
- Only the theme-specific meta (slider settings, builder layouts) needs attention

If migrating from a different WordPress installation:
```bash
wp import content-export.xml --authors=create
```

### 5. Rebuild homepage layout

TagDiv and JNews store homepage layouts in their proprietary builder format. These don't translate to NectarPress.
- Use NectarPress's Homepage Builder (Appearance → Homepage Builder)
- Re-create your section structure using NectarPress's section types
- This typically takes 1–2 hours for a typical news portal

### 6. Set up reporter profiles

NectarPress's reporter roles don't exist in other themes. For each author:
1. Go to Users → Edit User
2. Change role to `Reporter (NectarPress)` or appropriate role
3. Fill in the NectarPress Reporter Profile section

### 7. Redirects for URL changes

If your old theme used different URL structures:
```apache
# .htaccess example: old /category/rajniti/ was /rajniti/
Redirect 301 /rajniti/ /category/rajniti/
```

Or use the **Redirection** WordPress plugin for a UI-based approach.

### 8. Verify Schema.org markup

NectarPress adds NewsArticle schema automatically. Check with Google's Rich Results Test:
```
https://search.google.com/test/rich-results?url=https://yourdomain.com/your-post-slug/
```

---

## Migrating from Magazine Pro / GeneratePress + Custom Builds

These are lighter themes without built-in newsroom features. The migration is simpler:

1. Your posts and taxonomy structure carries over unchanged
2. You likely don't have complex homepage builder data to migrate
3. Focus on: header layout, widget areas, color scheme, logo placement

NectarPress's Theme Options (20 tabs) covers all standard customization that was previously done via custom CSS or child theme modifications.

---

## Migrating from Joomla (K2 / JoomNews)

High-level steps only — this migration requires custom scripting:

1. **Export Joomla content** to XML via K2 exporter or a custom MySQL dump
2. **Write a migration script** that creates WordPress posts from Joomla's content structure
3. **Map authors**: Joomla user IDs → WordPress user IDs
4. **Map categories**: Joomla section+category → WordPress category hierarchy
5. **Import media**: `wp media import` for migrated image files
6. **Set up URL redirects**: Joomla's URL pattern (`/component/k2/item/123-slug`) → WordPress (`/year/month/slug/`)

The [FG Joomla to WordPress](https://wordpress.org/plugins/fg-joomla-to-wordpress/) plugin automates much of this. After import, configure NectarPress normally.

---

## Preserving SEO During Migration

### Critical: don't change post slugs

WordPress post slugs (the URL-safe name in the URL) are what Google indexes. If your migration creates new post IDs with new slugs:

1. The old URLs 404 → Google loses ranking signals
2. Use 301 redirects from old URLs to new slugs
3. Keep redirect rules for at least 1 year (Google can take 6–12 months to fully process 301s on high-volume sites)

### robots.txt

During migration, block the staging site:
```
# staging.yourdomain.com/robots.txt
User-agent: *
Disallow: /
```

Remove this after migrating to production.

### Sitemap

NectarPress generates a sitemap at `/wp-sitemap.xml` (WordPress core) plus a News-specific sitemap at `/wp-sitemap-posts-post-1.xml`. Submit both to Google Search Console after migration.

### Canonical URLs

If you had `www` / non-`www` inconsistency on the old site, migration is the time to standardize. Choose one and add a 301 redirect for the other in Nginx/Apache config (not `.htaccess` — do it at the server level for efficiency).

---

## Bulk Content Import via WP-CLI

For importing large amounts of content from external sources (wire services, archives):

```bash
# Import from XML (standard WP export format)
wp import archive-2025.xml --authors=mapping.csv

# Import from JSON (custom format — requires custom importer plugin)
wp eval-file scripts/import-from-json.php --path=/var/data/archive.json

# Verify import counts
wp post list --post_type=post --post_status=publish --format=count
wp media list --format=count
```

After bulk import, rebuild the NectarPress demo import map if you need demo content alongside real content:
```bash
wp nectarpress demo import enterprise-daily --yes
```

Demo content coexists with real content — it uses different post meta to distinguish itself.
