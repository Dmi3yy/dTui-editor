# Konfiguration

Diese Referenz beschreibt die dTui Editor Einstellungen für Editor-Modi,
Plugins, Upload-Pfade, Themes und Integrations-Routes.

## Veröffentlichte Config

Runtime-Config nach dem Publishing:

```text
core/custom/config/cms/settings/dTuiEditor.php
```

Paket-Defaults:

```text
config/dTuiEditorSettings.php
```

## Grundoptionen

| Schlüssel | Default | Bedeutung |
| --- | --- | --- |
| `default_profile` | `full` | Profil für Felder ohne Override. |
| `default_theme` | `auto` | Folgt dem Manager-Theme. |
| `default_editor_mode` | `wysiwyg` | Startmodus: `markdown`, `split` oder `wysiwyg`. |
| `default_height` | `500px` | Standardhöhe des Editors. |
| `default_preview_style` | `vertical` | Preview-Layout für split editing. |
| `usage_statistics` | `false` | Deaktiviert TOAST UI telemetry. |

## Profile

| Profil | Zweck |
| --- | --- |
| `full` | Voller Editor mit images, EVO links, UML, Prism, colors und tables. |
| `mini` | Kompakter Editor für kürzere Felder. |
| `introtext` | Kleiner Editor für intro text. |
| `custom` | Basis für project-specific overrides. |

## Plugins

| Plugin | Default | Hinweis |
| --- | --- | --- |
| `codeSyntaxHighlight` | enabled | Lokales Prism language bundle. |
| `colorSyntax` | enabled | Text color controls. |
| `tableMergedCell` | enabled | Merged table cells. |
| `uml` | enabled | Editierbare PlantUML blocks. |
| `image` | enabled | Evolution image browser und paste upload. |
| `evolinks` | enabled | EVO resource link picker. |
| `chart` | disabled | Deaktiviert, weil es Manager-Seiten blockieren kann. |

## Bild-Uploads

Clipboard uploads verwenden `plugins.image.options.uploadPath`. Der Pfad muss
sicher und relativ zum Evolution base directory sein.

```php
'uploadPath' => 'assets/images',
```

## Theme values

| Value | Verhalten |
| --- | --- |
| `auto` | Erkennt den theme state des Evolution managers. |
| `lightness` | Für lightness manager theme. |
| `light` | Standard light editor surface. |
| `dark` | Dark editor surface. |
| `darkness` | High-contrast dark manager surface. |

## Routes

```php
'routes' => [
    'evo_link_search' => 'dtui-evo-link-search',
    'image_upload' => 'dtui-image-upload',
    'plantuml_renderer' => 'dtui-plantuml',
],
```

## Prism-Sprachen

```php
['html', 'css', 'scss', 'javascript', 'typescript', 'php', 'blade', 'sql', 'json', 'markdown', 'bash', 'yaml']
```

Aliases wie `js`, `ts`, `laravel-blade`, `bladephp`, `md`, `sh`, `shell`,
`yml`, `markup` und `xml` werden unterstützt.

## Safety notes

- Chart bleibt disabled, außer ein Projekt akzeptiert das performance risk.
- `usage_statistics` sollte disabled bleiben.
- Upload paths müssen relative sein; absolute paths und traversal segments
  werden abgelehnt.
- Das Prism language bundle sollte klein bleiben, damit Safari und manager iframe
  nicht blockieren.
