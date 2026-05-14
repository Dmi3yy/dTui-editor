# Fehlerbehebung

Diese Anleitung hilft, wenn der Editor leer bleibt, nicht speichert oder eine
Manager-Integration nicht wie erwartet funktioniert.

## Editor erscheint nicht

Prüfen Sie, ob `dTuiEditor` als Evolution rich text editor ausgewählt ist und ob
die Assets veröffentlicht wurden:

```text
assets/plugins/dTui.editor
```

Prüfen Sie außerdem, ob `OnRichTextEditorInit` für die textarea ids der Seite
ausgeführt wird.

## Nicht alle Editoren initialisieren

Repeated fields können alte gemeinsame names nutzen. Jede textarea braucht eine
eindeutige DOM id, und dTui boot configs sollen über `window.dTuiEditor` laufen.

## Content wird nicht gespeichert

Vor dem Lesen der textarea sync ausführen:

```js
window.dTuiEditor.sync(idOrTextareaName);
```

Für evo-ui forms:

```js
EvoUI.syncRichEditors(form, wire);
```

## Clipboard image upload funktioniert nicht

Prüfen Sie `plugins.image.options.uploadPath`, Schreibrechte und `pasteUpload`.
Der upload path muss sicher und relativ sein.

## Image browser fügt keine Datei ein

Prüfen Sie, ob der manager file browser im image mode geöffnet wird und ob die
selected file in den erwarteten field callback zurückkommt. Wenn ein consumer
evo-ui media helpers nutzt, müssen nach dem Setzen des Werts `input` und `change`
events ausgelöst werden.

## UML ist nach erneutem Öffnen nicht editierbar

Gespeichertes HTML muss `figure.dtui-uml` und das Attribut `data-uml` behalten.
Sanitizers dürfen diese Attribute nicht aus Manager-Content entfernen.

## Code highlighting fehlt

Für Laravel templates:

````md
```blade
{{ $slot }}
```
````

## Dark theme sieht falsch aus

Leeren Sie manager cache und browser cache nach dem republish von Assets. Wenn
nur code blocks betroffen sind, prüfen Sie, ob `prism.min.css`, compact Prism
bundle und `dtui-editor.css` aus derselben published asset version geladen werden.

## Manager page ist langsam

Prüfen Sie, ob das chart plugin weiterhin disabled ist. Wenn ein Projekt extra
Prism languages hinzufügt, halten Sie die Liste klein und testen Sie Safari im
Evolution manager iframe erneut.
