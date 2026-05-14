# Konfiguracja

Ten dokument opisuje ustawienia dTui Editor odpowiedzialne za tryby edytora,
pluginy, ścieżki uploadu, motywy i routes integracyjne.

## Opublikowany config

Runtime config po publikacji:

```text
core/custom/config/cms/settings/dTuiEditor.php
```

Domyślne ustawienia pakietu:

```text
config/dTuiEditorSettings.php
```

## Podstawowe opcje

| Klucz | Default | Znaczenie |
| --- | --- | --- |
| `default_profile` | `full` | Profil dla pól bez override. |
| `default_theme` | `auto` | Podąża za motywem managera. |
| `default_editor_mode` | `wysiwyg` | Startowy tryb: `markdown`, `split` lub `wysiwyg`. |
| `default_height` | `500px` | Domyślna wysokość edytora. |
| `default_preview_style` | `vertical` | Układ preview dla split editing. |
| `usage_statistics` | `false` | Wyłącza telemetrię TOAST UI. |

## Profile

| Profil | Cel |
| --- | --- |
| `full` | Pełny editor z images, EVO links, UML, Prism, colors i tables. |
| `mini` | Kompaktowy editor dla krótszych pól. |
| `introtext` | Mniejszy editor dla intro text. |
| `custom` | Baza dla project-specific overrides. |

## Pluginy

| Plugin | Default | Uwagi |
| --- | --- | --- |
| `codeSyntaxHighlight` | enabled | Lokalny Prism language bundle. |
| `colorSyntax` | enabled | Kontrolki koloru tekstu. |
| `tableMergedCell` | enabled | Scalanie komórek tabel. |
| `uml` | enabled | Edytowalne bloki PlantUML. |
| `image` | enabled | Evolution image browser i paste upload. |
| `evolinks` | enabled | EVO resource link picker. |
| `chart` | disabled | Wyłączony, bo może zawieszać strony managera. |

## Upload obrazów

Clipboard upload używa `plugins.image.options.uploadPath`. Ścieżka musi być
bezpieczna i względna względem Evolution base directory.

```php
'uploadPath' => 'assets/images',
```

## Theme values

| Value | Zachowanie |
| --- | --- |
| `auto` | Wykrywa theme state Evolution manager. |
| `lightness` | Dla lightness manager theme. |
| `light` | Standardowa light editor surface. |
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

## Języki Prism

```php
['html', 'css', 'scss', 'javascript', 'typescript', 'php', 'blade', 'sql', 'json', 'markdown', 'bash', 'yaml']
```

Aliases `js`, `ts`, `laravel-blade`, `bladephp`, `md`, `sh`, `shell`, `yml`,
`markup` i `xml` są obsługiwane.

## Safety notes

- Chart trzymaj disabled, chyba że projekt akceptuje performance risk.
- `usage_statistics` powinno pozostać disabled.
- Upload paths muszą być relative; absolute paths i traversal segments są
  odrzucane.
- Prism language bundle powinien pozostać mały, aby nie blokować Safari i manager
  iframe.
