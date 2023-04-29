<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Controller;

use Spse\NahradniHodnoceni\View;
use Psr\Http\Message\{ResponseInterface as Response, ServerRequestInterface as Request};

const modelNamespace = "Spse\\NahradniHodnoceni\\Model\\";
const EditableDatabaseEntity = "Spse\NahradniHodnoceni\Model\EditableDatabaseEntity";

// TODO tenhle kód je duplikát z `TableController.php`
const tableMap = [
    "predmety"  => "Subject",
    "studenti"  => "Student",
    "tridy"     => "_Class",
    "zkousky"   => "Exam",
    "ucitele"   => "Teacher",
    "priznaky"  => "_Trait",
    "ucebny"    => "Classroom"
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
            $item = $model::get($database, intval($id));
            if (is_null($item))
                return $view->renderResponse($request, $response, "/error.php", []);
        } else {
            return $view->renderResponse($request, $response, "/error.php", []);
        }

        return $view->renderResponse($request, $response, "/form.php", [
            "schema"            => $model::getProperties(),
            "item"              => $item,
            "intermediateData"  => $item == null ? (new $model($database))->getIntermediateData() : $item->getIntermediateData(),
            "type"              => tableMap[$name],
            "path"              => $path,
            "options"           => $model::getAvailableOptions($database),
        ]);
    }

    public function post(Request $request, Response $response, array $args): Response {
        $parsedBody = $request->getParsedBody();

        // var_dump($parsedBody);
        // return $parsedBody;
        
        $view = $this->container->get("view");
        $database = $this->container->get("database");

        $name = $args["name"];

        if (!array_key_exists($name, tableMap)) {
            echo "Tabulka nenalezena";
            return $response;
        }
        $model = modelNamespace . tableMap[$name];

        // vytváří nový nebo edituje starý
        $id = $args["id"];
        if ($id === "new") {
        
            // vytváří nový 
            try {
                $model::applyPostData($model::parsePostData($database, $parsedBody));
            } catch (\Throwable $th) {
                return $view->renderResponse($request, $response, "/error.php", [
                    "message" => $th->getMessage()
                ]);
            }

        } else {
            // edituje starý 
            try {
                $model::applyPostData($model::parsePostData($database, $parsedBody, intval($id)));
            } catch (\Throwable $th) {
                return $view->renderResponse($request, $response, "/error.php", [
                    "message" => $th->getMessage()
                ]);
            }
        }

        return $this->redirect($response, "/table/$name");
    }
}
