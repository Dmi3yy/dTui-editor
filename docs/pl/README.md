# Dokumentacja dTui Editor

dTui Editor dodaje TOAST UI Editor 3 do Evolution CMS jako samodzielny edytor
rich text dla panelu managera. Pakiet obsługuje Markdown, split preview, WYSIWYG,
linki EVO, przeglądarkę obrazów Evolution, wklejanie obrazów ze schowka, diagramy
UML i podświetlanie kodu Prism.

## Przewodniki

- [Przewodnik użytkownika](user-guide.md)
- [Przewodnik dewelopera](developer-guide.md)
- [Konfiguracja](configuration.md)
- [Reference](reference.md)
- [Frontend i adapter](frontend-guide.md)
- [Rozwiązywanie problemów](troubleshooting.md)

## Główne możliwości

- Rejestracja edytora jako `dTuiEditor`.
- Trzy tryby pracy: Markdown, Split i WYSIWYG.
- Wiele pól edytora na jednej stronie managera.
- Edycja istniejącego HTML jako Markdown i zapis czystego HTML.
- Wybór zasobu EVO z wyjściem `[~id~]`.
- Obrazy przez schowek, URL lub manager plików Evolution.
- Edytowalne bloki PlantUML z obsługą ciemnych motywów.
- Podświetlanie Prism dla popularnych języków web, w tym Blade.
- Stylowanie toolbarów, dialogów, powierzchni edytora i bloków kodu dla motywów Evolution.

## Ważne pliki

- `config/dTuiEditorSettings.php` - ustawienia domyślne.
- `plugins/dTuiEditorPlugin.php` - integracja z Evolution rich editor events.
- `src/Http/routes.php` - routes dla EVO links, image upload i PlantUML.
- `public/js/dtui-init.js` - browser runtime i bootstrap edytorów.
- `public/js/dtui-image.js` - image browser i paste upload.
- `public/js/dtui-evolinks.js` - wybór zasobów EVO.
- `public/css/dtui-editor.css` - integracja z motywami managera.
