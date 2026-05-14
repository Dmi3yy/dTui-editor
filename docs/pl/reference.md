# Reference

Ta strona zbiera nazwy integracji, routes, events, formaty i runtime API używane
przez dTui Editor.

## Composer package

```text
dmi3yy/dtui-editor
```

## Rich editor name

```text
dTuiEditor
```

## Evolution events

| Event | Zachowanie |
| --- | --- |
| `OnRichTextEditorRegister` | Dodaje `dTuiEditor` do listy edytorów. |
| `OnRichTextEditorInit` | Ładuje assets i tworzy boot config pól. |

## Published files

| Publish tag | Output |
| --- | --- |
| `dtui-editor-config` | `core/custom/config/cms/settings/dTuiEditor.php` i `which_editor.php`. |
| `dtui-editor-assets` | `assets/plugins/dTui.editor`. |

## Config and diagnostics

| Signal | Znaczenie |
| --- | --- |
| `cms.settings` | Evolution settings namespace dla published manager config. |
| `dTuiEditorCheck` | Config check file dla Evolution interface settings. |
| `dTuiEditorSettings` | Main package defaults file. |

## Routes

| Route | Cel |
| --- | --- |
| `dtui-evo-link-search` | Wyszukuje zasoby Evolution dla EVO link dialog. |
| `dtui-image-upload` | Przyjmuje obrazy ze schowka. |
| `dtui-plantuml` | Dodaje ciemny PlantUML skin i przekierowuje do renderer. |

## Format zapisu

| Funkcja | Format |
| --- | --- |
| Resource link | Placeholder `[~12~]`. |
| Clipboard image | HTML image po uploadzie. |
| UML | `figure.dtui-uml` z base64url source w `data-uml`. |
| Content field | Czysty HTML przed manager save. |

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
