# Reference

This page collects the exact integration names, routes, events, formats, and
runtime APIs used by dTui Editor.

## Composer Package

```text
dmi3yy/dtui-editor
```

## Rich Editor Name

```text
dTuiEditor
```

The plugin registers this name through `OnRichTextEditorRegister`.

## Evolution Events

| Event | Package behavior |
| --- | --- |
| `OnRichTextEditorRegister` | Adds `dTuiEditor` to the editor list. |
| `OnRichTextEditorInit` | Injects assets and creates editor boot config. |

## Published Files

| Publish tag | Output |
| --- | --- |
| `dtui-editor-config` | `core/custom/config/cms/settings/dTuiEditor.php` and `which_editor.php`. |
| `dtui-editor-assets` | `assets/plugins/dTui.editor`. |

## Config And Diagnostics

| Signal | Meaning |
| --- | --- |
| `cms.settings` | Evolution settings namespace used by the published manager config. |
| `dTuiEditorCheck` | Config check file used by Evolution interface settings. |
| `dTuiEditorSettings` | Main package defaults file. |

## Routes

| Route | Purpose |
| --- | --- |
| `dtui-evo-link-search` | Searches Evolution resources for the EVO link dialog. |
| `dtui-image-upload` | Receives pasted clipboard images. |
| `dtui-plantuml` | Applies dark PlantUML skin and redirects to the renderer. |

## Stored Formats

| Feature | Stored format |
| --- | --- |
| Resource link | `[~12~]` placeholder by default. |
| Clipboard image | HTML image from uploaded Markdown image. |
| UML | HTML `figure.dtui-uml` with base64url source in `data-uml`. |
| Content field | Clean HTML serialized before manager save. |

## evo-ui Bridge Names

| Name | Owner |
| --- | --- |
| `data-evo-rich-editor` | evo-ui field marker. |
| `data-evo-rich-editor-model` | evo-ui field/model mapping. |
| `EvoUI.syncRichEditors(form, wire)` | evo-ui sync bridge. |
| `window.dTuiEditor.sync(idOrName)` | dTui runtime sync. |
| `EvoUI\Support\RichTextEditor::html(...)` | Server-side evo-ui rich editor bridge. |

## JavaScript API

```js
window.dTuiEditor.boot(config);
window.dTuiEditor.enqueue(config);
window.dTuiEditor.flush();
window.dTuiEditor.get(idOrTextareaName);
window.dTuiEditor.sync(idOrTextareaName);
window.dTuiEditor.getValue(idOrTextareaName);
window.dTuiEditor.setMode(idOrTextareaName, 'markdown');
window.dTuiEditor.remove(idOrTextareaName);
```

## Editor Modes

| Mode | Meaning |
| --- | --- |
| `markdown` | Markdown editor only. |
| `split` | Markdown editor with live preview. |
| `wysiwyg` | Visual WYSIWYG editor only. |
