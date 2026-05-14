# Configuration

Cette référence décrit les paramètres dTui Editor qui contrôlent les modes, les
plugins, les chemins d'upload, les thèmes et les routes d'intégration.

## Config publiée

Config runtime après publication:

```text
core/custom/config/cms/settings/dTuiEditor.php
```

Valeurs par défaut du paquet:

```text
config/dTuiEditorSettings.php
```

## Options principales

| Clé | Default | Signification |
| --- | --- | --- |
| `default_profile` | `full` | Profil utilisé sans override de champ. |
| `default_theme` | `auto` | Suit le thème du manager. |
| `default_editor_mode` | `wysiwyg` | Mode initial: `markdown`, `split` ou `wysiwyg`. |
| `default_height` | `500px` | Hauteur par défaut. |
| `default_preview_style` | `vertical` | Layout preview pour split editing. |
| `usage_statistics` | `false` | Désactive TOAST UI telemetry. |

## Profils

| Profil | Usage |
| --- | --- |
| `full` | Editor complet avec images, EVO links, UML, Prism, colors et tables. |
| `mini` | Editor compact pour champs courts. |
| `introtext` | Editor plus petit pour intro text. |
| `custom` | Base pour project-specific overrides. |

## Plugins

| Plugin | Default | Note |
| --- | --- | --- |
| `codeSyntaxHighlight` | enabled | Bundle Prism local. |
| `colorSyntax` | enabled | Contrôles de couleur du texte. |
| `tableMergedCell` | enabled | Cellules de table fusionnées. |
| `uml` | enabled | Blocs PlantUML éditables. |
| `image` | enabled | Evolution image browser et paste upload. |
| `evolinks` | enabled | Sélecteur de ressource EVO. |
| `chart` | disabled | Désactivé car il peut bloquer les pages manager. |

## Uploads d'images

Clipboard upload utilise `plugins.image.options.uploadPath`. Le chemin doit être
relatif et sûr sous le dossier de base Evolution.

```php
'uploadPath' => 'assets/images',
```

## Theme values

| Value | Comportement |
| --- | --- |
| `auto` | Détecte le theme state du manager Evolution. |
| `lightness` | Pour le lightness manager theme. |
| `light` | Surface editor light standard. |
| `dark` | Surface editor dark. |
| `darkness` | Surface manager dark high-contrast. |

## Routes

```php
'routes' => [
    'evo_link_search' => 'dtui-evo-link-search',
    'image_upload' => 'dtui-image-upload',
    'plantuml_renderer' => 'dtui-plantuml',
],
```

## Langages Prism

```php
['html', 'css', 'scss', 'javascript', 'typescript', 'php', 'blade', 'sql', 'json', 'markdown', 'bash', 'yaml']
```

Les alias `js`, `ts`, `laravel-blade`, `bladephp`, `md`, `sh`, `shell`, `yml`,
`markup` et `xml` sont pris en charge.

## Safety notes

- Garder chart disabled sauf si le projet accepte le performance risk.
- Garder `usage_statistics` disabled.
- Les upload paths doivent être relatifs; absolute paths et traversal segments
  sont rejetés.
- Le Prism language bundle doit rester petit pour éviter de bloquer Safari et le
  manager iframe.
