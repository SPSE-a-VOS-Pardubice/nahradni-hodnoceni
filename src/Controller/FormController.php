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
  


        if (preg_match("/^new$/", $id)) { // je nový
            $path = "/tables/" . $args["name"] . "/new"; 
            
            return $view->renderResponse($request, $response, "/form.php", [
                "item" => new $model($database),
                "header" => $model::getProperties(),
                "type" => tableMap[$name],
                "path" => $path,
            ]);
        } else if (preg_match("/^\d+$/", $id)) { // je id
            $item = $model::get($database, $id);
            $path = "/tables/" . $args["name"] . "/" . $id; 

            if ($item != null) {
                return $view->renderResponse($request, $response, "/form.php", [
                    "item" => $item,
                    "header" => $model::getProperties(),
                    "type" => tableMap[$name],
                    "path" => $path, 
                ]);
            } else {
                return $view->renderResponse($request, $response, "/error.php", []);
            }
        } else { // je špatný
            return $view->renderResponse($request, $response, "/error.php", []);
        }
    }

    public function post(Request $request, Response $response, array $args): Response
    {
        // TODO: 
    }
}