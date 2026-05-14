# Documentation dTui Editor

dTui Editor ajoute TOAST UI Editor 3 à Evolution CMS comme éditeur rich text
auto-hébergé dans le manager. Le paquet prend en charge Markdown, split preview,
WYSIWYG, liens EVO, navigateur d'images Evolution, collage d'images depuis le
presse-papiers, diagrammes UML et coloration Prism.

## Guides

- [Guide utilisateur](user-guide.md)
- [Guide développeur](developer-guide.md)
- [Configuration](configuration.md)
- [Référence](reference.md)
- [Frontend et adaptateur](frontend-guide.md)
- [Dépannage](troubleshooting.md)

## Fonctionnalités principales

- Enregistrement de l'éditeur sous le nom `dTuiEditor`.
- Trois modes: Markdown, Split et WYSIWYG.
- Plusieurs champs éditeur sur la même page manager.
- Édition du HTML existant comme Markdown et sauvegarde en HTML propre.
- Sélecteur de lien EVO avec sortie `[~id~]`.
- Images via presse-papiers, URL directe ou navigateur de fichiers Evolution.
- Blocs PlantUML éditables avec rendu adapté aux thèmes sombres.
- Coloration Prism pour les langages web courants, y compris Blade.
- Styles adaptés aux thèmes Evolution pour toolbar, dialogs, surface et code.

## Fichiers importants

- `config/dTuiEditorSettings.php` - paramètres par défaut.
- `plugins/dTuiEditorPlugin.php` - intégration Evolution rich editor events.
- `src/Http/routes.php` - routes EVO links, image upload et PlantUML.
- `public/js/dtui-init.js` - browser runtime et bootstrap.
- `public/js/dtui-image.js` - image browser et paste upload.
- `public/js/dtui-evolinks.js` - sélecteur de ressources EVO.
- `public/css/dtui-editor.css` - intégration des thèmes du manager.
