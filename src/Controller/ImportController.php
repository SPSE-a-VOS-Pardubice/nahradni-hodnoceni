<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Controller;

use Spse\NahradniHodnoceni\View;
use Spse\NahradniHodnoceni\Model\Exam;
use Spse\NahradniHodnoceni\Model\Student;
use Spse\NahradniHodnoceni\Model\Subject;
use Spse\NahradniHodnoceni\Model\ExamTeacher;
use Spse\NahradniHodnoceni\Model\Teacher;
use Psr\Http\Message\{ResponseInterface as Response, ServerRequestInterface as Request};
use DateTime;

const modelNamespace = "Spse\\NahradniHodnoceni\\Model\\";

const csvMapping = [
    "trida"         => 0,
    "jmeno"         => 1,
    "prijmeni"      => 2,
    "predmet"       => 3,
    "znamka"        => 4,
    "zkousejici"    => 5
];

class ImportController extends AbstractController
{
    private $importedExams = array();
    private $importedExamsTeachers = array();
    
    public function show(Request $request, Response $response, array $args): Response
    {
        /** @var View  */
        $view = $this->container->get("view");

        return $view->renderResponse($request, $response, "/import.php");
    }

    public function upload(Request $request, Response $response, array $args): Response
    {
        /** @var View  */
        $view = $this->container->get("view");

        $database = $this->container->get("database");
        
        $file = $request->getUploadedFiles()["import"];
        if(!$file) {
            return $view->renderResponse($request, $response, "/error.php", 
                    ["message" => "Nahrání souboru selhalo", "backLink" => "/import/upload"]);
        }

        switch ($file->getError()) {
            case (UPLOAD_ERR_INI_SIZE):
                return $view->renderResponse($request, $response, "/error.php", 
                    ["message" => "Velikost nahraného souboru převyšuje nastavení 'upload_max_filesize' v 'php.ini'", "backLink" => "/import/upload"]);
            case (UPLOAD_ERR_FORM_SIZE):
                return $view->renderResponse($request, $response, "/error.php", 
                    ["message" => "Velikost nahraného souboru převyšuje nastavení 'MAX_FILE_SIZE' v HTML formuláři", "backLink" => "/import/upload"]);
            case (UPLOAD_ERR_PARTIAL):
                return $view->renderResponse($request, $response, "/error.php", 
                    ["message" => "Soubor byl pouze částečně nahrán", "backLink" => "/import/upload"]);
            case (UPLOAD_ERR_NO_FILE):
                return $view->renderResponse($request, $response, "/error.php", 
                    ["message" => "Žádný soubor nebyl nahrán", "backLink" => "/import/upload"]);
            case (UPLOAD_ERR_NO_TMP_DIR):
                return $view->renderResponse($request, $response, "/error.php", 
                    ["message" => "Chybí temporary folder", "backLink" => "/import/upload"]);
            case (UPLOAD_ERR_CANT_WRITE):
                return $view->renderResponse($request, $response, "/error.php", 
                    ["message" => "Chyba při zapisování souboru na disk", "backLink" => "/import/upload"]);
            case (UPLOAD_ERR_EXTENSION):
                return $view->renderResponse($request, $response, "/error.php", 
                    ["message" => "PHP extension zamezila v nahrání souboru", "backLink" => "/import/upload"]);

        }

        $stream = $file->getStream();
        if($stream == null) {
            return $view->renderResponse($request, $response, "/error.php", 
                    ["message" => "\$file->getStream() je null", "backLink" => "/import/upload"]);
        }

        $items = $this->parse($file->getStream());
        $importedExams = $items[0];
        $importedExamsTeachers = $items[1];
        
        return $view->renderResponse($request, $response, "/table.php", [
            "schema"                => Exam::getProperties(),
            "items"                 => $importedExams,
            "itemsIntermediateData" => array_map(function ($importedExams) { return $importedExams->getIntermediateData(); }, $importedExams),
            "path"                  => "",
            "options"               => Exam::getSelectOptions($database),
        ]);
        
        //return $this->redirect($response, "/import/preview");
    }

    public function showPreview(Request $request, Response $response, array $args): Response
    {
        // TODO: Parse CSV soubor
        /** @var View  */
        $view = $this->container->get("view");

        return $view->renderResponse($request, $response, "/importPreview.php");
    }

    public function accept(Request $request, Response $response, array $args): Response
    {
        // TODO: Nahrát nová data do databáze
        return $response;
    }
    
    public function parse($stream, string $separator = ",") {
        $database = $this->container->get("database");

        $returnValue = [];
        $importedExams = array();
        $importedExamsTeachers = array();
        
        $strings = explode("\n", $stream->__toString());
        $lastIndex = count($strings) - 1;
        if($strings[$lastIndex] == "") {
            unset($strings[$lastIndex]);
        }

        for ($i = 1; $i < count($strings); $i++) {
            $values = str_getcsv($strings[$i], $separator);

            $exam = new Exam($database);
            $exam->__set("id", 0);
            
            // Získávání student ID
            try {
                $student = Student::getFromNameSurnameClass($database, $values[csvMapping["jmeno"]], $values[csvMapping["prijmeni"]], $values[csvMapping["trida"]]);
                $exam->__set("student_id", $student->__get("id"));
            } catch(\RuntimeException $e) {
                $this->addToImportErrorLog($e->getMessage());
                continue;
            }

            // Získávání subject ID
            try {
                $subject = Subject::getFromAbbreviation($database, $values[csvMapping["predmet"]]);
                $exam->__set("subject_id", $subject->__get("id"));
            } catch(\RuntimeException $e) {
                $this->addToImportErrorLog($e->getMessage());
                continue;
            }
            
            $exam->__set("original_mark", $values[csvMapping["znamka"]]);
            
            // TODO: Zjistit jaké výchozí hodnoty použít
            $exam->__set("classroom_id", -1);
            $exam->__set("final_mark", "");
            $exam->__set("time", new DateTime("1970-01-01"));

            $importedExams[] = $exam;

            $examTeacher = new ExamTeacher($database);
            $examTeacher->__set("exam_id", 0);

            try {
                $teacher = Teacher::getFromSurname($database, $values[csvMapping["zkousejici"]]);
                $examTeacher->__set("teacher_id", $teacher->__get("id"));
            } catch(\RuntimeException $e) {
                $this->addToImportErrorLog($e->getMessage());
                continue;
            }

            $examTeacher->__set("Role", "Zkoučející");

            $importedExamsTeachers[] = $examTeacher;
        }

        $returnValue[0] = $importedExams;
        $returnValue[1] = $importedExamsTeachers;

        return $returnValue;
    }

    public function addToImportErrorLog(string $string) {

    }
}