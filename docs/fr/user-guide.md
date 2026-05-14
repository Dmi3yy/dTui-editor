# Guide utilisateur

## Ouvrir l'éditeur

Sélectionnez `dTuiEditor` comme rich text editor dans les paramètres du manager
Evolution. Les champs utilisant l'événement standard rich text editor peuvent
alors afficher dTui Editor.

L'éditeur propose trois modes:

| Mode | Usage |
| --- | --- |
| `Markdown` | Édition du Markdown uniquement. |
| `Split` | Markdown avec aperçu en direct. |
| `WYSIWYG` | Édition visuelle sans panneau Markdown. |

Le sélecteur de mode se trouve en bas de l'éditeur.

## Éditer le contenu

dTui Editor charge le HTML Evolution existant comme Markdown éditable. À la
sauvegarde, la valeur est écrite comme HTML.

La toolbar contient headings, bold, italic, strike, listes, task lists, tables,
images, links, EVO links, code blocks, color syntax et UML.

## Workflows recommandés

Utilisez `WYSIWYG` pour l'édition de texte courant. Utilisez `Split` quand le
contenu contient des images, code blocks, liens internes ou UML et que l'éditeur
doit comparer source et preview. Utilisez `Markdown` pour la documentation
technique ou le nettoyage précis de Markdown.

Avant de quitter une page de ressource, sauvegardez avec l'action normale
Evolution. dTui synchronise la textarea cachée pendant le save flow afin
qu'Evolution reçoive du HTML propre.

## Liens EVO

Le bouton `E` ouvre la recherche de ressources Evolution et insère un lien
interne. Par défaut, le lien est sauvegardé comme placeholder:

```md
[Article][~12~]
```

## Images

Les images peuvent être ajoutées de trois manières:

1. Coller une image avec `Ctrl+V` ou `Command+V`.
2. Coller une URL d'image directe dans le dialog standard.
3. Utiliser le bouton `Img` et choisir un fichier dans Evolution image browser.

Les uploads depuis le presse-papiers sont généralement stockés dans:

```text
assets/images
```

Pour les images choisies depuis Evolution browser, gardez les fichiers dans des
public asset folders normaux et utilisez des filenames descriptifs. Clipboard
uploads reçoit un filename généré avec `dtui-` pour éviter les collisions.

## UML

Utilisez le bouton `UML` ou un bloc PlantUML:

```md
$$uml
Bob->Alice: Hello
$$
```

La source UML d'origine est conservée avec l'image rendue afin que le diagramme
reste éditable après réouverture.

## Blocs de code

Utilisez des fenced code blocks avec un nom de langage:

````md
```blade
<x-evo::layout :title="$pageTitle">
    {{ $slot }}
</x-evo::layout>
```
````

Le paquet prend en charge HTML, CSS, SCSS, JavaScript, TypeScript, PHP, Blade,
SQL, JSON, Markdown, Bash et YAML.

Utilisez `blade` pour les snippets Laravel/Evolution Blade et `php` pour le PHP
simple. Utilisez `text` pour command output, file trees ou les blocs sans
highlighting.

## Dépannage

Si l'éditeur n'apparaît pas, vérifiez que `dTuiEditor` est sélectionné et que les
assets sont publiés dans `assets/plugins/dTui.editor`.

Si le collage d'image ne fonctionne pas, vérifiez `uploadPath` et les droits
d'écriture.
