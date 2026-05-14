# Rozwiązywanie problemów

Ten przewodnik pomaga, gdy editor jest pusty, nie zapisuje treści albo integracja
managera działa inaczej niż oczekiwano.

## Editor się nie pojawia

Sprawdź, czy `dTuiEditor` jest wybrany jako Evolution rich text editor i czy
assets są opublikowane:

```text
assets/plugins/dTui.editor
```

Sprawdź też, czy `OnRichTextEditorInit` jest wywoływany dla textarea ids na
stronie.

## Nie inicjalizują się wszystkie editory

Repeated fields mogą mieć stare wspólne names. Każda textarea powinna mieć
unikalny DOM id, a boot configs powinny przechodzić przez `window.dTuiEditor`.

## Content się nie zapisuje

Przed odczytem textarea wykonaj sync:

```js
window.dTuiEditor.sync(idOrTextareaName);
```

Dla evo-ui forms:

```js
EvoUI.syncRichEditors(form, wire);
```

## Clipboard image upload nie działa

Sprawdź `plugins.image.options.uploadPath`, uprawnienia zapisu i `pasteUpload`.
Upload path musi być bezpieczną ścieżką względną.

## Image browser nie wstawia pliku

Sprawdź, czy manager file browser otwiera się w image mode i czy selected file
wraca do oczekiwanego field callback. Jeśli consumer używa evo-ui media helpers,
po ustawieniu wartości powinny zostać wysłane events `input` i `change`.

## UML nie jest edytowalny po ponownym otwarciu

HTML musi zachować `figure.dtui-uml` i atrybut `data-uml`. Sanitizers nie powinny
usuwać tych atrybutów z treści managera.

## Brak podświetlania kodu

Dla Laravel templates użyj:

````md
```blade
{{ $slot }}
```
````

## Dark theme wygląda źle

Wyczyść manager cache i browser cache po republish assets. Jeśli problem dotyczy
tylko code blocks, sprawdź, czy `prism.min.css`, compact Prism bundle i
`dtui-editor.css` pochodzą z tej samej published asset version.

## Manager page działa wolno

Sprawdź, czy chart plugin nadal jest disabled. Jeśli projekt dodaje extra Prism
languages, utrzymaj wąską listę i ponownie przetestuj Safari w Evolution manager
iframe.
