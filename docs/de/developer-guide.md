# Entwicklerhandbuch

## Architektur

dTui Editor ist ein Evolution CMS Paket, das sich über Manager-Events als
Rich-Text-Editor registriert. PHP veröffentlicht Config, Routes und Assets; der
Browser-Runtime-Code erstellt TOAST UI Editor Instanzen für passende Textareas.

Kernteile:

- `EvolutionCMS\dTuiEditor\DTuiEditorServiceProvider` registriert config, routes,
  views und publish groups.
- `plugins/dTuiEditorPlugin.php` registriert `dTuiEditor`, lädt Assets und gibt
  Feldkonfigurationen aus.
- `public/js/dtui-init.js` steuert Editor-Erstellung, Moduswechsel,
  HTML/Markdown-Roundtrip, Sync, Prism und UML-Wiederherstellung.
- `src/Http/routes.php` stellt EVO links, image upload und PlantUML redirects
  bereit.

## Installation

Aus dem Evolution `core` Verzeichnis:

```sh
php artisan package:installrequire dmi3yy/dtui-editor "*"
php artisan vendor:publish --provider="EvolutionCMS\dTuiEditor\DTuiEditorServiceProvider"
```

## Konfiguration

Standardconfig:

```text
config/dTuiEditorSettings.php
```

Veröffentlichte Runtime-Config:

```text
core/custom/config/cms/settings/dTuiEditor.php
```

Wichtige Optionen:

```php
'default_profile' => 'full',
'default_theme' => 'auto',
'default_editor_mode' => 'wysiwyg',
'default_preview_style' => 'vertical',
'usage_statistics' => false,
```

Profile steuern Toolbar, TOAST UI options und aktive Plugins.

## Routes

```php
'evo_link_search' => 'dtui-evo-link-search',
'image_upload' => 'dtui-image-upload',
'plantuml_renderer' => 'dtui-plantuml',
```

`dtui-image-upload` speichert Bilder in einem sicheren relativen `uploadPath`.
`dtui-plantuml` passt Diagramme für dunkle Manager-Themes an.

## Assets und Prism

Runtime-Assets liegen unter:

```text
assets/plugins/dTui.editor
```

Das Prism-Bundle ist bewusst klein und enthält nur häufige Web-/Content-Sprachen.
Die sichtbare Liste steht in `plugins.codeSyntaxHighlight.options.languages`.

Nach Änderungen unter `public/` Assets erneut veröffentlichen:

```sh
php artisan vendor:publish --provider="EvolutionCMS\dTuiEditor\DTuiEditorServiceProvider" --tag=dtui-editor-assets --force
```

Nach Änderungen an Default Config die Config erneut veröffentlichen:

```sh
php artisan vendor:publish --provider="EvolutionCMS\dTuiEditor\DTuiEditorServiceProvider" --tag=dtui-editor-config --force
```

## evo-ui und dDocs boundary

dTui Editor bleibt Eigentümer von TOAST UI assets, profiles, Prism languages,
image handling, EVO links, UML und HTML cleanup. evo-ui besitzt nur den generic
rich text field lifecycle: markers, initialization, sync, clear und media picker
bridge helpers.

dDocs kann dTui als Markdown editor/viewer dependency verwenden, aber canonical
documentation content bleibt file-first Markdown unter `docs/`.

## JavaScript API

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

## Theme behavior

Theme mode kann `auto`, `lightness`, `light`, `dark` oder `darkness` sein. In
`auto` liest der runtime Evolution manager cookies/classes. CSS deckt toolbar,
buttons, dialogs, editor surface, code blocks und Prism tokens für dark themes ab.

Neue Styles sollten unter dTui classes scoped sein und keine globalen Overrides
für TOAST UI oder evo-ui primitives erzeugen.

## Checks

```sh
php -l config/dTuiEditorSettings.php
php -l plugins/dTuiEditorPlugin.php
php -l src/Http/routes.php
node --check public/js/dtui-init.js
node --check public/vendor/prism-evo-languages.min.js
```
