# NectarPress Hooks Reference

> All NectarPress hooks follow the namespace pattern `nectarpress/<module>/<hook_name>`.
> This document is auto-generated from PHPDoc; do not edit manually.

---

## Actions

### `nectarpress/nr/workflow_activated`

Fires when the newsroom editorial workflow module is loaded (Business+ plan).
Use to register additional workflow-dependent functionality.

```php
add_action('nectarpress/nr/workflow_activated', function() {
    // Safe to call newsroom workflow APIs here
    NectarPress_NR_Workflow::instance()->add_state('custom_state', [...]);
});
```

**Since:** 2.5.0  
**Parameters:** none

---

### `nectarpress/nr/workflow_transition`

Fires after a post transitions between editorial workflow states.

```php
add_action('nectarpress/nr/workflow_transition', function(
    int    $post_id,
    string $from_state,
    string $to_state,
    int    $actor_id
) {
    // Example: send Slack notification on approval
    if ($to_state === 'approved') {
        my_plugin_slack_notify($post_id, $actor_id);
    }
}, 10, 4);
```

**Since:** 2.5.0  
**Parameters:**  
- `int $post_id` — Post being transitioned  
- `string $from_state` — Previous workflow state  
- `string $to_state` — New workflow state  
- `int $actor_id` — User who triggered the transition

---

### `nectarpress/nr/before_publish_check`

Fires just before a post is published. Returning a `WP_Error` from an attached filter (use the filter variant below) blocks publication.

```php
// Use the filter version for blocking logic
add_filter('nectarpress/nr/pre_publish_check', function(
    \WP_Error|true $result,
    int            $post_id
) {
    if (my_plugin_fails_check($post_id)) {
        return new WP_Error('my_check', 'Cannot publish: check failed.');
    }
    return $result;
}, 10, 2);
```

**Since:** 2.5.0  
**Parameters:**  
- `int $post_id` — Post being published

---

### `nectarpress/license/activated`

Fires after a license key is successfully activated and the JWT is stored.

```php
add_action('nectarpress/license/activated', function(
    string $plan,
    array  $features
) {
    // Example: unlock custom module on Business+
    if (in_array('newsroom_workflow', $features, true)) {
        my_plugin_enable_newsroom_extras();
    }
}, 10, 2);
```

**Since:** 2.3.0  
**Parameters:**  
- `string $plan` — Plan slug (starter/professional/business/enterprise)  
- `array $features` — Array of feature slugs unlocked by this plan

---

### `nectarpress/license/verification_failed`

Fires when the daily license verification heartbeat fails.

```php
add_action('nectarpress/license/verification_failed', function(
    string $reason,
    int    $days_since_last_success
) {
    if ($days_since_last_success > 7) {
        // Site is in graceful degradation mode — log it
        error_log("NP license unverified for {$days_since_last_success} days: {$reason}");
    }
}, 10, 2);
```

**Since:** 2.3.0  
**Parameters:**  
- `string $reason` — Machine-readable failure reason (e.g., `network_error`, `key_expired`)  
- `int $days_since_last_success` — Days since last successful verification

---

### `nectarpress/demo/import_complete`

Fires after a demo pack import finishes successfully.

```php
add_action('nectarpress/demo/import_complete', function(
    string $slug,
    string $token,
    array  $counts
) {
    // $counts: ['posts'=>500, 'users'=>10, 'media'=>5, ...]
    wp_mail(get_option('admin_email'), 'Demo imported', "Pack: {$slug}, Posts: {$counts['posts']}");
}, 10, 3);
```

**Since:** 2.6.0  
**Parameters:**  
- `string $slug` — Pack slug that was imported  
- `string $token` — Rollback token  
- `array $counts` — Associative array of item type => count

---

### `nectarpress/demo/rollback_complete`

Fires after a demo pack rollback finishes.

**Since:** 2.6.0  
**Parameters:**  
- `string $token` — Import token that was rolled back  
- `array $deleted` — `['posts'=>N, 'users'=>N, 'terms'=>N]`

---

### `nectarpress/security/integrity_violation`

Fires when the file integrity monitor detects modified theme files.

```php
add_action('nectarpress/security/integrity_violation', function(
    array $modified_files,
    int   $count
) {
    // Forward to your incident management system
    my_pagerduty_alert("NectarPress integrity violation: {$count} files modified");
}, 10, 2);
```

**Since:** 2.4.0  
**Parameters:**  
- `array $modified_files` — Array of relative file paths that changed  
- `int $count` — Number of modified files

---

### `nectarpress/upgrade`

Fires when the theme detects a version upgrade on activation. Use to run
migration tasks after a theme update.

```php
add_action('nectarpress/upgrade', function(
    string $old_version,
    string $new_version
) {
    if (version_compare($old_version, '2.7.0', '<')) {
        my_plugin_migrate_to_2_7();
    }
}, 10, 2);
```

**Since:** 2.2.0  
**Parameters:**  
- `string $old_version` — Version before the upgrade  
- `string $new_version` — Version after the upgrade (current)

---

## Filters

### `nectarpress/feature_gate`

Filters the result of `nectarpress_has_feature()`.
Allows external plugins to override feature availability (e.g., for testing).

```php
add_filter('nectarpress/feature_gate', function(
    bool   $enabled,
    string $feature,
    string $current_plan
): bool {
    // Force-enable paywall for testing
    if ($feature === 'paywall' && defined('NP_TESTING')) {
        return true;
    }
    return $enabled;
}, 10, 3);
```

**Since:** 2.3.0  
**Parameters:**  
- `bool $enabled` — Whether the feature is currently enabled  
- `string $feature` — Feature slug being checked  
- `string $current_plan` — Active plan slug  
**Return:** `bool`

---

### `nectarpress/byline/schema`

Filters the Schema.org `author` array for a post's NewsArticle JSON-LD.

```php
add_filter('nectarpress/byline/schema', function(
    array $schema_authors,
    int   $post_id
): array {
    // Add organisation as co-author
    $schema_authors[] = [
        '@type'  => 'Organization',
        'name'   => 'My News Agency',
        'url'    => 'https://example.com',
    ];
    return $schema_authors;
}, 10, 2);
```

**Since:** 2.5.0  
**Parameters:**  
- `array $schema_authors` — Array of Schema.org Person objects  
- `int $post_id` — Post ID  
**Return:** `array`

---

### `nectarpress/nr/correction_types`

Filters the available correction types. Add custom types to the editorial corrections system.

```php
add_filter('nectarpress/nr/correction_types', function(array $types): array {
    $types['factual_update'] = [
        'label'    => __('Factual Update', 'my-plugin'),
        'label_ne' => 'तथ्य अद्यावधिक',
        'severity' => 'moderate',
    ];
    return $types;
});
```

**Since:** 2.5.0  
**Parameters:**  
- `array $types` — Current correction type definitions  
**Return:** `array`

---

### `nectarpress/paywall/should_gate`

Filters whether a specific post should be paywalled. Return `false` to bypass
the paywall for specific content (e.g., press releases, public-interest articles).

```php
add_filter('nectarpress/paywall/should_gate', function(
    bool $gate,
    int  $post_id
): bool {
    // Never gate sponsored content
    if (has_category('sponsored', $post_id)) {
        return false;
    }
    return $gate;
}, 10, 2);
```

**Since:** 2.3.0  
**Parameters:**  
- `bool $gate` — Whether to apply the paywall (default: determined by tier/count)  
- `int $post_id` — Post being checked  
**Return:** `bool`

---

### `nectarpress/csp/directives`

Filters the Content Security Policy directives before output.

```php
add_filter('nectarpress/csp/directives', function(array $directives): array {
    // Allow an embedded widget from a trusted domain
    $directives['script-src'][] = "'https://widget.trusted.com'";
    $directives['frame-src'][]  = 'https://widget.trusted.com';
    return $directives;
});
```

**Since:** 2.4.0  
**Parameters:**  
- `array $directives` — Map of CSP directive => array of sources  
**Return:** `array`

---

### `nectarpress/perf/resource_hints`

Filters the `<link rel="preconnect|dns-prefetch">` hints emitted in `<head>`.

```php
add_filter('nectarpress/perf/resource_hints', function(array $hints): array {
    // Add a custom CDN origin
    $hints['https://cdn.mysite.com'] = ['preconnect'];
    return $hints;
});
```

**Since:** 2.7.0  
**Parameters:**  
- `array $hints` — Map of origin => array of hint types  
**Return:** `array`

---

### `nectarpress/date_format`

Filters the date format string used by `nectarpress_format_date()`.

```php
add_filter('nectarpress/date_format', function(
    string $format,
    string $context
): string {
    // Use AD-only format in JSON-LD (machines don't need BS dates)
    if ($context === 'schema') {
        return 'ad';
    }
    return $format;
}, 10, 2);
```

**Since:** 2.7.0  
**Parameters:**  
- `string $format` — Current format ('bs', 'ad', or 'both')  
- `string $context` — Where the date is being displayed  
**Return:** `string`
