# Troubleshooting

Цей guide допомагає, коли editor не з'являється, не зберігає content або одна з
manager integrations працює неочікувано.

## Editor не з'являється

Перевірте, що `dTuiEditor` вибраний як Evolution rich text editor і assets
опубліковані:

```text
assets/plugins/dTui.editor
```

Також перевірте, що `OnRichTextEditorInit` викликається для textarea ids на
сторінці.

## Не ініціалізуються всі editors

Repeated fields можуть використовувати старі однакові names. Кожна textarea має
мати унікальний DOM id, а dTui boot configs мають проходити через
`window.dTuiEditor`.

## Content не зберігається

Перед читанням textarea викликайте sync:

```js
window.dTuiEditor.sync(idOrTextareaName);
```

Для evo-ui forms:

```js
EvoUI.syncRichEditors(form, wire);
```

## Clipboard image upload не працює

Перевірте `plugins.image.options.uploadPath`, file permissions і чи ввімкнений
`pasteUpload`. Upload path має бути відносним і безпечним.

## Image browser не вставляє файл

Перевірте, що manager file browser відкривається в image mode і selected file
повертається в expected field callback. Якщо consumer використовує evo-ui media
helpers, після встановлення значення мають dispatch-итись `input` і `change`
events.

## UML не редагується після повторного відкриття

UML roundtrip потребує, щоб saved HTML зберіг `figure.dtui-uml` wrapper і
`data-uml` attribute. Sanitizers не мають видаляти ці attributes з manager-stored
content.

## Code highlighting відсутній

Використовуйте одну з bundled languages або aliases. Для Laravel templates:

````md
```blade
{{ $slot }}
```
````

## Dark theme виглядає неправильно

Очистіть manager cache і browser cache після republish assets. Якщо проблема
тільки в code blocks, перевірте, що `prism.min.css`, compact Prism bundle і
`dtui-editor.css` завантажені з тієї самої published asset version.

## Manager page повільна

Перевірте, що chart plugin лишається disabled. Якщо проєкт додає extra Prism
languages, тримайте список вузьким і повторно тестуйте Safari в Evolution manager
iframe.
