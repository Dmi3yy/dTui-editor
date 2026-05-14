# Referenz

Diese Seite sammelt Integrationsnamen, Routes, Events, Formate und Runtime APIs
von dTui Editor.

## Composer package

```text
dmi3yy/dtui-editor
```

## Rich editor name

```text
dTuiEditor
```

## Evolution events

| Event | Verhalten |
| --- | --- |
| `OnRichTextEditorRegister` | Fügt `dTuiEditor` zur Editorliste hinzu. |
| `OnRichTextEditorInit` | Lädt Assets und erstellt Field boot config. |

## Published files

| Publish tag | Output |
| --- | --- |
| `dtui-editor-config` | `core/custom/config/cms/settings/dTuiEditor.php` und `which_editor.php`. |
| `dtui-editor-assets` | `assets/plugins/dTui.editor`. |

## Config and diagnostics

| Signal | Bedeutung |
| --- | --- |
| `cms.settings` | Evolution settings namespace für published manager config. |
| `dTuiEditorCheck` | Config check file für Evolution interface settings. |
| `dTuiEditorSettings` | Main package defaults file. |

## Routes

| Route | Zweck |
| --- | --- |
| `dtui-evo-link-search` | Sucht Evolution resources für den EVO link dialog. |
| `dtui-image-upload` | Empfängt Bilder aus der Zwischenablage. |
| `dtui-plantuml` | Fügt dark PlantUML skin hinzu und leitet zum renderer weiter. |

## Speicherformate

| Feature | Format |
| --- | --- |
| Resource link | Placeholder `[~12~]`. |
| Clipboard image | HTML image nach Upload. |
| UML | `figure.dtui-uml` mit base64url source in `data-uml`. |
| Content field | Clean HTML vor manager save. |

## evo-ui bridge names

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
