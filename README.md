# WordPress 10Web Module

Hidden Newfold module that loads PostHog session replay on the WVC editor admin screen (`admin.php?page=wvc-editor`).

## Module Responsibilities

- Register with the Newfold Module Loader as a hidden, always-active module.
- Enqueue a bundled PostHog script on the WVC editor screen only.
- Provide no user-facing UI of its own.

## Key Paths

| Purpose | Location |
|---------|----------|
| Bootstrap / registration | `bootstrap.php` |
| Admin asset loading | `includes/TenWeb.php` |
| PostHog entry point | `src/editor-support/index.js` |
| Built assets | `build/editor-support/` |

## Development

```bash
composer install
composer run lint

npm install
npm run build
npm run lint
npm run start
```

Run `npm run build` after changing `src/` and commit the updated files in `build/`.

## Releases

1. Bump `NFD_TENWEB_MODULE_VERSION` in `bootstrap.php` and the `version` in `package.json`.
2. Run `npm run build`.
3. Commit the updated build artifacts.

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
