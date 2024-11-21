# Prosty framework PHP

Ten framework został stworzony, aby zaprezentować jak wiedzę zdobytą podczas wykładów AI można wykorzystać w praktyce do stworzenia w miarę wygodnego narzędzia do budowania systemów internetowych.

W tym artykule omówione zostały podstawy konstrukcji i mechanizmy działania tego frameworka.

Framework składa się z następujących katalogów:

```text
custom-php-framework
|--config
|--public
|--sql
|--src
|--templates
|--autoload.php
|--data.db
```

Poszczególne katalogi zostaną opisane poniżej.

## `config`

Katalog zawiera pliki konfiguracyjne frameworka. W pliku `config.php` znajdują się ustawienia połączenia z bazą danych.
Plik ten początkowo nie istnieje i należy go skopiować z pliku `config.dist.php`. Plik `config.dist.php` zawiera domyślne ustawienia, które należy dostosować do własnych potrzeb. Jest on zacommitowany do repozytorium. Pliku `config.php` nie należy commitować. Zawiera on bowiem ustawienia prywatne każdego z developerów, bądź też dane produkcyjne (w przypadku wdrożenia systemu na serwer).

## `public`

To tzw. webroot aplikacji. Tutaj znajduje się plik index.php, który jest punktem wejścia do aplikacji. Cały ruch na stronie przechodzi przez ten plik. Jest to tzw. "front controller". 
W katalogu `public` znajdują się również pliki statyczne wykorzystywane przez przeglądarkę do zarządzania wyglądem aplikacji, tzw. assety.

Struktura katalogu `assets` jest następująca:

```text
public
|--assets
|  |--dist
|  |--src
|  |  |--less
|  |  |--vendor
```

Framework wykorzystuje preprocesor LESS w celu ułatwienia tworzenia styli CSS. Jego kody źródłowe znajdują się w katalogu `public/assets/src/less`. Po zmianach w plikach LESS, należy skompilować je do plików CSS. Należy zwrócić uwagę, że punktem wejścia LESS jest plik `style.less`. Wewnątrz niego podłączone są następnie poszczególne fragmenty styli (ich nazwy rozpoczynają się od podkreślenia, żeby odróżnić częściowe style od pełnego punktu wejścia).
W katalogu `public/assets/src/vendor` należy umieścić wszelkie biblioteki zewnętrzne wykorzystywane przez style, takie jak `reset.css` itp.

Skompilowane pliki CSS trafiają do katalogu `public/assets/dist`. To właśnie te pliki należy następnie podłączyć do szablonów HTML.

## `sql`

Ten katalog zawiera prymitywny (lecz prosty!) system migracji. Pozwala on, aby wszyscy programiści w zespole mogli mieć spójne bazy danych, ale własne bazy danych. Eliminuje to konieczność korzystania z zewnętrznej, wspólnej, deweloperskiej bazy danych. Przypadki takie zdarzały się w poprzednich edycjach kursu i zawsze kończyły się katastrofalnie :)

Zamiast wprowadzać zmiany bezpośrednio w bazie danych, należy utworzyć tutaj kolejne pliki SQL. Przykładowo `01-post.sql`, `02-comment.sql`, ..., `99-update-comments.sql`. Nazwa pliku składa się z numeru kolejnego oraz krótkiego opisu zmiany. Alternatywnie zamiast numeru kolejnego można użyć daty utworzenia danego pliku migracji.

Uwaga. Poprawnie nazwane tabele powinny być rzeczownikami policzalnymi w liczbie pojedynczej. W przypadku złożonych nazw stosuje się `snake_case` (por. `PascalCase`, `camelCase` i `kebab-case`).

## `templates`

W tym katalogu znajdują się szablony HTML / PHP do budowania wyglądu strony. W pliku `base.html.php` znajduje się ogólny szablon strony - począwszy od otwierającego znacznika `<html>` przez cssy, jsy, nagłówek, content aż po stopkę i znacznik zamykający `</html>`.

Plik `base.html.php` wykorzystuje `nav.html.php` do wygenerowania listy składającej się na menu nawigacyjne.

Szablony do poszczególnych akcji kontrolerów są pogrupowana w katalogach o nazwach zgodnych z nazwami kontrolerów. Przykładowo `HelloController::helloAction()` powinno mieć swój widok w pliku `templates/hello/hello.html.php`. Plik ten powinien ustawiać potrzebne zmienne i ładować zawartość pliku `base.html.php`, który te zmienne wykorzystuje.

## `src`

W tym katalogu znajduje się kod źródłowy frameworka. Jest on podzielony na kilka katalogów:

- `Controller` - kontrolery aplikacji
- `Model` - modele aplikacji
- `Exception` - wyjątki aplikacji, tak aby łatwiej było określić co poszło nie tak
- `Service` - serwisy, czyli tzw. helpery. Użyteczne klasy wspierające działanie aplikacji.

Każdy kontroler musi zwrócić kod HTML kompletnego widoku do wyświetlenia na koniec działania aplikacji. Do generowania szablonu można wykorzystać serwis `Templating`. Do generowania linków w szablonach można wykorzystać serwis `Router`.
Serwis `Config` daje dostęp do ustawień konfiguracyjnych aplikacji (tych z pliku `config.php`, ale posiada dodatkową obsługę błędów).

Kontroler `InfoController` posiada jedną akcję `infoAction()`, która wyświetla wynik `phpinfo()`. Przydatne do podłączania xdebuga i ogólnego sprawdzania konfiguracji. USUNĄĆ PRZED WDROŻENIEM PRODUKCYJNYM.

Kontroler `PostController` posiada wszystkie akcje konieczne do operacji CRUD (Create, Read, Update, Delete) na modelu `Post`. Wykorzystuje klasę `Post` do zarządzania danymi oraz szablony do generowania widoków.

Modele w tym frameworku zbudowane są w oparciu o wzorzec ActiveRecord. Każdy model odpowiada jednej tabeli w bazie danych. Oprócz pól odpowiadających bazie danych, modele posiadają metody odpowiedzialne za pobieranie i zapisywanie danych z bazy danych z tabeli odpowiadającej danemu modelowi. Jest to najprostsze podejście do mapowania obiektowo relacyjnego (ORM). Zastosowanie modeli sprawia, że w kodzie kontrolera nie trzeba wykonywać żadnych zapytań bezpośrednio do bazy danych. Wykonujemy przykładowo `Post::findAll()` i w rezultacie otrzymujemy tablicę wszystkich postó w systemie, od razu w postaci obiektów klasy `Post`.

Poprawnie nazwany model powinien zaczynać się od wielkiej litery i być policzalnym rzeczownikiem w liczbie pojedynczej. Wielowyrazowe nazwy pól powinny być zapisane w `camelCase` (por. `PascalCase`, `snake_case` i `kebab-case`). Model poiwnien mieć metody konwertujące obiekty na tablice i tablice na obiekty.

## `autoload.php`

Plik ten wczytany jest na początku front controllera i zapewnia że nigdzie nie trzeba ręcznie includować plików z kodami źródłowymi.

## `data.db`

Baza danych SQLite. Zobacz instrukcję do laboratorium w celu utworzenia bazy danych. Na utworzonej bazie danych należy wykonać wszystkie dostępne migracje i okresowo sprawdzać, czy nie ma nowych.

## Routing

Routing w aplikacji odbywa się w pliku `index.php`. Wszystkie akcje muszą mieć swoje słowo kluczowe `action`, a w głównym switchu front controllera wybierany, konfigurowany, wykonywany i wyświetlany jest odpowiedni kontroler i akcja.
