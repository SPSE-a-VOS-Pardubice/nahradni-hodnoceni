<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Controller;

use Spse\NahradniHodnoceni\View;
use Psr\Http\Message\{ResponseInterface as Response, ServerRequestInterface as Request};
const modelNamespace = "Spse\\NahradniHodnoceni\\Model\\";

const tableMap = [
    "predmety"  => "Subject",
    "studenti"  => "Student",
    "tridy"     => "_Class",
    "zkousky"   => "Exam",
    "ucitele"   => "Teacher",
    "priznaky"  => "_Trait",
    "ucebny"    => "Classroom"
];

class TableController extends AbstractController {
    public function show(Request $request, Response $response, array $args): Response {
        /** @var View */
        $view = $this->container->get("view");
        $database = $this->container->get("database");

        $name = $args["name"];

        if(!array_key_exists($name, tableMap)) {
            echo "Tabulka nenalezena";
            return $response;
        }
        $model = modelNamespace . tableMap[$name];

        $tableRoute = "/table/" . $args["name"] . "/";

        $items = $model::getAll($database);

        // Vyrenderuj webovou stránku.
        return $view->renderResponse($request, $response, "/table.php", [
            "schema"                => $model::getProperties(),
            "items"                 => $items,
            "itemsIntermediateData" => array_map(function ($item) { return $item->getIntermediateData(); }, $items),
            "path"                  => $tableRoute,
            "options"               => $model::getSelectOptions($database),
        ]);
    }
}