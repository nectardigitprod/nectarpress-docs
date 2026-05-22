# Contributing to NectarPress

Thank you for your interest in improving NectarPress! This guide covers how to report bugs, suggest features, and submit code contributions.

---

## Ways to Contribute

| Type | How |
|---|---|
| Bug report | [Open a GitHub Issue](https://github.com/nectardigit/nectar-press/issues) |
| Feature request | [Open a GitHub Discussion](https://github.com/nectardigit/nectar-press/discussions) |
| Documentation fix | Click "Edit this page" on any docs page |
| Code contribution | Fork → branch → PR (see below) |
| Translation | Contact [translations@nectardigit.com](mailto:translations@nectardigit.com) |

---

## Code Contribution Workflow

### 1. Fork and clone

```bash
gh repo fork nectardigit/nectar-press --clone
cd nectar-press
```

### 2. Install dependencies

```bash
composer install
npm install
```

### 3. Create a branch

Use the convention `type/short-description`:

```bash
git checkout -b fix/wizard-nonce-validation
git checkout -b feat/nepali-calendar-widget
git checkout -b docs/add-khalti-setup-guide
```

### 4. Make your changes

- Follow [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/php/)
- Run PHPCS before committing: `composer lint`
- Run PHPStan: `composer analyse`
- Run tests: `composer test`

### 5. Commit

```bash
git add -p   # stage hunks interactively
git commit -m "fix: wizard nonce uses correct action name"
```

Commit message format: `type: short description` (lowercase, imperative mood)

Types: `feat`, `fix`, `docs`, `style`, `refactor`, `test`, `chore`

### 6. Open a Pull Request

```bash
gh pr create --fill
```

All PRs require:

- [ ] Description of what changed and why
- [ ] Reference to related issue (`Fixes #123`)
- [ ] Tests pass (`composer test`)
- [ ] PHPCS passes (`composer lint`)
- [ ] No secrets, credentials, or `.env` values committed

---

## Code Style

NectarPress follows the **WordPress PHP Coding Standards** with these additions:

- `nectarpress_` prefix on all public functions
- `NECTARPRESS_` prefix on all constants
- `NectarPress_` prefix on all class names
- `np-` or `npwz-` CSS class prefixes (theme front-end / wizard)
- Short-name aliases (`np_*`) live exclusively in `includes/np-aliases.php`

### PHP version target

PHP 7.4+ (no PHP 8-only syntax in public API methods). Internal-only methods may use PHP 8 features.

---

## Running Tests

```bash
# Unit tests (PHPUnit)
composer test

# Static analysis
composer analyse

# Coding standards
composer lint

# Fix auto-fixable CS issues
composer lint:fix
```

Tests live in `tests/Unit/`. Functional tests use WP_Mock — no live WordPress required.

---

## Documentation Contributions

Docs live in `docs/` and are built with [MkDocs Material](https://squidfunk.github.io/mkdocs-material/).

```bash
pip install mkdocs-material mkdocs-minify-plugin
mkdocs serve   # live-reload at http://127.0.0.1:8000
```

For new pages:

1. Create `docs/your-page.md`
2. Add it to the `nav:` section of `mkdocs.yml`
3. Keep content concise — link out to external references rather than duplicating them

---

## Security Vulnerabilities

**Do not open a public GitHub Issue for security vulnerabilities.**

Report privately to [security@nectardigit.com](mailto:security@nectardigit.com). We respond within 72 hours and follow responsible disclosure (90-day embargo before public disclosure).

---

## License

By contributing, you agree your contributions are licensed under the same license as NectarPress (GPL-2.0 for open-source portions, proprietary for the license-enforcement layer). See `LICENSE.txt` and `EULA.txt` for details.
