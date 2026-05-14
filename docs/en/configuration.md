# Configuration

This reference explains the dTui Editor settings that control editor behavior,
plugins, upload paths, themes, and integration routes.

## Published Config

After publishing, runtime settings live in:

```text
core/custom/config/cms/settings/dTuiEditor.php
```

Package defaults live in:

```text
config/dTuiEditorSettings.php
```

## Core Defaults

| Key | Default | Meaning |
| --- | --- | --- |
| `default_profile` | `full` | Profile used when a field does not override it. |
| `default_theme` | `auto` | Follows the manager theme unless overridden. |
| `default_editor_mode` | `wysiwyg` | Initial mode: `markdown`, `split`, or `wysiwyg`. |
| `default_height` | `500px` | Default editor height. |
| `default_preview_style` | `vertical` | Preview layout used for split editing. |
| `usage_statistics` | `false` | Keeps TOAST UI telemetry disabled. |

## Profiles

Profiles define toolbar groups, TOAST UI options, and plugin enablement.

| Profile | Purpose |
| --- | --- |
| `full` | Full manager editor with images, EVO links, UML, Prism, colors, and tables. |
| `mini` | Compact editor for shorter rich text fields. |
| `introtext` | Smaller editor for intro text. |
| `custom` | Starting point for site-specific overrides. |

## Plugin Settings

| Plugin | Default | Notes |
| --- | --- | --- |
| `codeSyntaxHighlight` | enabled | Uses the local Prism language bundle. |
| `colorSyntax` | enabled | Adds text color controls. |
| `tableMergedCell` | enabled | Enables merged table cells. |
| `uml` | enabled | Renders editable PlantUML blocks. |
| `image` | enabled | Adds Evolution image browser and paste upload support. |
| `evolinks` | enabled | Adds the EVO resource link picker. |
| `chart` | disabled | Disabled by default because it can hang manager pages. |

## Image Uploads

Clipboard uploads use `plugins.image.options.uploadPath`. The path must be a
safe relative path under the Evolution base directory.

```php
'uploadPath' => 'assets/images',
```

Set `pasteUpload` to `false` to keep the image picker while disabling clipboard
uploads.

## Theme Values

| Value | Behavior |
| --- | --- |
| `auto` | Detects Evolution manager theme state. |
| `lightness` | Optimized for the lightness manager theme. |
| `light` | Standard light editor surface. |
| `dark` | Dark editor surface. |
| `darkness` | High-contrast dark manager surface. |

Use `auto` for shared packages. Use a fixed theme only when a project has a
known manager theme and needs strict visual parity.

## Routes

Route keys are relative URLs passed to the browser runtime.

```php
'routes' => [
    'evo_link_search' => 'dtui-evo-link-search',
    'image_upload' => 'dtui-image-upload',
    'plantuml_renderer' => 'dtui-plantuml',
],
```

## Prism Languages

The default visible code languages are:

```php
['html', 'css', 'scss', 'javascript', 'typescript', 'php', 'blade', 'sql', 'json', 'markdown', 'bash', 'yaml']
```

Aliases such as `js`, `ts`, `laravel-blade`, `bladephp`, `md`, `sh`, `shell`,
`yml`, `markup`, and `xml` are supported without expanding the visible list.

## Safety Notes

- Keep chart disabled unless a project explicitly accepts the manager
  performance risk.
- Keep `usage_statistics` disabled for manager privacy and offline stability.
- Keep upload paths relative; absolute paths and traversal segments are rejected.
- Keep language bundles small to avoid Safari and manager iframe hangs.
