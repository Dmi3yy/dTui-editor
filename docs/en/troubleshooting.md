# Troubleshooting

Use this guide when the editor appears blank, does not save, or one of the
manager integrations does not behave as expected.

## Editor Does Not Appear

Check that `dTuiEditor` is selected as the Evolution rich text editor and that
assets were published:

```text
assets/plugins/dTui.editor
```

Also verify that `OnRichTextEditorInit` is called for the textarea ids rendered
on the page.

## Multiple Editors Do Not All Initialize

Repeated fields can reuse legacy names. Make sure every rendered textarea has a
unique DOM id and that dTui boot configs are queued through `window.dTuiEditor`.

## Content Does Not Save

Call sync before reading textarea values:

```js
window.dTuiEditor.sync(idOrTextareaName);
```

For evo-ui forms, call:

```js
EvoUI.syncRichEditors(form, wire);
```

## Clipboard Image Upload Fails

Check `plugins.image.options.uploadPath`, file system permissions, and whether
`pasteUpload` is enabled. The upload path must be relative and safe.

## Image Browser Does Not Insert A File

Check that the manager file browser opens with image mode and that the selected
file is returned to the expected field callback. If a consumer uses evo-ui media
helpers, make sure it dispatches `input` and `change` events after setting the
field value.

## UML Is Not Editable After Reopen

UML roundtrip requires the saved HTML to keep the `figure.dtui-uml` wrapper and
its `data-uml` attribute. Sanitizers must not remove those attributes from
manager-stored content.

## Code Highlighting Is Missing

Use one of the bundled languages or aliases. For Laravel templates, use:

````md
```blade
{{ $slot }}
```
````

## Dark Theme Looks Wrong

Clear manager cache and browser cache after republishing assets. If only code
blocks look wrong, check that `prism.min.css`, the compact Prism bundle, and
`dtui-editor.css` are all loaded from the same published asset version.

## Manager Page Feels Slow

Confirm that the chart plugin is still disabled. If a project adds extra Prism
languages, keep the list narrow and retest Safari in the Evolution manager iframe.
