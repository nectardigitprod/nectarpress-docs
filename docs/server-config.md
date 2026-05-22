# NectarPress Server Configuration Guide

> Correct server configuration is responsible for 30–50% of your Lighthouse Performance score. These settings are verified on Apache 2.4 + PHP 8.x and Nginx 1.24.

---

## Apache `.htaccess`

Place this inside `<IfModule mod_rewrite.c>` in your WordPress `.htaccess`, **after** the standard WordPress rewrite block.

```apache
# ── Browser Caching ─────────────────────────────────────────────
<IfModule mod_expires.c>
    ExpiresActive On

    # Fonts — 1 year (immutable, hashed filenames)
    ExpiresByType font/woff2                        "access plus 1 year"
    ExpiresByType font/woff                         "access plus 1 year"
    ExpiresByType application/font-woff             "access plus 1 year"

    # Theme CSS + JS (Vite hashed filenames, safe to cache 1 year)
    ExpiresByType text/css                          "access plus 1 year"
    ExpiresByType application/javascript            "access plus 1 year"
    ExpiresByType text/javascript                   "access plus 1 year"

    # Images
    ExpiresByType image/webp                        "access plus 1 year"
    ExpiresByType image/avif                        "access plus 1 year"
    ExpiresByType image/jpeg                        "access plus 1 month"
    ExpiresByType image/png                         "access plus 1 month"
    ExpiresByType image/svg+xml                     "access plus 1 month"
    ExpiresByType image/gif                         "access plus 1 month"
    ExpiresByType image/x-icon                      "access plus 1 year"

    # HTML — always revalidate
    ExpiresByType text/html                         "access plus 0 seconds"
</IfModule>

# ── Cache-Control Headers ────────────────────────────────────────
<IfModule mod_headers.c>
    # Hashed assets — immutable (Vite appends content hash to filename)
    <FilesMatch "\.(js|css|woff2|woff)$">
        Header set Cache-Control "max-age=31536000, immutable"
    </FilesMatch>
    # Images
    <FilesMatch "\.(jpg|jpeg|png|gif|webp|avif|svg|ico)$">
        Header set Cache-Control "max-age=2592000, public"
    </FilesMatch>
</IfModule>

# ── Gzip Compression ─────────────────────────────────────────────
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css
    AddOutputFilterByType DEFLATE application/javascript text/javascript
    AddOutputFilterByType DEFLATE application/json application/ld+json
    AddOutputFilterByType DEFLATE image/svg+xml application/xml
    AddOutputFilterByType DEFLATE font/woff font/woff2

    # Serve pre-compressed files if available
    RewriteEngine On
    RewriteCond %{HTTP:Accept-Encoding} gzip
    RewriteCond %{REQUEST_FILENAME}.gz -f
    RewriteRule ^(.*)$ $1.gz [QSA,L]
</IfModule>

# ── Brotli (requires mod_brotli) ─────────────────────────────────
<IfModule mod_brotli.c>
    AddOutputFilterByType BROTLI_COMPRESS text/html text/plain text/css
    AddOutputFilterByType BROTLI_COMPRESS application/javascript text/javascript
    AddOutputFilterByType BROTLI_COMPRESS application/json application/ld+json
    AddOutputFilterByType BROTLI_COMPRESS font/woff2 image/svg+xml
</IfModule>

# ── Security Headers ─────────────────────────────────────────────
<IfModule mod_headers.c>
    Header always set X-Content-Type-Options "nosniff"
    Header always set X-Frame-Options "SAMEORIGIN"
    Header always set Referrer-Policy "strict-origin-when-cross-origin"
    Header always set Permissions-Policy "geolocation=(), microphone=(), camera=()"
    # HSTS — enable ONLY after verifying HTTPS works perfectly on your site
    # Header always set Strict-Transport-Security "max-age=31536000; includeSubDomains; preload"
</IfModule>

# ── Prevent .php file access in uploads ──────────────────────────
<IfModule mod_rewrite.c>
    RewriteRule ^wp-content/uploads/.*\.php$ - [F,NC]
</IfModule>
```

---

## Nginx `nginx.conf` / site config

Add inside your `server {}` block:

```nginx
# ── Browser caching ──────────────────────────────────────────────
location ~* \.(woff2|woff|js|css)$ {
    expires 1y;
    add_header Cache-Control "max-age=31536000, immutable";
    add_header Vary "Accept-Encoding";
    gzip_static on;
    brotli_static on;
}

location ~* \.(jpg|jpeg|png|gif|webp|avif|svg|ico)$ {
    expires 30d;
    add_header Cache-Control "max-age=2592000, public";
    gzip_static on;
}

# HTML — always revalidate
location ~* \.html$ {
    add_header Cache-Control "no-cache, must-revalidate";
    add_header Pragma "no-cache";
}

# ── Gzip compression ─────────────────────────────────────────────
gzip on;
gzip_vary on;
gzip_proxied any;
gzip_comp_level 6;
gzip_types
    text/plain text/css text/xml text/javascript
    application/json application/javascript application/ld+json
    image/svg+xml font/woff font/woff2 application/xml;

# ── Brotli compression (requires ngx_brotli module) ──────────────
brotli on;
brotli_comp_level 6;
brotli_types
    text/plain text/css application/javascript
    application/json application/ld+json image/svg+xml;

# ── Security headers ─────────────────────────────────────────────
add_header X-Content-Type-Options "nosniff" always;
add_header X-Frame-Options "SAMEORIGIN" always;
add_header Referrer-Policy "strict-origin-when-cross-origin" always;
add_header Permissions-Policy "geolocation=(), microphone=(), camera=()" always;

# ── Block PHP in uploads ──────────────────────────────────────────
location ~* /wp-content/uploads/.*\.php$ {
    deny all;
}
```

---

## Cloudflare Page Rules

If you use Cloudflare CDN (recommended for Nepali traffic — edge nodes in Mumbai/Singapore):

| URL Pattern | Cache Level | Edge TTL |
|---|---|---|
| `*.nectarpress.com/wp-content/themes/nectarpress/assets/*` | Cache Everything | 1 year |
| `*.nectarpress.com/wp-content/uploads/*` | Cache Everything | 30 days |
| `*.nectarpress.com/?*` | Bypass | — |
| `*.nectarpress.com/wp-admin/*` | Bypass | — |
| `*.nectarpress.com/wp-login.php` | Bypass | — |

**Important:** Always bypass cache for:
- URLs with query parameters (`?`)
- Admin paths (`/wp-admin/`)
- Logged-in users (set via `Cache-Control: private` — NectarPress does this automatically for premium readers)

---

## PHP Configuration Recommendations

```ini
; php.ini or .user.ini
memory_limit = 256M          ; theme runs cleanly in 40M; 256M for import operations
max_execution_time = 300     ; allow for demo import (large packs)
upload_max_filesize = 64M    ; ePaper PDFs can be large
post_max_size = 64M
opcache.enable = 1
opcache.memory_consumption = 128
opcache.interned_strings_buffer = 8
opcache.max_accelerated_files = 4000
opcache.revalidate_freq = 60
```

---

## Hosting Recommendations for Nepali Traffic

| Tier | Provider | Nepal latency | Notes |
|---|---|---|---|
| Shared | Himalayan Host | ~30ms | Nepal-based, best latency |
| VPS | DigitalOcean Bangalore | ~70ms | Good balance, SSD |
| VPS | Vultr Mumbai | ~65ms | Similar to DO |
| Managed WP | Cloudways (Bangalore) | ~70ms | Easy scaling |
| Enterprise | AWS ap-south-1 (Mumbai) | ~60ms | With CloudFront → ~20ms |

**Always add Cloudflare CDN** regardless of host — Nepali peak hours (7–10 PM NST) can bring 10× traffic spikes during breaking news events. Cloudflare's edge absorbs this without hitting your origin.
