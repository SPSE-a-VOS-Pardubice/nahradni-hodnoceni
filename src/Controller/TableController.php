<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Controller;

use Spse\NahradniHodnoceni\View;
use Psr\Http\Message\{ResponseInterface as Response, ServerRequestInterface as Request};
const modelNamespace = "Spse\\NahradniHodnoceni\\Model\\";

const tableMap = [
    "predmety" => "Predmet",
    "studenti" => "Student",
    "tridy" => "Trida",
    "zkousky" => "Zkouska",
    "ucitele" => "Ucitel",
    "priznaky" => "Priznak",
    "ucebny" => "Ucebna"
];

const tables = ["predmety", "studenti", "tridy", "zkousky", "ucitele", "priznaky", "ucebny"];

class TableController extends AbstractController {
    public function show(Request $request, Response $response, array $args): Response {
        /** @var View */
        $view = $this->container->get("view");
        $database = $this->container->get("database");

        $name = $args["name"];

        if(!array_key_exists($name, tableMap)){
            echo "Tabulka nenalezena";
            return $response;
        }
        $model = modelNamespace . tableMap[$name];

        $tableRoute = "/table/" . $args["name"] . "/";

        $items = $model::getAll($database);

        // Vyrenderuj webovou stránku.
        return $view->renderResponse($request, $response, "/table.php", [
            "schema" => $model::getProperties(),
            "items" => $items,
            "intermediateData" => array_map(function ($item) { return $item->getIntermediateData(); }, $items),
            "path" => $tableRoute,
            "list" => tables,
            "options" => $model::getSelectOptions($database),
        ]);
    }
}