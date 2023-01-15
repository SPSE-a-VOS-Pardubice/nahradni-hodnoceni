<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Controller;

use Spse\NahradniHodnoceni\View;
use Psr\Http\Message\{ResponseInterface as Response, ServerRequestInterface as Request};

const modelNamespace = "Spse\\NahradniHodnoceni\\Model\\";
const EditableDatabaseEntity = "Spse\NahradniHodnoceni\Model\EditableDatabaseEntity";

const tableMap = [
    "predmety" => "Predmet",
    "studenti" => "Student",
    "tridy" => "Trida",
    "zkousky" => "Zkouska",
    "ucitele" => "Ucitel",
    "priznaky" => "Priznak",
    "ucebny" => "Ucebna"
];

class FormController extends AbstractController
{
    public function show(Request $request, Response $response, array $args): Response
    {
        /** @var View */
        $view = $this->container->get("view");
        $database = $this->container->get("database");

        $name = $args["name"];

        if (!array_key_exists($name, tableMap)) {
            echo "Tabulka nenalezena";
            return $response;
        }
        $model = modelNamespace . tableMap[$name];

        $id = $args["id"];

        if ($id === "new") {
            $path = "/table/" . $args["name"] . "/new";
            $item = null;
        } else if (preg_match("/^\d+$/", $id)) {
            $path = "/table/" . $args["name"] . "/" . $id; 
            $item = $model::get($database, $id);
            if (is_null($item))
                return $view->renderResponse($request, $response, "/error.php", []);
        } else {
            return $view->renderResponse($request, $response, "/error.php", []);
        }

        return $view->renderResponse($request, $response, "/form.php", [
            "schema"            => $model::getProperties(),
            "item"              => $item,
            "intermediateData"  => $item->getIntermediateData(),
            "type"              => tableMap[$name],
            "path"              => $path,
            "options"           => $model::getSelectOptions($database),
        ]);
    }

    public function post(Request $request, Response $response, array $args): Response
    {
        // TODO: 
        $parsedBody = $request->getParsedBody();
        var_dump($parsedBody);
    }
}