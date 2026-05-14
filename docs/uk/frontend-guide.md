# Frontend та adapter guide

Цей guide пояснює, як dTui Editor працює з browser-managed Evolution fields і
спільним evo-ui editor/media adapter contract.

## Ownership boundary

`dTui Editor` володіє editor-specific assets і поведінкою:

- TOAST UI Editor assets;
- profiles і toolbar presets;
- Prism language bundle;
- image paste upload і кнопка Evolution image browser;
- EVO link picker;
- UML plugin і PlantUML dark-theme bridge;
- HTML/Markdown roundtrip cleanup.

`evo-ui` володіє generic field lifecycle:

- `data-evo-rich-editor` field markers;
- `data-evo-rich-editor-model` model mapping;
- `EvoUI.initRichEditorField(root)`;
- `EvoUI.syncRichEditors(form, wire)`;
- `EvoUI.clearRichEditors(form)`;
- media picker helpers, наприклад `EvoUI.browseImageField(inputId)`.

Consumers мають використовувати evo-ui markers і sync helpers для звичайних rich
text fields. Не треба писати module-local boot code для стандартних editor
fields.

## Asset bridge

dTui лишається owner для assets. Коли evo-ui рендерить поле з `dTuiEditor`, він
має пройти через Evolution rich editor initialization path. dTui plugin сам
підключить CSS/JS один раз і поставить field boot configs у queue.

```php
EvoUI\Support\RichTextEditor::html($ids, '500px', 'dTuiEditor', $options);
```

Bridge має передавати field-specific options, а не копіювати dTui runtime logic
у consumer package.

## Field markup contract

Звичайні evo-ui rich text fields мають рендерити textarea зі stable ids і
shared markers:

```html
<textarea
    id="content_body"
    data-evo-rich-editor
    data-evo-rich-editor-model="content.body"
></textarea>
```

Consumer володіє model names і persistence. dTui володіє editor instance, який
enhance-ить textarea.

## Save flow

Перед збереженням form з rich editors викликайте:

```js
EvoUI.syncRichEditors(form, wire);
```

Так dTui Editor серіалізує TOAST UI content назад в original textarea. Livewire
або звичайний form persistence після цього читає textarea value.

## Consumer rules

- Використовуйте `dTuiEditor`, коли потрібен Markdown/WYSIWYG content.
- Storage path і validation для images залишаються у consuming module.
- Generic lifecycle code має бути в evo-ui.
- dTui-specific toolbar, Prism, EVO link і UML logic лишаються в цьому пакеті.
- Consumer packages не мають вантажити remote editor assets.

## Release checklist

- Consumer package використовує evo-ui markers для ordinary rich text fields.
- Save actions викликають `EvoUI.syncRichEditors` перед Livewire persistence.
- Consumer package не дублює dTui toolbar, Prism, EVO link, image або UML
  runtime code.
- dTui assets published один раз і вантажаться через Evolution editor events.
