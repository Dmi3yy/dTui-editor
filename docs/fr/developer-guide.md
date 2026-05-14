# Guide développeur

## Architecture

dTui Editor est un paquet Evolution CMS enregistré comme rich text editor via
les événements du manager. PHP publie config, routes et assets; le runtime
navigateur crée les instances TOAST UI Editor pour les textareas.

Éléments principaux:

- `EvolutionCMS\dTuiEditor\DTuiEditorServiceProvider` enregistre config, routes,
  views et publish groups.
- `plugins/dTuiEditorPlugin.php` enregistre `dTuiEditor`, injecte les assets et
  émet la configuration par champ.
- `public/js/dtui-init.js` gère création, mode switcher, roundtrip HTML/Markdown,
  sync, Prism et restauration UML.
- `src/Http/routes.php` expose EVO links, image upload et PlantUML redirects.

## Installation

Depuis le dossier Evolution `core`:

```sh
php artisan package:installrequire dmi3yy/dtui-editor "*"
php artisan vendor:publish --provider="EvolutionCMS\dTuiEditor\DTuiEditorServiceProvider"
```

## Configuration

Config par défaut:

```text
config/dTuiEditorSettings.php
```

Config runtime publiée:

```text
core/custom/config/cms/settings/dTuiEditor.php
```

Options importantes:

```php
'default_profile' => 'full',
'default_theme' => 'auto',
'default_editor_mode' => 'wysiwyg',
'default_preview_style' => 'vertical',
'usage_statistics' => false,
```

Les profils contrôlent toolbar, options TOAST UI et plugins actifs.

## Routes

```php
'evo_link_search' => 'dtui-evo-link-search',
'image_upload' => 'dtui-image-upload',
'plantuml_renderer' => 'dtui-plantuml',
```

`dtui-image-upload` écrit les images dans un `uploadPath` relatif sûr.
`dtui-plantuml` adapte les diagrammes aux thèmes sombres du manager.

## Assets et Prism

Les assets runtime se trouvent dans:

```text
assets/plugins/dTui.editor
```

Le bundle Prism est volontairement réduit aux langages web/content courants. La
liste visible est définie dans `plugins.codeSyntaxHighlight.options.languages`.

Après modification des fichiers sous `public/`, republiez les assets:

```sh
php artisan vendor:publish --provider="EvolutionCMS\dTuiEditor\DTuiEditorServiceProvider" --tag=dtui-editor-assets --force
```

Après modification des default config, republiez la config:

```sh
php artisan vendor:publish --provider="EvolutionCMS\dTuiEditor\DTuiEditorServiceProvider" --tag=dtui-editor-config --force
```

## evo-ui et dDocs boundary

dTui Editor reste propriétaire de TOAST UI assets, profiles, Prism languages,
image handling, EVO links, UML et HTML cleanup. evo-ui possède seulement le
generic rich text field lifecycle: markers, initialization, sync, clear et media
picker bridge helpers.

dDocs peut utiliser dTui comme Markdown editor/viewer dependency, mais le
canonical documentation content reste du Markdown file-first dans `docs/`.

## API JavaScript

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

Theme mode peut être `auto`, `lightness`, `light`, `dark` ou `darkness`. En
`auto`, le runtime lit les cookies/classes du manager Evolution. Le CSS couvre
toolbar, buttons, dialogs, editor surface, code blocks et Prism tokens pour dark
themes.

Les nouveaux styles doivent être scoped sous les classes dTui et ne doivent pas
ajouter d'overrides globaux de TOAST UI ou evo-ui primitives.

## Vérifications

```sh
php -l config/dTuiEditorSettings.php
php -l plugins/dTuiEditorPlugin.php
php -l src/Http/routes.php
node --check public/js/dtui-init.js
node --check public/vendor/prism-evo-languages.min.js
```
