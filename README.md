# Náhradní hodnocení
Školní projekt pro organizaci náhradních hodnocení a správu jejich záznamů.

## Vývoj
Pro lokální instalaci je zapotřebí mít PHP přidané v PATH. Musí být také nainstalována/povolena následujících rozšíření:
- ext-json *
- PHP MySQL driver

### Závislosti
Projekt používá [Composer](https://getcomposer.org/) pro správu závislostí.
Pro instalaci musíte mít Composer v PATH a ve složce projektu spustit
```bash
composer install
```

A pro následné aktualizace
```bash
composer update
```

### Databáze
Jako databázi doporučuji používat MariaDB, ať přes [Docker](https://hub.docker.com/_/mariadb/) nebo [XAMPP](https://www.apachefriends.org/).

Pro nahrání schématu vezměte nejnovější soubor ze složky `sql/`, co končí `-full` a spusťte ho v databázi. Poté vezměte všechny `-partial` soubory, starší než ten `full` a postupně je také v databázi spusťte. Je možné, že po nejnovější `full` verzi nebyly vydané žádné `partial` verze, v tom případě stačí pouze ta `full` verze.

Pro nahrání testovacích dat vezměte soubor `docs/data.sql` a spusťe ho v databázi.

| :warning:  Exportování z MySQL Workbench   |
|--------------------------------------------|

Po exportu z MySQL Workbench je zapotřebí odstranit z textu všechny instance ` VISIBLE` u `INDEX`u pro zpětnou kompatibilitu (MariaDB 10.4 a starší).

### Spuštění
Před spuštěním se ujistěte, že jste vytvořili a nastavili soubor `config/config.php`. Můžete k tomu použít příklad v `config/config.example.php`.

Hlavní soubor `index.php` se sice nachází ve složce `public/`, při spuštění vývojářské verze se ale musíte nacházet v rootu projektu.

Pokud chcete zobrazovat chybové hlášky, je zapotřebí nastavit proměnnou prostředí (environment variable) `DEBUG` na hodnotu `true`. Provedete to tak, že před spuštění `php` uděláte v terminálu ještě

Linux (bash, zsh, apod.):
```
export DEBUG=true
```

Windows (PowerShell):
```
$Env:DEBUG = "true"
```

Pro spuštění `php` (např. na portu 8080) je pak nutno zadat do terminálu
```bash
php -S 127.0.0.1:8080 -t public/
```
### Debugging
K debuggování se zapotřebí si nainstalovat [Xdebug](https://xdebug.org/) a přidat do konfiguračního soubory PHP ``php.ini`` tyto dvě řádky:
```
xdebug.mode = debug
xdebug.start_with_request = yes
```
Po spuštění PHP serveru obvyklým způsobem je zapotřebí zapnout debugger pomocí launch configuration Visual Studia s názvem ``Listen for Xdebug``.


## Deployment
TODO, Issue je [zde](https://github.com/SPSE-a-VOS-Pardubice/nahradni-hodnoceni/issues/8)
