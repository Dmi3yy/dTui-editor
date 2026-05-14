# Гайд розробника

## Архітектура

dTui Editor - пакет Evolution CMS, який реєструється як rich text editor через
події менеджера. PHP частина публікує конфіг, routes і assets, а browser runtime
створює TOAST UI Editor instances для textarea.

Ключові частини:

- `EvolutionCMS\dTuiEditor\DTuiEditorServiceProvider` реєструє config, routes,
  views і publish groups.
- `plugins/dTuiEditorPlugin.php` реєструє `dTuiEditor`, додає assets і генерує
  boot config для полів.
- `public/js/dtui-init.js` відповідає за editor creation, mode switcher,
  HTML/Markdown roundtrip, sync, Prism setup і UML restore.
- `src/Http/routes.php` містить AJAX routes для EVO links, image upload і
  PlantUML redirects.

## Встановлення

З директорії Evolution `core`:

```sh
php artisan package:installrequire dmi3yy/dtui-editor "*"
php artisan vendor:publish --provider="EvolutionCMS\dTuiEditor\DTuiEditorServiceProvider"
```

Для локальної Extras розробки використовуйте path repository або symlink install,
після чого перевидайте config/assets.

## Конфігурація

Дефолтний config:

```text
config/dTuiEditorSettings.php
```

Опублікований runtime config:

```text
core/custom/config/cms/settings/dTuiEditor.php
```

Важливі ключі:

```php
'default_profile' => 'full',
'default_theme' => 'auto',
'default_editor_mode' => 'wysiwyg',
'default_preview_style' => 'vertical',
'usage_statistics' => false,
```

Profiles керують toolbar, TOAST UI options і plugin set. Plugin options лежать у
секції `plugins`.

## Routes

Пакет додає три routes:

```php
'evo_link_search' => 'dtui-evo-link-search',
'image_upload' => 'dtui-image-upload',
'plantuml_renderer' => 'dtui-plantuml',
```

`dtui-image-upload` зберігає pasted images у безпечний відносний `uploadPath`.
`dtui-plantuml` додає темну PlantUML skin для dark/darkness тем і робить redirect
на renderer.

## Assets

Runtime assets лежать у:

```text
assets/plugins/dTui.editor
```

Prism bundle навмисно малий: він містить тільки базові web/content мови.
Видимий список мов задається в
`plugins.codeSyntaxHighlight.options.languages`.

Після змін у `public/` перевидайте assets:

```sh
php artisan vendor:publish --provider="EvolutionCMS\dTuiEditor\DTuiEditorServiceProvider" --tag=dtui-editor-assets --force
```

Після змін default config перевидайте config:

```sh
php artisan vendor:publish --provider="EvolutionCMS\dTuiEditor\DTuiEditorServiceProvider" --tag=dtui-editor-config --force
```

## evo-ui та dDocs boundary

dTui Editor лишається owner для TOAST UI assets, profiles, Prism languages, image
handling, EVO links, UML і HTML cleanup. evo-ui володіє тільки generic rich text
field lifecycle: markers, initialization, sync, clear і media picker bridge
helpers.

dDocs може використовувати dTui як Markdown editor/viewer dependency, але
canonical documentation content лишається file-first Markdown у `docs/`. Не
зберігайте canonical docs у database fields.

## JavaScript API

API доступне як `window.dTuiEditor`:

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

Перед ручним читанням textarea викликайте `sync`.

## Теми

Підтримуються `auto`, `lightness`, `light`, `dark` і `darkness`. В `auto` режимі
runtime читає cookies/classes Evolution manager. CSS покриває toolbar, buttons,
dialogs, editor surface, code blocks і Prism tokens для темних тем.

Коли додаєте styles, scope має бути під dTui classes. Не додавайте global
overrides для TOAST UI або evo-ui primitives, щоб dTui не змінював unrelated
forms, tables або modals.

## Перевірки

Корисні локальні checks:

```sh
php -l config/dTuiEditorSettings.php
php -l plugins/dTuiEditorPlugin.php
php -l src/Http/routes.php
node --check public/js/dtui-init.js
node --check public/vendor/prism-evo-languages.min.js
```

Для smoke test відкрийте manager і перевірте звичайне resource field та repeated
rich text fields.
