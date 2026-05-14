# dTui.editor

TOAST UI Editor 3 integration for Evolution CMS 3.5.7+.

The package replaces the manager rich text editor with a self-hosted TOAST UI Editor build and adds Evolution CMS conveniences on top: EVO resource links, manager image browser support, paste-to-upload images, UML diagrams, syntax highlighting, table merge support, multilingual UI assets, and theme-aware styling for the four Evolution manager themes.

Full dDocs-ready package documentation lives in [`docs/`](docs/README.md).

## Features

- Markdown and WYSIWYG editing through TOAST UI Editor 3.2.2.
- Automatic rich text editor registration as `dTuiEditor`.
- Multiple editor fields on the same manager page, including repeated fields with the same legacy selector/name.
- Existing HTML content is converted into editable Markdown on load, then saved back as HTML for Evolution content fields.
- Evolution resource link picker with live resource search and placeholder output such as `[~12~]`.
- Image insertion from the Evolution manager file browser.
- Image insertion from a direct URL through the default TOAST UI image dialog.
- Clipboard image paste and upload into the configured image directory.
- Editable UML diagrams through the TOAST UI UML plugin and a local Evolution route that can adapt PlantUML output for dark themes.
- Code block syntax highlighting through Prism and `@toast-ui/editor-plugin-code-syntax-highlight`.
- Color syntax, merged table cells, task lists, code blocks, and the standard TOAST UI toolbar.
- Multilingual editor UI packs for the configured manager language.
- Theme modes: `auto`, `lightness`, `light`, `dark`, and `darkness`.
- Compact Evolution-friendly toolbar styling.
- Configurable profiles and per-plugin enable flags from the Evolution interface settings.
- Self-hosted JS/CSS assets; no npm build or CDN is required at runtime.

## Requirements

- PHP 8.3+
- Evolution CMS 3.5.7+
- Composer 2.2+
- A published `assets/plugins/dTui.editor` directory

## Installation

From the Evolution `core` directory:

```sh
php artisan package:installrequire dmi3yy/dtui-editor "*"
```

For local package development, the demo can use a Composer path/symlink install, for example:

```sh
composer require dmi3yy/dtui-editor:* --prefer-source
```

Then publish the config and assets:

```sh
php artisan vendor:publish --provider="EvolutionCMS\dTuiEditor\DTuiEditorServiceProvider"
```

Or publish only one group:

```sh
php artisan vendor:publish --provider="EvolutionCMS\dTuiEditor\DTuiEditorServiceProvider" --tag=dtui-editor-config
php artisan vendor:publish --provider="EvolutionCMS\dTuiEditor\DTuiEditorServiceProvider" --tag=dtui-editor-assets
```

Published files:

- `core/custom/config/cms/settings/dTuiEditor.php`
- `core/custom/config/cms/settings/which_editor.php`
- `assets/plugins/dTui.editor`

After publishing, select `dTuiEditor` as the rich text editor in Evolution manager settings.

## Configuration

The main configuration file is:

```txt
core/custom/config/cms/settings/dTuiEditor.php
```

Important defaults:

```php
'default_profile' => 'full',
'default_theme' => 'auto',
'default_editor_mode' => 'wysiwyg',
'default_height' => '500px',
'default_edit_type' => 'wysiwyg',
'default_preview_style' => 'vertical',
'usage_statistics' => false,
```

## Editor Modes

dTui.editor supports three editor modes through the manager setting `dtui_editor_mode`, the config key `default_editor_mode`, or per-field/profile `editorMode` overrides. This setting chooses the initial state, and the editor renders its own three-button switcher so users can move between modes while editing:

| Mode | Behavior |
| --- | --- |
| `markdown` | Markdown editor only. |
| `split` | Markdown editor with live preview side by side. |
| `wysiwyg` | WYSIWYG editor only. |

Legacy aliases are normalized: `md` and `markdown-only` become `markdown`, `vertical` becomes `split`, and `ww` or `wysiwyg-only` become `wysiwyg`.

Routes used by package features:

```php
'routes' => [
    'evo_link_search' => 'dtui-evo-link-search',
    'image_upload' => 'dtui-image-upload',
    'plantuml_renderer' => 'dtui-plantuml',
],
```

The route values are relative to the site base URL and are passed to the browser during editor initialization.

## Profiles

Profiles define toolbar options, editor options, and the plugin set used for a field.

Bundled profiles:

- `full`: full editor with syntax highlight, color syntax, merged table cells, UML, images, and EVO links.
- `mini`: compact editor with image and EVO link support.
- `introtext`: smaller editor for short content.
- `custom`: base profile for project-specific overrides.

The package removes `scrollSync` from toolbars because it does not fit the Evolution manager layout.

## Plugins

All plugins are configured under `plugins` in `dTuiEditor.php` and can also be exposed as manager interface settings.

Default plugin state:

| Plugin | Default | Purpose |
| --- | --- | --- |
| `codeSyntaxHighlight` | enabled | Prism-based code highlighting for fenced code blocks. |
| `colorSyntax` | enabled | Text color controls. |
| `tableMergedCell` | enabled | Merged cells in TOAST UI tables. |
| `uml` | enabled | PlantUML diagrams through TOAST UI custom blocks. |
| `image` | enabled | Evolution image picker and paste upload integration. |
| `evolinks` | enabled | Evolution resource link picker. |
| `chart` | disabled | Bundled, but disabled by default because it can hang the manager in this integration. |

The chart assets are only loaded if the chart plugin is enabled and included in the active profile.

## EVO Links

The `evolinks` plugin adds an `E` toolbar button. It opens an Evolution resource picker with search and inserts the selected resource as a link.

Default behavior:

- Search starts after `minChars` characters.
- Results are loaded from `dtui-evo-link-search`.
- Output mode is `placeholder`, so links are saved as Evolution placeholders such as `[~2~]`.
- Placeholders are encoded while TOAST UI edits Markdown and decoded again before saving.

Supported output modes:

- `placeholder`: saves `[~id~]`.
- `relative`: saves a site-relative URL.
- `absolute`: saves an absolute URL.

## Images

Images can be inserted in three ways.

1. Paste an image from the clipboard with `Ctrl+V` or `Command+V`.
2. Use the default TOAST UI image dialog and paste a direct image URL.
3. Use the custom `Img` toolbar button to open the Evolution manager image browser.

Clipboard uploads use `dtui-image-upload` and save files under the configured relative path:

```txt
assets/images
```

Uploaded filenames are prefixed with `dtui-` and include a timestamp/hash to avoid collisions.
`uploadPath` is resolved under the Evolution base path and only accepts safe relative path segments.

Set `plugins.image.options.pasteUpload` to `false` to disable clipboard uploads while keeping the image picker. Set `plugins.image.options.uploadPath` to change the upload directory.

## UML

The `uml` plugin adds a toolbar button and supports TOAST UI custom block syntax:

````md
$$uml
Bob->Alice: Hello
$$
````

In light themes, diagrams use the configured PlantUML renderer directly. In `dark` and `darkness`, dTui.editor routes diagrams through:

```txt
dtui-plantuml
```

That route decodes the PlantUML payload, injects a dark PlantUML skin, re-encodes the diagram, and redirects to the configured renderer. Invalid UML payloads return `404`.

dTui.editor keeps UML editable after save. TOAST UI runtime UML blocks are stored as a normal image plus the original PlantUML source:

```html
<figure class="dtui-uml" data-uml="...">
    <img src="/dtui-plantuml/?theme=dark&uml=..." alt="uml">
</figure>
```

`data-uml` stores the original PlantUML source as base64url. When the resource is opened again, the package restores it back to `$$uml ... $$` before the editor is shown, so diagrams are not one-way image exports.

Use `plugins.uml.options.rendererURL` to change the PlantUML renderer. Set `plugins.uml.options.darkTheme` to `false` to always use that renderer directly.

## Code Highlighting

Code highlighting is enabled through Prism. Use fenced code blocks with a language name:

````md
```php
echo 'Hello Evolution CMS';
```
````

Bundled Prism assets are intentionally limited to common web/content languages: HTML/XML, CSS, SCSS, JavaScript, TypeScript, PHP, Blade, SQL, JSON, Markdown, Bash, and YAML. The package uses the lean TOAST UI code syntax plugin with a local Prism highlighter instead of the all-languages bundle, which keeps Safari and Evolution manager pages responsive. Aliases such as `js`, `ts`, `laravel-blade`, `bladephp`, `md`, `sh`, `shell`, `yml`, `markup`, and `xml` still work for fenced code blocks without expanding the visible language list.

Dark manager themes remove Prism text shadows so code stays readable inside Evolution's dark editor surface.

## Languages

dTui.editor maps Evolution manager languages to TOAST UI language packs.

English is built into TOAST UI. Bundled UI packs include common Evolution manager languages such as `uk-UA`, `ru-RU`, `de-DE`, `fr-FR`, `es-ES`, `it-IT`, `pl-PL`, `pt-BR`, `ja-JP`, `ko-KR`, and `zh-CN`. The package first checks `fe_editor_lang`, then `manager_language`, then falls back to `default_language`.

## Themes

Supported manager theme modes are `auto`, `lightness`, `light`, `dark`, and `darkness`. In `auto` mode the editor follows the Evolution manager theme through `EVO_themeMode` and `MODX_themeMode` cookies/classes. The CSS covers toolbar buttons, EVO link dialogs, editor surfaces, code blocks, and Prism tokens for both dark themes.

## HTML and Markdown Roundtrip

Evolution content fields usually store HTML, while TOAST UI edits Markdown internally.

dTui.editor handles that bridge automatically:

- On load, existing HTML is injected with `setHTML`, so the Markdown pane shows editable Markdown instead of raw tags.
- Before save, editor content is serialized with `getHTML`.
- EVO placeholders are decoded back from safe editor URLs.
- Broken escaped image Markdown is normalized to `<img>`.
- TOAST UI runtime attributes such as `contenteditable="false"` and UML helper tool markup are stripped before the value is written back to the original textarea.

This keeps saved Evolution content clean while still giving editors Markdown/WYSIWYG editing.

## JavaScript API

The browser API is exposed as `window.dTuiEditor`.

Available methods:

```js
window.dTuiEditor.boot(config);
window.dTuiEditor.enqueue(config);
window.dTuiEditor.flush();
window.dTuiEditor.get(idOrTextareaName);
window.dTuiEditor.sync(idOrTextareaName);
window.dTuiEditor.getValue(idOrTextareaName);
window.dTuiEditor.setMode(idOrTextareaName, 'markdown'); // markdown, split, wysiwyg
window.dTuiEditor.remove(idOrTextareaName);
```

`boot` is called automatically by the Evolution plugin. The other methods are useful for custom manager screens and dynamic field integrations.

## Assets

Runtime assets are published to:

```txt
assets/plugins/dTui.editor
```

The package loads only the assets needed by the active profile/plugin set and deduplicates each CSS/JS file when multiple editor init events run on one page.

When assets change during development, republish them:

```sh
php artisan vendor:publish --provider="EvolutionCMS\dTuiEditor\DTuiEditorServiceProvider" --tag=dtui-editor-assets --force
```

## Development Smoke Checks

Useful checks before committing package changes:

```sh
php -l src/Http/routes.php
php -l plugins/dTuiEditorPlugin.php
php -l config/dTuiEditorSettings.php
php -l src/DTuiEditorServiceProvider.php
node --check public/js/dtui-init.js
node --check public/js/dtui-image.js
node --check public/js/dtui-evolinks.js
composer validate --no-check-publish
```

Route checks against a local demo:

```sh
curl -I 'http://127.0.0.1:8788/dtui-plantuml/?theme=dark&uml=Syp9J4vLqBLJSCfFibBmICt9oGS0'
curl -I 'http://127.0.0.1:8788/dtui-plantuml/?theme=dark&uml=bad%20input'
curl -X POST 'http://127.0.0.1:8788/dtui-image-upload/'
```

Expected behavior:

- Valid PlantUML input returns a redirect to the configured renderer.
- Invalid PlantUML input returns `404`.
- Unauthenticated POST requests to `dtui-image-upload` return `403`.

## Notes

- TOAST UI chart support is included but disabled by default.
- The package is designed for Evolution manager forms and rich text editor events, not for public frontend rendering.
- If the Evolution image browser is unavailable, the package can fall back to MCPUK when configured.
