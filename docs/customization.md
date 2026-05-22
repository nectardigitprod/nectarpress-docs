# Customization Guide

NectarPress ships with a **20-tab Theme Options panel** and five visual drag-and-drop builders — no coding required. This guide covers everything from brand colors to homepage layouts.

---

## Accessing Theme Options

Navigate to **Appearance → NectarPress Options** (also accessible at the top of the WordPress admin via the "NectarPress" menu item).

---

## Tab Overview

| Tab | What you configure |
|-----|--------------------|
| 01 General | Site name (Nepali/English), logo, favicon, tagline |
| 02 Branding | Logo upload, logo dimensions |
| 03 Header | Sticky nav, trending strip, dark-mode toggle, language switcher |
| 04 Homepage | Stock ticker URL, roadblock countdown |
| 05 Homepage Layout | Links to the Homepage Sections drag-and-drop manager |
| 06 Single Post | Layout, typography, related posts, social share, ad slots |
| 07 Footer Company | Publisher name, addresses, registration numbers |
| 08 Footer Copyright | Copyright text, developer credit toggle |
| 09 Social Links | Up to 8 social icon + URL pairs |
| 10 SEO | Meta title/description/keywords, OG/Twitter tags, GA4/GTM |
| 11 Archive | Grid/list layout, pagination type, post card controls |
| 12 Performance | Caching, lazy-load settings |
| 13 Social | Additional social profile URLs |
| 14 Newsletter | Newsletter integration settings |
| 15 Integrations | GA4, GTM, Facebook Pixel, Microsoft Clarity |
| 16 Utilities | Advanced options |
| 17 ePaper | PDF viewer settings |
| 18 Newsroom | Editorial workflow settings |
| 19 Subscription | Paywall, Khalti payment gateway keys |
| 20 License | License activation, telemetry consent, update channel |

---

## Brand Colors

Set your primary and accent colors under **Tab 02 Branding** or during the Setup Wizard step 4:

- **Primary Color** — used for navigation, buttons, headings (default `#420c6c`)
- **Accent Color** — used for category badges, hover effects (default `#f26522`)

!!! tip "Live preview"
    Open the WordPress Customizer (**Appearance → Customize**) to preview color changes in real time.

---

## Visual Builders

### Homepage Sections Builder

**Appearance → Homepage Sections** — drag, drop, and configure up to 20 sections:

- Featured Article
- Category News Grid (2–4 column)
- Breaking News Ticker
- Video Section
- Poll Widget
- Advertisement Zones
- Tab-based Section

### Header Builder

**Appearance → Header Builder** — choose from five presets and configure the utility bar:

| Preset | Description |
|--------|-------------|
| Classic | Logo left, main menu right |
| Centered | Logo centered, menu below |
| Boxed | Full-width utility bar, contained header |
| Split | Logo visually splits the main menu (newspaper-traditional) |
| Newsroom | Dense layout — breaking ticker is prominent |

### Single Post Layout

**Appearance → Single Post Layout** — control: featured image style, article width, sidebar position.

### Archive / Category Layout

**Appearance → Category Layout** — 8 listing presets: `1col`, `2col`, `3col`, `4col`, `list`, `hero_grid`, `magazine`, `masonry`.

### Footer Builder

**Appearance → Footer Builder** — multi-column widget areas with drag-and-drop column ordering.

---

## Custom CSS

Add site-wide custom CSS in **Tab 02 Branding → Custom CSS** section. It is output in `<head>` on every page.

---

## Dark Mode

Enable the dark-mode toggle in **Tab 03 Header → Enable Dark Mode Toggle**. A sun/moon button appears in the nav bar. Styles are in `assets/css/style-dark-mode.css`.

---

## RTL Support

NectarPress ships a complete RTL stylesheet (`style-rtl.css`). WordPress automatically enqueues it when the site language is RTL (e.g., Arabic).

---

## Child Theme

For deeper customization, create a child theme:

```bash
mkdir wp-content/themes/nectarpress-child
```

```php
<?php
// wp-content/themes/nectarpress-child/functions.php
add_action( 'wp_enqueue_scripts', function () {
    wp_enqueue_style(
        'nectarpress-parent',
        get_template_directory_uri() . '/style.css'
    );
    wp_enqueue_style(
        'nectarpress-child',
        get_stylesheet_directory_uri() . '/style.css',
        [ 'nectarpress-parent' ]
    );
} );
```

All NectarPress template tags and hooks remain available in a child theme.
