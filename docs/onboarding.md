# NectarPress Onboarding Guide

> You should be reading this after purchasing a NectarPress license and installing the theme on a fresh WordPress site. This guide walks you from zero to a live news portal in under 30 minutes.

---

## Prerequisites

- WordPress 6.2 or higher
- PHP 7.4 or higher (8.1+ recommended)
- A NectarPress license key from [nectardigit.com](https://nectardigit.com) — or start with a 14-day evaluation
- HTTPS enabled on your domain (required for Khalti payments and CSP headers)

---

## Step 1 — Install the Theme

1. Log into WordPress Admin → **Appearance → Themes → Add New → Upload Theme**
2. Upload the `nectarpress.zip` file
3. Click **Activate**

The setup wizard starts automatically on your next admin page load.

---

## Step 2 — Setup Wizard (7 steps, ~5 minutes)

### Step 1: Welcome + EULA

Read the End-User License Agreement. Scroll to the bottom and tick **"I have read and agree to the terms"** to proceed. You cannot advance without accepting — this is intentional.

### Step 2: License Key

You have two options:

**Option A — Activate license key**
Paste your `NP-XXXX-XXXX-XXXX-XXXX` key from [nectardigit.com/account](https://nectardigit.com/account). A green checkmark confirms activation.

**Option B — Start 14-day evaluation**
Click "Start free evaluation" to try all Business-tier features for 14 days without a key. You'll be prompted to activate before evaluation expires.

### Step 3: Demo Content (optional)

Choose a demo pack to pre-populate your site with realistic Nepali news content:

| Pack | Best for | Plan required | Posts |
|------|----------|---------------|-------|
| **Solo Blogger** | Individual journalists | Starter+ | 20 |
| **SME News Portal** | Small newsrooms (5–20 staff) | Professional+ | 100 |
| **Enterprise Daily** | Large newspapers/TV portals | Business+ | 500 |

Demo content is labelled with "(Demo)" and marked with a special meta tag so it can be rolled back completely at any time — it never touches your real content.

Skip this step if you prefer to start with a clean site.

### Step 4: Branding

Upload your publication's:
- **Logo** (SVG or PNG, transparent background, min 400×150 px)
- **Favicon** (32×32 or 180×180 PNG)
- **Site Name** and **Tagline**

You can change these later at Appearance → Customize or Theme Options → Tab 1.

### Step 5: Header Preset

Choose a header layout:

| Preset | Best for |
|--------|----------|
| **Minimal** | Clean, modern look; logo left + nav right |
| **Classic** | Traditional newspaper; billboard ads + centered logo |
| **Newsroom** | Breaking news strip + NEPSE + date ticker |

All presets are fully customizable after setup.

### Step 6: Integrations

Configure optional third-party integrations. All are optional — skip any you don't need:

- **Google Analytics 4** — paste your `G-XXXXXXXXXX` Measurement ID
- **Google Tag Manager** — paste your `GTM-XXXXXXX` container ID (if using GTM, skip GA4)
- **Facebook Pixel** — paste your 15-digit Pixel ID
- **Disqus** — enter your Disqus shortname for comment sections
- **Mailchimp** — paste your Mailchimp API key for newsletter signups

Scripts load lazily after first user interaction — they do not block your Lighthouse score.

### Step 7: Done

Your site is ready. Quick links to:
- Open the Customizer (live preview)
- View your demo content
- Go to the Reporter Directory
- Read the documentation

---

## Step 3 — First Reporter, First Post

### Add your first reporter

1. **Users → Add New**
2. Set Role to `Reporter (NectarPress)` or `Editor (NectarPress)`
3. Fill in the **NectarPress Reporter Profile** section that appears below the standard user form:
   - Designation (e.g., "Political Reporter")
   - Bio (Nepali, displays on reporter page)
   - Beat (which categories this reporter covers)
   - District (for directory filtering)
   - Social links
4. Click **Add New User**

The reporter now appears at `/reporters/<username>/` automatically — no manual page creation needed.

### Write your first post

1. **Posts → Add New**
2. Write your article. The NectarPress **Story** panel in the right sidebar handles:
   - **Workflow state** (Draft → Pending Review → Editor Review → Approved → Published)
   - **Primary byline** (auto-set to post author)
   - **Co-bylines** (add photographer, translator, etc.)
   - **Category assignment** (limits which editors can review based on beat)
3. For a quick first publish, click **Publish** directly — the workflow is enforced for newsroom roles but admins can bypass.

---

## Step 4 — Configure Your Homepage Sections

1. Go to **Appearance → Homepage Builder** (or Theme Options → Tab 8 Homepage)
2. Drag section types onto the canvas:
   - **Featured Story** — large hero image + text
   - **Breaking Stack** — 4–6 stories in a tight grid
   - **Category Section** — latest posts from a chosen category
   - **Full-Width Slider** — up to 5 stories with auto-advance
   - **Magazine Grid** — 8-story magazine-style layout
   - **NEPSE Widget** — live stock ticker
   - **Poll Widget** — embedded opinion poll
   - **ePaper Widget** — latest edition cover + download link
3. Click **Save Layout**
4. Visit your homepage — the layout is live immediately

---

## Post-Wizard Checklist

After completing the wizard, a checklist widget appears on your WordPress dashboard. It auto-ticks each item as you complete it:

- [ ] Setup wizard completed
- [ ] License activated
- [ ] Demo content imported (or skipped)
- [ ] Logo and site name set
- [ ] First real post published

Dismiss the widget once you're ready — it won't come back.

---

## Getting Help

- **WP-CLI health check:** `wp nectarpress doctor`
- **Documentation:** [docs.nectardigit.com/nectarpress](https://docs.nectardigit.com/nectarpress)
- **Support email:** support@nectardigit.com
- **Response time:** 1 business day for Professional+, same day for Enterprise
