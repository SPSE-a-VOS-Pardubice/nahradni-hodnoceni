<?php

declare(strict_types=1);

define("ROOT_DIR", dirname(__DIR__));
require_once ROOT_DIR . "/vendor/autoload.php";
require_once ROOT_DIR . "/config/config.php";

use Spse\NahradniHodnoceni\Controller\{HomeController, ImportController};
use Spse\NahradniHodnoceni\Model\Database;
use Spse\NahradniHodnoceni\View;
use DI\Container;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Spse\NahradniHodnoceni\Controller\TableController;
use Spse\NahradniHodnoceni\Controller\FormController;
use Spse\NahradniHodnoceni\Middleware\Header;

// Sestav kontejner.
$container = new Container();

$container->set("config", $config);
$container->set("messages", []);

$container->set("database", function () use ($config) {
    return new Database(
        //ROOT_DIR,
        $config["db"]["type"],
        $config["db"]["host"],
        $config["db"]["name"],
        $config["db"]["user"],
        $config["db"]["pass"]
    );
});

$container->set("view", function () {
    return new View(ROOT_DIR);
});

// Zkontroluj, zda je databáze připravena.
// TODO: Spustit kontrolu pouze po aktualizaci programu.
// TODO: Přidat do databáze automatické aktualizace pro podporu budoucích struktur.
// TODO $container->get("database")->check();

// Vytvoř aplikaci a zaregistruj cesty.
$app = AppFactory::create(null, $container);

// Zhotov middlewary.
$header = new Header($container);
// TODO

// Webový interface:
$app->group("", function (RouteCollectorProxy $group) {
    // Domovská stránka:
    $group->get("/", [HomeController::class, "home"]);
    $group->get("/table/{name}", [TableController::class, "show"]);

    $group->get("/table/{name}/{id}", [FormController::class, "show"]);
    $group->post("/table/{name}/{id}", [FormController::class, "post"]);

    // Správa uživatele:
    $group->get("/import/menu", [ImportController::class, "menu"]);

})->add($header);

// API:
$app->group("/api", function (RouteCollectorProxy $group) {

});

// Registeruj globální middlewary a spusť aplikaci.
$errorMiddleware = $app->addErrorMiddleware(getenv("DEBUG") === "true", true, true);
$app->run();
