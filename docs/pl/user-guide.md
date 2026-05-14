# Przewodnik użytkownika

## Uruchomienie edytora

W ustawieniach Evolution manager wybierz `dTuiEditor` jako rich text editor.
Pola korzystające ze standardowego zdarzenia rich text editor będą mogły używać
dTui Editor.

Edytor ma trzy tryby:

| Tryb | Zastosowanie |
| --- | --- |
| `Markdown` | Tylko edycja źródła Markdown. |
| `Split` | Markdown i podgląd na żywo obok siebie. |
| `WYSIWYG` | Edycja wizualna bez panelu Markdown. |

Przełącznik trybu znajduje się na dole edytora.

## Edycja treści

dTui Editor otwiera istniejącą treść HTML Evolution jako edytowalny Markdown.
Podczas zapisu wartość wraca do pola jako HTML.

Toolbar zawiera nagłówki, bold, italic, strike, listy, task lists, tabele,
obrazy, linki, EVO links, code blocks, color syntax i UML.

## Zalecane workflows

Użyj `WYSIWYG` dla zwykłej edycji tekstu. Użyj `Split`, gdy treść zawiera
obrazy, code blocks, linki wewnętrzne albo UML i trzeba porównać source z
preview. Użyj `Markdown` dla dokumentacji technicznej lub dokładnego cleanup
Markdown.

Przed opuszczeniem strony zasobu zapisuj przez standardową akcję Evolution.
dTui synchronizuje ukrytą textarea w save flow, więc Evolution otrzymuje czysty
HTML.

## Linki EVO

Przycisk `E` otwiera wyszukiwarkę zasobów Evolution i wstawia link wewnętrzny.
Domyślnie link zapisywany jest jako placeholder:

```md
[Wpis bloga][~12~]
```

## Obrazy

Obrazy można dodać na trzy sposoby:

1. Wklej obraz ze schowka przez `Ctrl+V` lub `Command+V`.
2. Wklej bezpośredni URL w standardowym dialogu obrazu.
3. Użyj przycisku `Img` i wybierz plik w Evolution image browser.

Upload ze schowka zapisuje pliki zwykle w:

```text
assets/images
```

Obrazy wybrane z Evolution browser trzymaj w normalnych public asset folders i
stosuj opisowe filenames. Clipboard uploads dostają wygenerowaną nazwę `dtui-`,
aby uniknąć collisions.

## UML

Użyj przycisku `UML` albo bloku PlantUML:

```md
$$uml
Bob->Alice: Hello
$$
```

Oryginalny kod UML jest zachowywany razem z obrazem, więc diagram można później
otworzyć i edytować.

## Bloki kodu

Używaj fenced code blocks z nazwą języka:

````md
```blade
<x-evo::layout :title="$pageTitle">
    {{ $slot }}
</x-evo::layout>
```
````

Pakiet obsługuje HTML, CSS, SCSS, JavaScript, TypeScript, PHP, Blade, SQL, JSON,
Markdown, Bash i YAML.

Używaj `blade` dla Laravel/Evolution Blade snippets i `php` dla plain PHP.
Używaj `text` dla command output, file trees albo bloków bez highlighting.

## Rozwiązywanie problemów

Jeśli edytor się nie pojawia, sprawdź ustawienie `dTuiEditor` oraz opublikowane
assets w `assets/plugins/dTui.editor`.

Jeśli wklejanie obrazu nie działa, sprawdź `uploadPath` i prawa zapisu.
