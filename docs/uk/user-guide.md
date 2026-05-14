# Гайд користувача

## Відкриття редактора

Виберіть `dTuiEditor` як rich text editor у налаштуваннях Evolution manager.
Після цього поля, які використовують стандартну подію rich text editor, можуть
рендерити dTui Editor.

Редактор має три режими:

| Режим | Для чого |
| --- | --- |
| `Markdown` | Редагування тільки Markdown source. |
| `Split` | Markdown з live preview поруч. |
| `WYSIWYG` | Візуальне редагування без Markdown pane. |

Перемикач режимів знаходиться внизу редактора.

## Редагування контенту

dTui Editor відкриває існуючий HTML контент Evolution як редагований Markdown.
При збереженні значення записується назад як HTML.

Toolbar містить headings, bold, italic, strike, lists, task lists, tables,
images, links, EVO links, code blocks, color syntax та UML.

## Рекомендовані workflows

Використовуйте `WYSIWYG` для звичайного текстового контенту. Використовуйте
`Split`, коли в матеріалі є картинки, code blocks, внутрішні links або UML і
потрібно бачити source та preview. Використовуйте `Markdown` для технічної
документації або точного Markdown cleanup.

Перед виходом зі сторінки ресурсу зберігайте через стандартну дію Evolution.
dTui синхронізує hidden textarea під час save flow, тому Evolution отримує clean
HTML.

## EVO посилання

Кнопка `E` відкриває пошук ресурсів Evolution і вставляє внутрішнє посилання.
За замовчуванням посилання зберігається як плейсхолдер:

```md
[Блог][~12~]
```

Такий формат безпечний для Evolution output і лишається редагованим у Markdown
та WYSIWYG режимах.

## Картинки

Картинки можна вставляти трьома способами:

1. Вставити з буфера через `Ctrl+V` або `Command+V`.
2. Вставити прямий URL через стандартний image dialog.
3. Натиснути `Img` і вибрати файл у Evolution image browser.

Paste upload зберігає файли в налаштовану директорію, зазвичай:

```text
assets/images
```

Для картинок із Evolution browser тримайте файли у звичайних public asset
folders і давайте зрозумілі filenames. Clipboard uploads отримують generated
`dtui-` filename, щоб уникати collisions.

## UML

Використовуйте кнопку `UML` або PlantUML блок:

```md
$$uml
Bob->Alice: Hello
$$
```

У preview блок рендериться як картинка. dTui Editor зберігає оригінальний UML
source разом із картинкою, тому після повторного відкриття діаграму можна
редагувати.

## Code blocks

Для підсвітки використовуйте fenced blocks з назвою мови:

````md
```blade
<x-evo::layout :title="$pageTitle">
    {{ $slot }}
</x-evo::layout>
```
````

Пакет підтримує HTML, CSS, SCSS, JavaScript, TypeScript, PHP, Blade, SQL, JSON,
Markdown, Bash і YAML.

Використовуйте `blade` для Laravel/Evolution Blade snippets і `php` для plain
PHP. Використовуйте `text` для command output, file trees або blocks без
highlighting.

## Troubleshooting

Якщо редактор не з'являється, перевірте вибір `dTuiEditor` у manager settings і
наявність опублікованих assets у `assets/plugins/dTui.editor`.

Якщо paste upload не працює, перевірте `uploadPath` і права на запис у цю
директорію.
