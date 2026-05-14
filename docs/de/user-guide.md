# Benutzerhandbuch

## Editor öffnen

Wählen Sie `dTuiEditor` in den Evolution Manager-Einstellungen als Rich-Text-
Editor aus. Felder, die das Standardereignis für Rich-Text-Editoren verwenden,
können danach dTui Editor anzeigen.

Der Editor hat drei Modi:

| Modus | Zweck |
| --- | --- |
| `Markdown` | Nur Markdown-Quelltext bearbeiten. |
| `Split` | Markdown mit Live-Vorschau daneben. |
| `WYSIWYG` | Visuelle Bearbeitung ohne Markdown-Bereich. |

Der Modusschalter befindet sich unten im Editor.

## Inhalte bearbeiten

dTui Editor lädt bestehende Evolution-HTML-Inhalte als editierbares Markdown.
Beim Speichern wird der Inhalt wieder als HTML geschrieben.

Die Toolbar enthält Überschriften, bold, italic, strike, Listen, task lists,
Tabellen, Bilder, Links, EVO links, code blocks, color syntax und UML.

## Empfohlene Workflows

Nutzen Sie `WYSIWYG` für normale Textbearbeitung. Nutzen Sie `Split`, wenn der
Inhalt Bilder, code blocks, interne Links oder UML enthält und Source und Preview
verglichen werden sollen. Nutzen Sie `Markdown` für technische Dokumentation
oder exaktes Markdown cleanup.

Speichern Sie Ressourcen über die normale Evolution-Aktion. dTui synchronisiert
die versteckte textarea im save flow, sodass Evolution sauberes HTML erhält.

## EVO-Links

Die Schaltfläche `E` öffnet die Ressourcensuche von Evolution und fügt einen
internen Link ein. Standardmäßig wird ein Platzhalter gespeichert:

```md
[Blogbeitrag][~12~]
```

## Bilder

Bilder können auf drei Arten eingefügt werden:

1. Bild mit `Ctrl+V` oder `Command+V` aus der Zwischenablage einfügen.
2. Direkte Bild-URL im Standarddialog einfügen.
3. `Img` verwenden und eine Datei im Evolution image browser auswählen.

Uploads aus der Zwischenablage werden normalerweise hier gespeichert:

```text
assets/images
```

Bilder aus dem Evolution browser sollten in normalen public asset folders liegen
und aussagekräftige filenames haben. Clipboard uploads erhalten automatisch
einen `dtui-` filename, um collisions zu vermeiden.

## UML

Verwenden Sie die Schaltfläche `UML` oder einen PlantUML-Block:

```md
$$uml
Bob->Alice: Hello
$$
```

Der ursprüngliche UML-Quelltext bleibt zusammen mit dem gerenderten Bild
erhalten, damit das Diagramm später wieder bearbeitet werden kann.

## Codeblöcke

Nutzen Sie fenced code blocks mit Sprachname:

````md
```blade
<x-evo::layout :title="$pageTitle">
    {{ $slot }}
</x-evo::layout>
```
````

Unterstützt werden HTML, CSS, SCSS, JavaScript, TypeScript, PHP, Blade, SQL,
JSON, Markdown, Bash und YAML.

Verwenden Sie `blade` für Laravel/Evolution Blade snippets und `php` für plain
PHP. Verwenden Sie `text` für command output, file trees oder Blöcke ohne
highlighting.

## Fehlerbehebung

Wenn der Editor nicht erscheint, prüfen Sie die Auswahl `dTuiEditor` und die
veröffentlichten Assets in `assets/plugins/dTui.editor`.

Wenn Bild-Paste nicht funktioniert, prüfen Sie `uploadPath` und Schreibrechte.
