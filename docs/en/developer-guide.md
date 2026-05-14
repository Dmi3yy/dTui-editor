# Developer Guide

## Architecture

dTui Editor is an Evolution CMS package that registers itself as a manager rich
text editor through Evolution events. PHP publishes config and assets, while the
browser runtime creates TOAST UI Editor instances for matching textareas.

Core pieces:

- `EvolutionCMS\dTuiEditor\DTuiEditorServiceProvider` registers config, routes,
  views, and publish groups.
- `plugins/dTuiEditorPlugin.php` registers `dTuiEditor`, injects assets, and
  emits per-field boot config.
- `public/js/dtui-init.js` owns editor creation, mode switching, HTML/Markdown
  roundtrip, sync, Prism setup, and UML restoration.
- `src/Http/routes.php` exposes AJAX endpoints for EVO links, image upload, and
  dark-theme PlantUML redirects.

## Installation

From the Evolution `core` directory:

```sh
php artisan package:installrequire dmi3yy/dtui-editor "*"
php artisan vendor:publish --provider="EvolutionCMS\dTuiEditor\DTuiEditorServiceProvider"
```

For local Extras development, use a path repository or symlink install and then
republish config/assets.

## Configuration

Default config lives in:

```text
config/dTuiEditorSettings.php
```

Published runtime config lives in:

```text
core/custom/config/cms/settings/dTuiEditor.php
```

Important settings:

```php
'default_profile' => 'full',
'default_theme' => 'auto',
'default_editor_mode' => 'wysiwyg',
'default_preview_style' => 'vertical',
'usage_statistics' => false,
```

Profiles control toolbar items, TOAST UI options, and enabled plugins. Plugin
settings live under `plugins`.

## Routes

The package registers three manager-facing routes:

```php
'evo_link_search' => 'dtui-evo-link-search',
'image_upload' => 'dtui-image-upload',
'plantuml_renderer' => 'dtui-plantuml',
```

`dtui-image-upload` writes pasted clipboard images to the configured safe
relative `uploadPath`. `dtui-plantuml` rewrites PlantUML diagrams for dark manager
themes before redirecting to the configured renderer.

## Assets

Runtime assets are self-hosted under:

```text
assets/plugins/dTui.editor
```

The package intentionally ships a small Prism language bundle instead of the full
Prism all-languages build. Supported visible languages are configured in
`plugins.codeSyntaxHighlight.options.languages`.

Republish assets after changing files under `public/`:

```sh
php artisan vendor:publish --provider="EvolutionCMS\dTuiEditor\DTuiEditorServiceProvider" --tag=dtui-editor-assets --force
```

Republish config after changing package defaults:

```sh
php artisan vendor:publish --provider="EvolutionCMS\dTuiEditor\DTuiEditorServiceProvider" --tag=dtui-editor-config --force
```

## evo-ui And dDocs Boundary

dTui Editor remains the owner of TOAST UI assets, profiles, Prism languages,
image handling, EVO links, UML, and HTML cleanup. evo-ui owns only the generic
rich text field lifecycle: field markers, initialization, sync, clear, and media
picker bridge helpers.

dDocs can use dTui as a Markdown editor/viewer dependency, but documentation
content remains file-first Markdown under `docs/`. Do not store canonical docs in
database fields.

## JavaScript API

The runtime API is available as `window.dTuiEditor`:

```js
window.dTuiEditor.boot(config);
window.dTuiEditor.enqueue(config);
window.dTuiEditor.flush();
window.dTuiEditor.get(idOrTextareaName);
window.dTuiEditor.sync(idOrTextareaName);
window.dTuiEditor.getValue(idOrTextareaName);
window.dTuiEditor.setMode(idOrTextareaName, 'split');
window.dTuiEditor.remove(idOrTextareaName);
```

Use `sync` before reading the original textarea manually.

## Theme Behavior

Theme mode can be `auto`, `lightness`, `light`, `dark`, or `darkness`. In `auto`
mode the browser runtime follows Evolution manager theme cookies/classes. CSS
covers toolbar density, button states, dialogs, editor surfaces, code blocks, and
Prism tokens for dark themes.

When adding styles, scope them under dTui classes and avoid global overrides of
TOAST UI or evo-ui primitives. Manager packages should be able to load dTui
without changing unrelated form, table, or modal surfaces.

## Development Checks

Useful local checks:

```sh
php -l config/dTuiEditorSettings.php
php -l plugins/dTuiEditorPlugin.php
php -l src/Http/routes.php
node --check public/js/dtui-init.js
node --check public/vendor/prism-evo-languages.min.js
```

For demo smoke tests, open the Evolution manager and verify one normal resource
field and one repeated rich text field.
