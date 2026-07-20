# Agent guidance – wp-module-10web

This file gives AI agents a quick orientation to the repo.

## What this project is

- **wp-module-10web** – Hidden Newfold module for 10Web/WVC theme integrations. Currently loads a PostHog session replay bundle on the WVC editor admin screen. Maintained by Newfold Labs.

- **Stack:** PHP 7.4+. JS built with `@wordpress/scripts`. Expects the Newfold Module Loader from the host plugin.

- **Architecture:** Registers via `NewfoldLabs\WP\ModuleLoader\register()` on `plugins_loaded`. Defines `NFD_TENWEB_*` constants in the registration callback, then instantiates `TenWeb`, which loads the text domain and boots `AdminRestrictions` and `EditorSupport`.

## Key paths

| Purpose | Location |
|---------|----------|
| Bootstrap | `bootstrap.php` |
| Module bootstrap | `includes/TenWeb.php` |
| Admin restrictions | `includes/AdminRestrictions.php` |
| Editor asset loading | `includes/EditorSupport.php` |
| JS source | `src/editor-support/index.js` |
| Build output | `build/editor-support/` |
| Webpack config | `scripts/webpack.config.js` |
| Translations | `languages/` |

## Essential commands

```bash
composer install
composer run lint
composer run fix
composer run i18n

npm install
npm run build
npm run lint
npm run start
```

## Documentation

- **README.md** – human-oriented setup, development, and release notes.
- **CLAUDE.md** – symlink to this file when present.

---

## Keeping documentation current

When you change code, features, or workflows, update **README.md** and this file. After JS changes, run `npm run build` and commit `build/`. When cutting a release, bump `NFD_TENWEB_MODULE_VERSION` in `bootstrap.php` and `package.json`.
