# Náhradní hodnocení

## Development

Projekt se skládá ze dvou částí. Pro backend je použit framework Java Spring Boot. Frontend je napsán v TypeScriptu s React framework.

Dokumentace k frontendu je v jeho složce, [zde](frontend/README.md).

### Databáze

Jako databáze je použita MySQL, doporučujeme použít MariaDB. Po spuštění serveru je zapotřebí vytvořit db ke které se backend připojí.

### Spuštění

Pro správu záležitostí využíváme Gradle systém. Při spuštení se musí nastavit následující argumenty, buď přes CLI, nebo environmental variables:
- `spring.datasource.url` (`jdbc:mysql://127.0.0.1:3306/nahradni-hodnoceni`)
- `spring.datasource.username`
- `spring.datasource.password`


Příklad spuštění pomocí CLI argumentů:
```
gradlew bootRun --args="--spring.datasource.url=jdbc:mysql://127.0.0.1:3306/nahradni-hodnoceni --spring.datasource.username=username --spring.datasource.password=password"
```
