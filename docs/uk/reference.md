# Довідник

Ця сторінка збирає точні integration names, routes, events, formats і runtime
API, які використовує dTui Editor.

## Composer package

```text
dmi3yy/dtui-editor
```

## Rich editor name

```text
dTuiEditor
```

Плагін реєструє цю назву через `OnRichTextEditorRegister`.

## Evolution events

| Event | Поведінка пакета |
| --- | --- |
| `OnRichTextEditorRegister` | Додає `dTuiEditor` у список редакторів. |
| `OnRichTextEditorInit` | Інжектить assets і створює boot config полів. |

## Published files

| Publish tag | Output |
| --- | --- |
| `dtui-editor-config` | `core/custom/config/cms/settings/dTuiEditor.php` і `which_editor.php`. |
| `dtui-editor-assets` | `assets/plugins/dTui.editor`. |

## Config and diagnostics

| Signal | Значення |
| --- | --- |
| `cms.settings` | Evolution settings namespace для published manager config. |
| `dTuiEditorCheck` | Config check file для Evolution interface settings. |
| `dTuiEditorSettings` | Main package defaults file. |

## Routes

| Route | Для чого |
| --- | --- |
| `dtui-evo-link-search` | Шукає Evolution resources для EVO link dialog. |
| `dtui-image-upload` | Приймає картинки, вставлені з буфера. |
| `dtui-plantuml` | Додає dark PlantUML skin і робить redirect на renderer. |

## Stored formats

| Feature | Формат збереження |
| --- | --- |
| Resource link | `[~12~]` placeholder за замовчуванням. |
| Clipboard image | HTML image після uploaded Markdown image. |
| UML | HTML `figure.dtui-uml` з base64url source у `data-uml`. |
| Content field | Clean HTML перед manager save. |

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

## Editor modes

| Mode | Значення |
| --- | --- |
| `markdown` | Тільки Markdown editor. |
| `split` | Markdown editor з live preview. |
| `wysiwyg` | Тільки visual WYSIWYG editor. |
