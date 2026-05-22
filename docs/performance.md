# NectarPress Performance Guide

> NectarPress targets Lighthouse mobile ≥ 90 and Core Web Vitals: LCP ≤ 2.5s, INP ≤ 200ms, CLS ≤ 0.1 on a clean install with the Enterprise demo pack. These targets are achievable without any external caching plugin on a VPS with 2 vCPU / 4 GB RAM.

---

## Expected Baselines (Clean Install + Cloudflare CDN)

| Page | LCP | INP | CLS | LH Perf |
|------|-----|-----|-----|---------|
| Homepage | 1.8s | 85ms | 0.02 | 94 |
| Single post | 1.5s | 72ms | 0.01 | 96 |
| Category archive | 1.9s | 90ms | 0.03 | 92 |
| Reporter page | 1.6s | 78ms | 0.01 | 95 |

Measurements: Lighthouse DevTools throttled (Slow 4G, 4× CPU), Chrome 124, Ubuntu 22.04.

---

## Built-in Optimizations

NectarPress applies these out of the box — no configuration needed:

### Font Loading
- All Nepali fonts (Mukta, Noto Sans Devanagari, Kalimati) self-hosted in `/assets/fonts/`
- Fonts subset to Devanagari + Latin Basic (saves 50–60% vs full unicode)
- `font-display: swap` prevents invisible text during load
- Primary fonts preloaded via `<link rel="preload" as="font">`
- Font stylesheet non-blocking (`<link rel="preload" as="style" onload="this.rel='stylesheet'">`)

### Script Loading
- All NectarPress scripts: `defer` strategy
- Google Analytics, GTM, Facebook Pixel: lazy-loaded after first user interaction (or 2s after DOMContentLoaded)
- No jQuery in new Sprint 1+ code; legacy jQuery usage isolated to specific handlers

### Image Optimization
- Hero / LCP images: `fetchpriority="high"` + `loading="eager"`
- All other images: `loading="lazy"` + `decoding="async"`
- Explicit `width` + `height` attributes on all images via filter (prevents CLS)
- `srcset` + `sizes` on all `wp_get_attachment_image()` calls

### CSS
- `contain: layout` on repeating card components (reduces repaints)
- Logical CSS properties where supported (`margin-inline-start` instead of `margin-left`)
- Critical CSS inlined at build time for homepage, single, archive, reporter templates

### Third-Party Resource Hints
- `preconnect` only to configured services (no unused preconnects)
- `dns-prefetch` for less-critical third parties

---

## Caching Plugin Recommendations

| Plugin | Recommended | Notes |
|--------|-------------|-------|
| **WP Rocket** | Strongly recommended | Best page cache + CSS/JS minification |
| **LiteSpeed Cache** | Recommended on LiteSpeed hosts | Excellent on Himalayan Host + LiteSpeed |
| **W3 Total Cache** | Acceptable | More complex configuration required |
| **Hummingbird** | Acceptable | Good for managed WP hosts |
| **WP Super Cache** | Basic | Fine for low-traffic sites |

### Paywall Cache Exclusion (critical)

All caching plugins must exclude premium-gated content from the page cache. Serving a cached paywalled page to a premium subscriber who logged in after the cache was set would show them the paywall incorrectly.

NectarPress automatically sets `Cache-Control: private, no-store` on:
- Any page where the paywall is triggered
- All admin pages
- All checkout/payment pages

**WP Rocket** respects these headers automatically.

For **W3 Total Cache** and others, add these URL patterns to the "Never Cache" list:
```
/np-paywall/
/np-checkout/
/np-account/
```

### Dynamic Widgets That Must Bypass Cache

These widgets fetch real-time data and must not be cached:
- NEPSE ticker (updates every 15 minutes during market hours)
- Forex rates (updates hourly)
- Live blog auto-refresh
- Breaking news strip (when using "live" mode)

NectarPress marks these with `Cache-Control: private` headers when rendered independently via the REST API. When embedded in page templates, they use AJAX calls that are automatically excluded by most caching plugins.

---

## CDN Configuration

### Cloudflare (recommended)

Cloudflare has edge nodes in Mumbai and Singapore — the closest to Nepal. Average latency from Kathmandu:

- Origin (Nepal host): 30–80ms depending on host
- Cloudflare edge (Mumbai): ~20ms first byte for cached assets

**Recommended Cloudflare settings:**
- SSL mode: Full (strict)
- Always Use HTTPS: On
- Auto Minify: Off (NectarPress ships pre-minified)
- Rocket Loader: **Off** (conflicts with NectarPress's lazy analytics loader)
- Cache level: Aggressive for assets (`.js`, `.css`, `.woff2`)
- Bypass cache for: WordPress admin, login, payment pages

See [docs/server-config.md](server-config.md) for full Cloudflare page rules.

### Bunny CDN

Bunny has a PoP in Mumbai. Good alternative to Cloudflare for asset delivery:
- Pull zone pointing to your origin
- Cache TTL: 30 days for images, 1 year for hashed JS/CSS
- Smart Optimization: On (auto WebP conversion)

---

## WebP / AVIF Image Optimization

Enable in **Theme Options → Tab 12 Performance → Image Optimization**.

Requirements:
- PHP GD with WebP support: `gd_info()` should show `WebP Support: true`
- OR PHP Imagick with WEBP delegate

When enabled:
- Uploaded JPG/PNG images are automatically converted to WebP
- Original files retained as fallback for browsers without WebP support
- The `<picture>` element with `<source type="image/webp">` is used for all responsive images

For AVIF (even smaller files, better quality), enable the "AVIF output" toggle — requires Imagick 7.0.25+.

---

## Database Query Optimization

NectarPress adds database indexes for its most-queried meta keys on activation:
- `nr_workflow_state` (editorial workflow queries)
- `_nectarpress_demo_import_id` (demo content scoping)
- `_np_zone` (ad zone lookups)

For sites with 10,000+ posts, enable the object cache layer in `wp-config.php`:

```php
// Redis (recommended)
define('WP_REDIS_HOST', '127.0.0.1');
define('WP_REDIS_PORT', 6379);
define('WP_REDIS_DATABASE', 0);
```

With Redis, NectarPress's cache TTLs extend from 1 hour to 12 hours for stable data (ad zones, dashboard aggregations).

---

## Performance Monitoring

### wp nectarpress doctor

The doctor command checks key performance indicators:

```bash
wp nectarpress doctor
```

Checks: PHP memory limit, MySQL version, filesystem write speed, object cache availability.

### Query Monitor Plugin

During development or when investigating slowness, install [Query Monitor](https://wordpress.org/plugins/query-monitor/). NectarPress respects QM's environment and adds its own timing data to the QM panel.

Key things to watch:
- Homepage section queries should complete in <50ms total
- No query should run >100ms
- N+1 patterns (same query running 20+ times) indicate a template issue

---

## Theme Zip Size Limits

The theme zip must stay ≤ 5 MB. Current size: ~3.2 MB.

Excluded from zip automatically:
- `node_modules/`
- `.git/`
- `vendor/` (Composer dependencies)
- `tests/`
- `docs/`
- `*.map` files (source maps)
- `.DS_Store`

To verify: `npm run zip && ls -lh dist/nectarpress.zip`
