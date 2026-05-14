# Dépannage

Ce guide aide quand l'éditeur reste vide, ne sauvegarde pas ou qu'une intégration
manager ne fonctionne pas comme prévu.

## L'éditeur n'apparaît pas

Vérifiez que `dTuiEditor` est sélectionné comme Evolution rich text editor et que
les assets sont publiés:

```text
assets/plugins/dTui.editor
```

Vérifiez aussi que `OnRichTextEditorInit` est appelé pour les textarea ids de la
page.

## Tous les éditeurs ne s'initialisent pas

Les repeated fields peuvent réutiliser d'anciens names. Chaque textarea doit
avoir un DOM id unique, et les boot configs dTui doivent passer par
`window.dTuiEditor`.

## Le contenu ne se sauvegarde pas

Synchronisez avant de lire la textarea:

```js
window.dTuiEditor.sync(idOrTextareaName);
```

Pour evo-ui forms:

```js
EvoUI.syncRichEditors(form, wire);
```

## Clipboard image upload échoue

Vérifiez `plugins.image.options.uploadPath`, les droits d'écriture et
`pasteUpload`. Le chemin d'upload doit être relatif et sûr.

## Image browser n'insère pas de fichier

Vérifiez que le manager file browser s'ouvre en image mode et que le selected
file revient au field callback attendu. Si un consumer utilise evo-ui media
helpers, il doit dispatcher les events `input` et `change` après avoir défini la
valeur.

## UML n'est pas éditable après réouverture

Le HTML sauvegardé doit conserver `figure.dtui-uml` et l'attribut `data-uml`.
Les sanitizers ne doivent pas supprimer ces attributs du contenu manager.

## Code highlighting absent

Pour les Laravel templates:

````md
```blade
{{ $slot }}
```
````

## Dark theme incorrect

Videz le manager cache et le browser cache après republish des assets. Si seuls
les code blocks sont concernés, vérifiez que `prism.min.css`, le compact Prism
bundle et `dtui-editor.css` viennent de la même published asset version.

## Manager page lente

Confirmez que le chart plugin reste disabled. Si un projet ajoute des extra Prism
languages, gardez la liste courte et retestez Safari dans l'iframe du manager
Evolution.
