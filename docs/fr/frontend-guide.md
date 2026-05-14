# Frontend et adaptateur

Ce guide explique comment dTui Editor s'intègre aux champs Evolution gérés dans
le navigateur et au contrat partagé evo-ui editor/media adapter.

## Limite de responsabilité

`dTui Editor` possède les assets et comportements spécifiques à l'éditeur:

- TOAST UI Editor assets;
- profiles et toolbar presets;
- Prism language bundle;
- image paste upload et bouton Evolution image browser;
- EVO link picker;
- UML plugin et PlantUML dark-theme bridge;
- HTML/Markdown roundtrip cleanup.

`evo-ui` possède le lifecycle générique des champs:

- `data-evo-rich-editor`;
- `data-evo-rich-editor-model`;
- `EvoUI.initRichEditorField(root)`;
- `EvoUI.syncRichEditors(form, wire)`;
- `EvoUI.clearRichEditors(form)`;
- media picker helpers comme `EvoUI.browseImageField(inputId)`.

Les consumers doivent utiliser les markers et sync helpers evo-ui au lieu
d'écrire un boot code local pour les champs rich text standards.

## Asset bridge

dTui reste propriétaire des assets. Quand evo-ui rend un champ avec
`dTuiEditor`, il doit passer par le chemin Evolution rich editor initialization.

```php
EvoUI\Support\RichTextEditor::html($ids, '500px', 'dTuiEditor', $options);
```

Le consumer transmet des field-specific options sans copier la runtime logic dTui.

## Field markup contract

Les champs evo-ui rich text ordinaires doivent rendre une textarea avec stable
ids et shared markers:

```html
<textarea
    id="content_body"
    data-evo-rich-editor
    data-evo-rich-editor-model="content.body"
></textarea>
```

Le consumer possède model names et persistence. dTui possède l'editor instance
qui améliore la textarea.

## Save flow

Avant de sauvegarder un formulaire avec rich editors:

```js
EvoUI.syncRichEditors(form, wire);
```

Ensuite Livewire ou la persistence classique lit la valeur textarea.

## Règles consumer

- Utiliser `dTuiEditor` pour le contenu Markdown/WYSIWYG.
- Garder storage et validation des images dans le consuming module.
- Garder le generic lifecycle code dans evo-ui.
- Garder toolbar, Prism, EVO link et UML logic dans dTui Editor.
- Ne pas charger d'assets editor distants depuis consumer packages.

## Release checklist

- Le consumer package utilise evo-ui markers pour ordinary rich text fields.
- Les save actions appellent `EvoUI.syncRichEditors` avant Livewire persistence.
- Le consumer package ne duplique pas dTui toolbar, Prism, EVO link, image ou UML
  runtime code.
- dTui assets sont publiés une fois et chargés via Evolution editor events.
