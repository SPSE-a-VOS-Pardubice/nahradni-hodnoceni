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
            "schema"                    => $model::getProperties(),
            "item"                      => $item,
            "compiledAvailableOptions"  => array("Subjects" => // "Subjects" v tomto případě značí název tabulky v databázi
                array(1 => "Číslicová Technika", 2 => "Servis PC", 3 => "Webové Aplikace", 4 => "Programování", 
                5 => "Technická dokumentace", 6 => "Anglický jazyk", 7 => "Fyzika", 8 => "Matematika")),
            "projectedIntermediateData" => array("subjects" => // "subjects" v tomto případě značí název intermediate property v modelu Teacher
                array(0 => array("subject_id" => 1, "suitability" => "vhodny"), 1 => array("subject_id" => 5, "suitability" => "nahovno"))),
            
            // staré
            "intermediateData"  => null, // $item == null ? [] : $item->getIntermediateData(),
            "type"              => tableMap[$name],
            "path"              => $path,
            "options"           => $model::getAvailableOptions($database), // Jsou možnosti selectů a human-readable forma externích dat intermediate tabulky jedno a to samé?
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
