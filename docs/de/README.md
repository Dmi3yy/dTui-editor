# dTui Editor Dokumentation

dTui Editor integriert TOAST UI Editor 3 als selbst gehosteten Rich-Text-Editor
in den Evolution CMS Manager. Das Paket unterstützt Markdown, Split-Preview,
WYSIWYG, EVO-Ressourcenlinks, den Evolution-Bildbrowser, Bild-Uploads aus der
Zwischenablage, UML-Diagramme und Prism-Codehervorhebung.

## Anleitungen

- [Benutzerhandbuch](user-guide.md)
- [Entwicklerhandbuch](developer-guide.md)
- [Konfiguration](configuration.md)
- [Referenz](reference.md)
- [Frontend und Adapter](frontend-guide.md)
- [Fehlerbehebung](troubleshooting.md)

## Hauptfunktionen

- Registrierung als `dTuiEditor`.
- Drei Modi: Markdown, Split und WYSIWYG.
- Mehrere Editorfelder auf derselben Managerseite.
- Bearbeitung bestehender HTML-Inhalte als Markdown und Speicherung als HTML.
- EVO-Link-Auswahl mit `[~id~]` Platzhaltern.
- Bilder per Zwischenablage, URL oder Evolution-Dateibrowser.
- Bearbeitbare PlantUML-Blöcke mit Unterstützung für dunkle Themes.
- Prism-Hervorhebung für gängige Websprachen inklusive Blade.
- Theme-aware Styling für Toolbar, Dialoge, Editorfläche und Codeblöcke.

## Wichtige Dateien

- `config/dTuiEditorSettings.php` - Standardeinstellungen.
- `plugins/dTuiEditorPlugin.php` - Evolution rich editor event integration.
- `src/Http/routes.php` - EVO links, image upload und PlantUML routes.
- `public/js/dtui-init.js` - browser runtime und editor bootstrap.
- `public/js/dtui-image.js` - image browser und paste upload.
- `public/js/dtui-evolinks.js` - EVO resource picker.
- `public/css/dtui-editor.css` - Integration in Manager-Themes.
