# Frontend und Adapter

Diese Anleitung erklärt, wie dTui Editor mit browserverwalteten Evolution-Feldern
und dem gemeinsamen evo-ui editor/media adapter contract zusammenarbeitet.

## Verantwortungsgrenze

`dTui Editor` besitzt editor-spezifische Assets und Logik:

- TOAST UI Editor assets;
- profiles und toolbar presets;
- Prism language bundle;
- image paste upload und Evolution image browser button;
- EVO link picker;
- UML plugin und PlantUML dark-theme bridge;
- HTML/Markdown roundtrip cleanup.

`evo-ui` besitzt den generischen Field-Lifecycle:

- `data-evo-rich-editor`;
- `data-evo-rich-editor-model`;
- `EvoUI.initRichEditorField(root)`;
- `EvoUI.syncRichEditors(form, wire)`;
- `EvoUI.clearRichEditors(form)`;
- media picker helpers wie `EvoUI.browseImageField(inputId)`.

Consumer sollen evo-ui markers und sync helpers verwenden und keinen lokalen
Boot-Code für Standard-Rich-Text-Felder schreiben.

## Asset bridge

dTui bleibt Asset Owner. Wenn evo-ui ein Feld mit `dTuiEditor` rendert, soll es
den Evolution rich editor initialization path verwenden.

```php
EvoUI\Support\RichTextEditor::html($ids, '500px', 'dTuiEditor', $options);
```

Der Consumer übergibt field-specific options, kopiert aber keine dTui runtime
logic.

## Field markup contract

Normale evo-ui rich text fields sollten eine textarea mit stable ids und shared
markers rendern:

```html
<textarea
    id="content_body"
    data-evo-rich-editor
    data-evo-rich-editor-model="content.body"
></textarea>
```

Der Consumer besitzt model names und persistence. dTui besitzt die editor
instance, die die textarea erweitert.

## Save flow

Vor dem Speichern eines Formulars mit rich editors:

```js
EvoUI.syncRichEditors(form, wire);
```

Danach liest Livewire oder normaler form persistence den textarea-Wert.

## Consumer-Regeln

- Verwenden Sie `dTuiEditor` für Markdown/WYSIWYG content.
- Storage und validation für Bilder bleiben im consuming module.
- Generic lifecycle code gehört in evo-ui.
- Toolbar, Prism, EVO link und UML logic bleiben in dTui Editor.
- Consumer packages laden keine remote editor assets.

## Release checklist

- Das consumer package nutzt evo-ui markers für ordinary rich text fields.
- Save actions rufen `EvoUI.syncRichEditors` vor Livewire persistence auf.
- Das consumer package dupliziert keinen dTui toolbar, Prism, EVO link, image
  oder UML runtime code.
- dTui assets werden einmal veröffentlicht und über Evolution editor events
  geladen.
