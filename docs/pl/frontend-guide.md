# Frontend i adapter

Ten przewodnik opisuje współpracę dTui Editor z polami Evolution zarządzanymi w
przeglądarce oraz z kontraktem evo-ui editor/media adapter.

## Granica odpowiedzialności

`dTui Editor` jest właścicielem elementów specyficznych dla edytora:

- TOAST UI Editor assets;
- profiles i toolbar presets;
- Prism language bundle;
- image paste upload i przycisk Evolution image browser;
- EVO link picker;
- UML plugin i PlantUML dark-theme bridge;
- HTML/Markdown roundtrip cleanup.

`evo-ui` jest właścicielem ogólnego lifecycle pól:

- `data-evo-rich-editor`;
- `data-evo-rich-editor-model`;
- `EvoUI.initRichEditorField(root)`;
- `EvoUI.syncRichEditors(form, wire)`;
- `EvoUI.clearRichEditors(form)`;
- media picker helpers, na przykład `EvoUI.browseImageField(inputId)`.

Consumers powinny używać markerów i sync helpers z evo-ui zamiast pisać lokalny
boot code dla standardowych rich text fields.

## Asset bridge

dTui pozostaje właścicielem assets. Gdy evo-ui renderuje pole z `dTuiEditor`,
powinno przejść przez ścieżkę Evolution rich editor initialization.

```php
EvoUI\Support\RichTextEditor::html($ids, '500px', 'dTuiEditor', $options);
```

Consumer przekazuje field-specific options, ale nie kopiuje logiki runtime dTui.

## Field markup contract

Standardowe evo-ui rich text fields powinny renderować textarea ze stable ids i
shared markers:

```html
<textarea
    id="content_body"
    data-evo-rich-editor
    data-evo-rich-editor-model="content.body"
></textarea>
```

Consumer posiada model names i persistence. dTui posiada editor instance, który
wzbogaca textarea.

## Save flow

Przed zapisem formularza z rich editors:

```js
EvoUI.syncRichEditors(form, wire);
```

Po synchronizacji Livewire lub normalny form persistence czyta wartość textarea.

## Reguły consumerów

- Używaj `dTuiEditor` dla Markdown/WYSIWYG content.
- Storage i validation obrazów zostają w module consumer.
- Generic lifecycle code należy do evo-ui.
- Toolbar, Prism, EVO link i UML logic pozostają w dTui Editor.
- Consumer packages nie ładują remote editor assets.

## Release checklist

- Consumer package używa evo-ui markers dla ordinary rich text fields.
- Save actions wywołują `EvoUI.syncRichEditors` przed Livewire persistence.
- Consumer package nie duplikuje dTui toolbar, Prism, EVO link, image ani UML
  runtime code.
- dTui assets są published raz i ładowane przez Evolution editor events.
