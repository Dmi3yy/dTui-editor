# Référence

Cette page regroupe les noms d'intégration, routes, events, formats et API
runtime utilisés par dTui Editor.

## Composer package

```text
dmi3yy/dtui-editor
```

## Rich editor name

```text
dTuiEditor
```

## Evolution events

| Event | Comportement |
| --- | --- |
| `OnRichTextEditorRegister` | Ajoute `dTuiEditor` à la liste des éditeurs. |
| `OnRichTextEditorInit` | Injecte les assets et crée la field boot config. |

## Published files

| Publish tag | Output |
| --- | --- |
| `dtui-editor-config` | `core/custom/config/cms/settings/dTuiEditor.php` et `which_editor.php`. |
| `dtui-editor-assets` | `assets/plugins/dTui.editor`. |

## Config and diagnostics

| Signal | Signification |
| --- | --- |
| `cms.settings` | Evolution settings namespace pour published manager config. |
| `dTuiEditorCheck` | Config check file pour Evolution interface settings. |
| `dTuiEditorSettings` | Main package defaults file. |

## Routes

| Route | Usage |
| --- | --- |
| `dtui-evo-link-search` | Recherche les resources Evolution pour EVO link dialog. |
| `dtui-image-upload` | Reçoit les images collées depuis le presse-papiers. |
| `dtui-plantuml` | Ajoute un dark PlantUML skin et redirige vers le renderer. |

## Formats stockés

| Fonction | Format |
| --- | --- |
| Resource link | Placeholder `[~12~]`. |
| Clipboard image | HTML image après upload. |
| UML | `figure.dtui-uml` avec source base64url dans `data-uml`. |
| Content field | HTML propre avant manager save. |

## evo-ui bridge names

| Name | Owner |
| --- | --- |
| `data-evo-rich-editor` | evo-ui field marker. |
| `data-evo-rich-editor-model` | evo-ui field/model mapping. |
| `EvoUI.syncRichEditors(form, wire)` | evo-ui sync bridge. |
| `window.dTuiEditor.sync(idOrName)` | dTui runtime sync. |
| `EvoUI\Support\RichTextEditor::html(...)` | Server-side evo-ui rich editor bridge. |

## API JavaScript

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
