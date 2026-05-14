# dTui Editor Documentation

dTui Editor adds TOAST UI Editor 3 to Evolution CMS as a self-hosted manager rich
text editor. It supports Markdown, split preview, WYSIWYG editing, EVO resource
links, Evolution image browser integration, clipboard image uploads, UML diagrams,
and Prism code highlighting.

## Guides

- [User Guide](user-guide.md)
- [Developer Guide](developer-guide.md)
- [Configuration](configuration.md)
- [Reference](reference.md)
- [Frontend and Adapter Guide](frontend-guide.md)
- [Troubleshooting](troubleshooting.md)

## Main Capabilities

- Rich text editor registration as `dTuiEditor`.
- Three editing modes: Markdown, Split, and WYSIWYG.
- Multiple editor fields on one manager page.
- HTML-to-Markdown editing and clean HTML save roundtrip.
- EVO link picker with `[~id~]` placeholder output.
- Image insertion by paste, direct URL, or Evolution file browser.
- Editable PlantUML blocks with dark-theme rendering support.
- Prism highlighting for common web languages, including Blade.
- Theme-aware toolbar, dialogs, editor surface, and code blocks.

## Important Files

- `config/dTuiEditorSettings.php` - default package settings.
- `plugins/dTuiEditorPlugin.php` - Evolution rich editor event integration.
- `src/Http/routes.php` - EVO link search, image upload, and PlantUML routes.
- `public/js/dtui-init.js` - browser runtime and editor bootstrap.
- `public/js/dtui-image.js` - image browser and paste upload integration.
- `public/js/dtui-evolinks.js` - EVO resource link picker.
- `public/css/dtui-editor.css` - Evolution manager theme integration.
