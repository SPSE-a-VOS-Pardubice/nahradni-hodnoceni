<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Controller;

use Spse\NahradniHodnoceni\View;
use Psr\Http\Message\{ResponseInterface as Response, ServerRequestInterface as Request};

const modelNamespace = "Spse\\NahradniHodnoceni\\Model\\";

class ImportController extends AbstractController
{
    public function show(Request $request, Response $response, array $args): Response {
        /** @var View  */
        $view = $this->container->get("view");
        
        return $view->renderResponse($request, $response, "/import.php");
    }
    
    public function upload(Request $request, Response $response, array $args): Response {
        return $this->redirect($response, "/import/preview");
    }

    public function showPreview(Request $request, Response $response, array $args): Response {
        // TODO: Parse CSV soubor
        /** @var View  */
        $view = $this->container->get("view");
        
        return $view->renderResponse($request, $response, "/importPreview.php");
    }

    public function accept(Request $request, Response $response, array $args): Response {
        // TODO: Nahrát nová data do databáze

        return $response;
    }




    
    
    /*public function show(Request $request, Response $response, array $args): Response {
        $test = $request->getUploadedFiles();
        $a = $test[0];
    }

    public function menu(Request $request, Response $response, array $args): Response {
        /** @var View 
        $view = $this->container->get("view");

        echo (file_get_contents("../sql/import.csv"));
        $file = fopen("../sql/import.csv", "r");
        $this->parse($file);
        fclose($file);
        
        // Vyrenderuj webovou stránku.
        return $view->renderResponse($request, $response, "/import.php", [
            "test" => "uwu"
        ]);
    }

    private function parse($stream, string $separator = ";"): array {
        /** @var array<Exam> 
        $result = [];
        
        for ($i = 0; $i < 4; $i++) {
            $values = fgetcsv($stream, null, $separator);
            $object = new Exam(null);
            $object->setProperty("id",              "");
            $object->setProperty("student_id",      "Pavel");
            $object->setProperty("subject_id",      "Křivka");
            $object->setProperty("classroom_id",    "");
            $object->setProperty("original_mark",   "");
            $object->setProperty("final_mark",      "");
            $object->setProperty("time",            "");
        }

        return [];
    }*/
}