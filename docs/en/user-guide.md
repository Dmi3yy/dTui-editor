# User Guide

## Opening the Editor

Select `dTuiEditor` as the Evolution rich text editor in manager settings. Any
field that uses the Evolution rich text editor event can then render dTui Editor.

The editor can open in one of three modes:

| Mode | Purpose |
| --- | --- |
| `Markdown` | Edit source Markdown only. |
| `Split` | Edit Markdown with a live preview. |
| `WYSIWYG` | Edit visually without the Markdown pane. |

Use the mode switcher at the bottom of the editor to move between modes.

## Content Editing

dTui Editor loads existing Evolution HTML content into TOAST UI and converts it
into editable Markdown. When the page is saved, content is written back as HTML.

Common toolbar actions include headings, bold, italic, strike, lists, task lists,
tables, images, links, EVO links, code blocks, color syntax, and UML diagrams.

## Recommended Workflows

Use `WYSIWYG` when a manager edits normal page text and does not need to inspect
Markdown. Use `Split` when content contains images, code blocks, internal links,
or UML and the editor needs to compare source with output. Use `Markdown` when
the task is mostly technical writing, documentation, or exact Markdown cleanup.

Before leaving a resource page, save through the normal Evolution action. dTui
syncs the hidden textarea during the save flow so Evolution receives clean HTML.

## EVO Resource Links

Use the `E` toolbar button to search Evolution resources and insert an internal
link. By default links are saved as Evolution placeholders:

```md
[Blog post][~12~]
```

The placeholder is safe for Evolution content output and can still be edited in
Markdown or WYSIWYG mode.

## Images

Images can be inserted in three ways:

1. Paste an image from the clipboard with `Ctrl+V` or `Command+V`.
2. Use the standard image dialog and paste an image URL.
3. Use the `Img` toolbar button to open the Evolution image browser.

Clipboard uploads are stored in the configured image directory, usually:

```text
assets/images
```

For content images selected from the Evolution browser, keep files in normal
public asset folders and use descriptive filenames. Clipboard uploads receive a
generated `dtui-` filename to avoid collisions.

## UML Diagrams

Use the `UML` toolbar button or write a PlantUML block:

```md
$$uml
Bob->Alice: Hello
$$
```

The diagram is rendered as an image in the editor preview. dTui Editor stores the
original UML source with the rendered image, so the diagram stays editable when
the resource is opened again.

## Code Blocks

Use fenced code blocks with a language name:

````md
```blade
<x-evo::layout :title="$pageTitle">
    {{ $slot }}
</x-evo::layout>
```
````

Bundled highlighting includes HTML, CSS, SCSS, JavaScript, TypeScript, PHP,
Blade, SQL, JSON, Markdown, Bash, and YAML.

Use `blade` for Laravel/Evolution Blade snippets and `php` for plain PHP. Use
`text` when the block is command output, a file tree, or content that should not
be highlighted as code.

## Troubleshooting

If the editor does not appear, check that `dTuiEditor` is selected as the rich
text editor and that package assets were published to `assets/plugins/dTui.editor`.

If image paste does not upload, check the configured upload path and file system
permissions for the target directory.
