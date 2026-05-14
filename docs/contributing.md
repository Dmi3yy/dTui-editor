# Contributing To dTui Editor Docs

Use this guide when adding or changing package documentation for dTui Editor.

## Locale Rules

The public docs root is:

```text
docs/
```

Required release locales:

```text
en
uk
pl
de
fr
```

Use `uk` for Ukrainian. Do not create `docs/ua`.

## Page Rules

- Use one H1 per page.
- Keep user guides focused on manager tasks.
- Keep developer guides focused on architecture and integration boundaries.
- Put exact settings, routes, events, formats, and APIs in reference pages.
- Use `text` for file trees and command output.
- Set a language on every fenced code block.
- Use relative links inside docs.

## Adapter Rule

Do not move dTui-specific runtime behavior into consumer docs. The documented
contract is:

- dTui owns editor assets and editor-specific features.
- evo-ui owns generic field lifecycle markers and sync helpers.
- consumers use the bridge and do not hand-roll editor boot code.

## Verification

Run before release:

```sh
php docs/checks/docs-check.php
```
