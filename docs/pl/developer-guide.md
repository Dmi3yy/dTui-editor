# Przewodnik dewelopera

## Architektura

dTui Editor jest pakietem Evolution CMS rejestrowanym jako manager rich text
editor przez zdarzenia Evolution. PHP publikuje config, routes i assets, a
browser runtime tworzy instancje TOAST UI Editor dla textarea.

Główne elementy:

- `EvolutionCMS\dTuiEditor\DTuiEditorServiceProvider` rejestruje config, routes,
  views i publish groups.
- `plugins/dTuiEditorPlugin.php` rejestruje `dTuiEditor`, ładuje assets i tworzy
  konfigurację pól.
- `public/js/dtui-init.js` obsługuje instancje, przełącznik trybu, roundtrip
  HTML/Markdown, sync, Prism i odtwarzanie UML.
- `src/Http/routes.php` udostępnia EVO links, image upload i PlantUML redirects.

## Instalacja

Z katalogu Evolution `core`:

```sh
php artisan package:installrequire dmi3yy/dtui-editor "*"
php artisan vendor:publish --provider="EvolutionCMS\dTuiEditor\DTuiEditorServiceProvider"
```

## Konfiguracja

Domyślny config:

```text
config/dTuiEditorSettings.php
```

Opublikowany runtime config:

```text
core/custom/config/cms/settings/dTuiEditor.php
```

Najważniejsze opcje:

```php
'default_profile' => 'full',
'default_theme' => 'auto',
'default_editor_mode' => 'wysiwyg',
'default_preview_style' => 'vertical',
'usage_statistics' => false,
```

Profile sterują toolbarem, opcjami TOAST UI i zestawem pluginów.

## Routes

```php
'evo_link_search' => 'dtui-evo-link-search',
'image_upload' => 'dtui-image-upload',
'plantuml_renderer' => 'dtui-plantuml',
```

`dtui-image-upload` zapisuje obrazy w bezpiecznym względnym `uploadPath`.
`dtui-plantuml` dostosowuje diagramy do ciemnych motywów managera.

## Assets i Prism

Assets runtime znajdują się w:

```text
assets/plugins/dTui.editor
```

Bundle Prism jest ograniczony do podstawowych języków web/content. Widoczne
języki są ustawione w `plugins.codeSyntaxHighlight.options.languages`.

Po zmianach w `public/` opublikuj assets ponownie:

```sh
php artisan vendor:publish --provider="EvolutionCMS\dTuiEditor\DTuiEditorServiceProvider" --tag=dtui-editor-assets --force
```

Po zmianach default config opublikuj config ponownie:

```sh
php artisan vendor:publish --provider="EvolutionCMS\dTuiEditor\DTuiEditorServiceProvider" --tag=dtui-editor-config --force
```

## evo-ui i dDocs boundary

dTui Editor pozostaje właścicielem TOAST UI assets, profiles, Prism languages,
image handling, EVO links, UML i HTML cleanup. evo-ui posiada tylko generic rich
text field lifecycle: markers, initialization, sync, clear i media picker bridge
helpers.

dDocs może używać dTui jako Markdown editor/viewer dependency, ale canonical
documentation content pozostaje file-first Markdown w `docs/`.

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

Theme mode może być `auto`, `lightness`, `light`, `dark` albo `darkness`. W
`auto` runtime czyta Evolution manager cookies/classes. CSS obejmuje toolbar,
buttons, dialogs, editor surface, code blocks i Prism tokens dla dark themes.

Nowe style powinny być scoped pod dTui classes i nie powinny globalnie nadpisywać
TOAST UI ani evo-ui primitives.

## Kontrole

```sh
php -l config/dTuiEditorSettings.php
php -l plugins/dTuiEditorPlugin.php
php -l src/Http/routes.php
node --check public/js/dtui-init.js
node --check public/vendor/prism-evo-languages.min.js
```
