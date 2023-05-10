<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Controller;

use Spse\NahradniHodnoceni\Model\DatabaseEntityPropertyType;
use Spse\NahradniHodnoceni\View;
use Psr\Http\Message\{ResponseInterface as Response, ServerRequestInterface as Request};
use Spse\NahradniHodnoceni\Model\DatabaseEntityProperty;
use Spse\NahradniHodnoceni\Model\DatabaseEntity;

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
                return $view->renderResponse($request, $response, "/error.php", ["message" => "Failed to fetch the item."]);
        } else {
            return $view->renderResponse($request, $response, "/error.php", ["message" => "Invalid ID entered."]);
        }

        return $view->renderResponse($request, $response, "/form.php", [
            "schema"                    => $model::getProperties(),
            "item"                      => $item,
            "availableOptions"          => $item::getAvailableOptions($database),
            "explicatedExternal"  => array("Subjects" => // "Subjects" v tomto případě značí název tabulky v databázi
                array(1 => "Číslicová Technika", 2 => "Servis PC", 3 => "Webové Aplikace", 4 => "Programování", 
                5 => "Technická dokumentace", 6 => "Anglický jazyk", 7 => "Fyzika", 8 => "Matematika")),
            "intermediateValues" => array("subjects" => // "subjects" v tomto případě značí název intermediate property v modelu Teacher
                array(0 => array("subject_id" => 1, "suitability" => 0), 1 => array("subject_id" => 5, "suitability" => 1))),
            
            // staré
            "type"              => tableMap[$name],
            "path"              => $path,
            "options"           => $model::getAvailableOptions($database),
        ]);
    }

    public function post(Request $request, Response $response, array $args): Response {
        $parsedBody = $request->getParsedBody();

        var_dump($parsedBody);
        // // return $parsedBody;
        
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
         
        //     try {
        //         $model::applyPostData($model::parsePostData($database, $parsedBody));
        //     } catch (\Throwable $th) {
        //         return $view->renderResponse($request, $response, "/error.php", [
        //             "message" => $th->getMessage()
        //         ]);
        //     }

            try {
                $model = new $model($database);
    
                $intermediateKeys = [];
                foreach($model::getProperties() as $property) {
                    if ($property->type === DatabaseEntityPropertyType::INTERMEDIATE_DATA) {
    
                        $intermediateKeys[$property->name] = [];
                        // splitni si pole
                        foreach ($parsedBody as $key => $value) {
                            $parsedKey = explode("-", $key);
                            
                            if ($parsedKey[0] === $property->name) {

                                if (!key_exists($parsedKey[1], $intermediateKeys[$property->name]))
                                    $intermediateKeys[$property->name][$parsedKey[1]] = [];
                            
                                $intermediateKeys[$property->name][$parsedKey[1]][$parsedKey[2]] = $value;
                            }
                        }
    
                    } else {
                        $model->$property->name = $parsedBody[$property->name];
                    }
                }
                
                $model->write();
                    
                foreach ($intermediateKeys as $propName => $values) {
                    foreach ($values as $key => $values) {
                        $intermediateModel = $model::findPropetyClass($propName);
                        $instance = new $intermediateModel($database);
    
                        foreach ($intermediateModel::getProperties() as $property) {
                            if (key_exists($property->name, $intermediateKeys[$propName][$key])) {
                                $instance->$property->name = $intermediateKeys[$propName][$key][$property->name];
                            } else if ($property->selectOptionsSource === $model::class) {
                                $instance->$property->name = $model->id; // id modelu
                            } else {
                                $instance->$property->name = $key;
                            }
                        }
                        $instance->write();
                    }
                }
            } catch (\Throwable $th) {
                throw $th;
            }

        } else {
        //     // edituje starý 
        //     try {
        //         $model::applyPostData($model::parsePostData($database, $parsedBody, intval($id)));
        //     } catch (\Throwable $th) {
        //         return $view->renderResponse($request, $response, "/error.php", [
        //             "message" => $th->getMessage()
        //         ]);
        //     }

            $model = new $model($database);
            $model->id = intval($parsedBody["id"]);

            $intermediateKeys = [];
            foreach($model::getProperties() as $property) {
                if ($property->type === DatabaseEntityPropertyType::INTERMEDIATE_DATA) {

                    $intermediateKeys[$property->name] = [];
                    // splitni si pole
                    foreach ($parsedBody as $key => $value) {
                        $parsedKey = explode("-", $key);
                        
                        if (!key_exists($parsedKey[1], $intermediateKeys[$property->name]))
                            $intermediateKeys[$property->name][$parsedKey[1]] = [];
                        
                        $intermediateKeys[$property->name][$parsedKey[1]][$parsedKey[2]] = $value;
                    }

                } else {
                    $model->$property->name = $parsedBody[$property->name];
                }
            }
            
            $model->write();
            
            $intermediateModels = [];
            foreach ($intermediateKeys as $propName => $values) {
                $intermediateModels[$propName] = [];
                foreach ($values as $key => $values) {
                    $intermediateModel = $model::findPropetyClass($propName);
                    $instance = new $intermediateModel($database);

                    foreach ($intermediateModel::getProperties() as $property) {
                        if (key_exists($property->name, $intermediateKeys[$propName][$key])) {
                            $instance->$property->name = $intermediateKeys[$propName][$key][$property->name];
                        } else if ($property->selectOptionsSource === $model::class) {
                            $instance->$property->name = $model->id; // id modelu
                        } else {
                            $instance->$property->name = $key;
                        }
                    }
                    
                    $intermediateModels[$propName][] = $instance;
                }
            }

            $model::updateData($database, $intermediateModels);
        }

        return $this->redirect($response, "/table/$name");
    }
}
