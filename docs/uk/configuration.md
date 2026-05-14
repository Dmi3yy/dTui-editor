# Конфігурація

Цей довідник описує налаштування dTui Editor, які керують режимами редактора,
плагінами, шляхами upload, темами та integration routes.

## Опублікований config

Після publish runtime config знаходиться тут:

```text
core/custom/config/cms/settings/dTuiEditor.php
```

Дефолти пакета:

```text
config/dTuiEditorSettings.php
```

## Core defaults

| Ключ | Default | Значення |
| --- | --- | --- |
| `default_profile` | `full` | Профіль для полів без override. |
| `default_theme` | `auto` | Слідує темі manager, якщо не задано інше. |
| `default_editor_mode` | `wysiwyg` | Початковий режим: `markdown`, `split` або `wysiwyg`. |
| `default_height` | `500px` | Висота редактора за замовчуванням. |
| `default_preview_style` | `vertical` | Preview layout для split editing. |
| `usage_statistics` | `false` | Вимикає TOAST UI telemetry. |

## Profiles

Profiles визначають toolbar, TOAST UI options і набір плагінів.

| Profile | Для чого |
| --- | --- |
| `full` | Повний manager editor з images, EVO links, UML, Prism, colors і tables. |
| `mini` | Компактний editor для коротших rich text полів. |
| `introtext` | Менший editor для intro text. |
| `custom` | База для project-specific overrides. |

## Plugin settings

| Plugin | Default | Примітка |
| --- | --- | --- |
| `codeSyntaxHighlight` | enabled | Використовує локальний Prism language bundle. |
| `colorSyntax` | enabled | Додає text color controls. |
| `tableMergedCell` | enabled | Вмикає merged table cells. |
| `uml` | enabled | Рендерить редаговані PlantUML blocks. |
| `image` | enabled | Додає Evolution image browser і paste upload. |
| `evolinks` | enabled | Додає EVO resource link picker. |
| `chart` | disabled | Вимкнено, бо може підвішувати manager pages. |

## Image uploads

Clipboard uploads використовують `plugins.image.options.uploadPath`. Шлях має
бути безпечним відносним шляхом від Evolution base directory.

```php
'uploadPath' => 'assets/images',
```

Встановіть `pasteUpload` у `false`, щоб залишити image picker, але вимкнути
clipboard uploads.

## Theme values

| Value | Поведінка |
| --- | --- |
| `auto` | Визначає theme state Evolution manager. |
| `lightness` | Оптимізовано для lightness manager theme. |
| `light` | Стандартна light editor surface. |
| `dark` | Dark editor surface. |
| `darkness` | High-contrast dark manager surface. |

Для shared packages використовуйте `auto`. Fixed theme має сенс тільки для
проєктів із жорстко визначеною manager theme.

## Routes

Route keys передаються в browser runtime як відносні URL.

```php
'routes' => [
    'evo_link_search' => 'dtui-evo-link-search',
    'image_upload' => 'dtui-image-upload',
    'plantuml_renderer' => 'dtui-plantuml',
],
```

## Prism languages

Visible code languages за замовчуванням:

```php
['html', 'css', 'scss', 'javascript', 'typescript', 'php', 'blade', 'sql', 'json', 'markdown', 'bash', 'yaml']
```

Aliases `js`, `ts`, `laravel-blade`, `bladephp`, `md`, `sh`, `shell`, `yml`,
`markup` і `xml` працюють без розширення видимого списку.

## Safety notes

- Тримайте chart disabled, якщо проєкт явно не прийняв performance risk.
- Тримайте `usage_statistics` disabled для privacy і offline stability.
- Upload paths мають бути relative; absolute paths і traversal segments
  відхиляються.
- Language bundle має лишатися малим, щоб не підвішувати Safari і manager iframe.
