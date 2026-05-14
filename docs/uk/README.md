# Документація dTui Editor

dTui Editor додає TOAST UI Editor 3 в Evolution CMS як self-hosted rich text
editor для менеджера. Пакет підтримує Markdown, split preview, WYSIWYG, EVO
посилання, файловий менеджер Evolution для картинок, вставку картинок з буфера,
UML-діаграми та Prism-підсвітку коду.

## Гайди

- [Гайд користувача](user-guide.md)
- [Гайд розробника](developer-guide.md)
- [Конфігурація](configuration.md)
- [Довідник](reference.md)
- [Frontend та adapter guide](frontend-guide.md)
- [Troubleshooting](troubleshooting.md)

## Основні можливості

- Реєстрація редактора як `dTuiEditor`.
- Три режими: Markdown, Split і WYSIWYG.
- Кілька editor-полів на одній сторінці менеджера.
- Редагування HTML як Markdown з чистим HTML при збереженні.
- EVO link picker з плейсхолдерами `[~id~]`.
- Картинки через paste, прямий URL або файловий менеджер Evolution.
- Редаговані PlantUML блоки з підтримкою темних тем.
- Prism highlight для популярних web мов, включно з Blade.
- Стилі toolbar, dialog, editor surface і code blocks під теми Evolution.

## Важливі файли

- `config/dTuiEditorSettings.php` - дефолтні налаштування.
- `plugins/dTuiEditorPlugin.php` - інтеграція з Evolution rich editor events.
- `src/Http/routes.php` - EVO links, image upload і PlantUML routes.
- `public/js/dtui-init.js` - browser runtime і boot editor instances.
- `public/js/dtui-image.js` - image browser і paste upload.
- `public/js/dtui-evolinks.js` - EVO resource picker.
- `public/css/dtui-editor.css` - інтеграція з темами менеджера.
