# Frontend And Adapter Guide

This guide explains how dTui Editor fits into browser-managed Evolution fields
and the shared evo-ui editor/media adapter contract.

## Ownership Boundary

`dTui Editor` owns editor-specific assets and behavior:

- TOAST UI Editor assets;
- profiles and toolbar presets;
- Prism language bundle;
- image paste upload and Evolution image browser button;
- EVO link picker;
- UML plugin and PlantUML dark-theme bridge;
- HTML/Markdown roundtrip cleanup.

`evo-ui` owns generic field lifecycle behavior:

- `data-evo-rich-editor` field markers;
- `data-evo-rich-editor-model` model mapping;
- `EvoUI.initRichEditorField(root)`;
- `EvoUI.syncRichEditors(form, wire)`;
- `EvoUI.clearRichEditors(form)`;
- media picker helpers such as `EvoUI.browseImageField(inputId)`.

Consumers should use evo-ui field markers and sync helpers for ordinary rich
text fields. They should not write module-local boot code for standard editor
fields.

## Asset Bridge

dTui remains the asset owner. When evo-ui renders a field configured for
`dTuiEditor`, it should call Evolution's rich editor initialization path. The
dTui plugin then injects its CSS/JS once and queues field boot configs.

```php
EvoUI\Support\RichTextEditor::html($ids, '500px', 'dTuiEditor', $options);
```

The bridge should pass field-specific options rather than copying dTui runtime
logic into the consumer package.

## Field Markup Contract

Ordinary evo-ui rich text fields should render a textarea with stable ids and the
shared markers:

```html
<textarea
    id="content_body"
    data-evo-rich-editor
    data-evo-rich-editor-model="content.body"
></textarea>
```

The consumer owns model names and persistence. dTui owns the editor instance that
enhances the textarea.

## Save Flow

Before saving a form that contains rich editors, call:

```js
EvoUI.syncRichEditors(form, wire);
```

This gives dTui Editor a chance to serialize TOAST UI content back into the
original textarea. Livewire or normal form persistence should then read the
textarea value.

## Consumer Rules

- Use `dTuiEditor` as the editor name when Markdown/WYSIWYG content is needed.
- Keep image storage and validation in the consuming module.
- Keep generic lifecycle code in evo-ui.
- Keep dTui-specific toolbar, Prism, EVO link, and UML logic in this package.
- Do not load remote editor assets from consumer packages.

## Release Checklist

- The consuming package uses evo-ui markers for ordinary rich text fields.
- Save actions call `EvoUI.syncRichEditors` before Livewire persistence.
- No consumer package duplicates dTui toolbar, Prism, EVO link, image, or UML
  runtime code.
- dTui assets are published once and loaded through Evolution editor events.
