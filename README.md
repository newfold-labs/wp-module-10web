# WordPress 10Web Module

Hidden Newfold module that loads PostHog session replay on the WVC editor admin screen (`admin.php?page=wvc-editor`).

## Module Responsibilities

- Register with the Newfold Module Loader as a hidden, always-active module.
- Enqueue a bundled PostHog script on the WVC editor screen only.
- Restrict theme switching and plugin access when the `wvc-theme` is active.
- Provide no user-facing UI of its own.

## Key Paths

| Purpose | Location |
|---------|----------|
| Bootstrap / registration | `bootstrap.php` |
| Module bootstrap | `includes/TenWeb.php` |
| Admin restrictions | `includes/AdminRestrictions.php` |
| Editor asset loading | `includes/EditorSupport.php` |
| PostHog entry point | `src/editor-support/index.js` |
| Built assets | `build/editor-support/` |
| Translations | `languages/` |

## Development

```bash
composer install
composer run lint
composer run i18n

npm install
npm run build
npm run lint
npm run start
```

Run `npm run build` after changing `src/` and commit the updated files in `build/`.

Run `composer run i18n` after changing user-facing strings in PHP. The text domain is `wp-module-10web` and translation files live in `languages/`.

## Admin Restrictions

When `wvc-theme` is active, the module locks down WP Admin for customers:

- Theme switching is disabled (menus removed, capability blocked, `pre_set_theme` locked to `wvc-theme`)
- The Plugins admin page is inaccessible
- Only approved plugins can load or appear in admin:
  - Yoast SEO (`wordpress-seo/wp-seo.php` or `wordpress-seo-premium/wp-seo-premium.php`)
  - WooCommerce (`woocommerce/woocommerce.php`)
  - Host brand plugin (resolved from the module container)
  - 10Web Manager (`10web-manager/10web-manager.php`)

Disable restrictions locally with:

```php
add_filter( 'nfd_tenweb_admin_restrictions_enabled', '__return_false' );
```

Customize the approved plugin list with the `nfd_tenweb_approved_plugins` filter.

## Releases

1. Bump `NFD_TENWEB_MODULE_VERSION` in `bootstrap.php` and the `version` in `package.json`.
2. Run `npm run build` and `composer run i18n`.
3. Commit the updated build and translation artifacts.

## Installation

### 1. Add the Newfold Satis to your `composer.json`.

```bash
composer config repositories.newfold composer https://newfold-labs.github.io/satis
```

### 2. Require the `newfold-labs/wp-module-10web` package.

```bash
composer require newfold-labs/wp-module-10web
```

The host plugin must already load the Newfold Module Loader. This module registers itself on `plugins_loaded` and requires no additional setup.

[More on Newfold WordPress Modules](https://github.com/newfold-labs/wp-module-loader)
