<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Controller;

use Spse\NahradniHodnoceni\Model\_Class;
use Spse\NahradniHodnoceni\View;
use Spse\NahradniHodnoceni\Model\Exam;
use Spse\NahradniHodnoceni\Model\Student;
use Spse\NahradniHodnoceni\Model\Subject;
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

// TODO: https://github.com/SPSE-a-VOS-Pardubice/nahradni-hodnoceni/issues/1#issuecomment-1399263118
class PreviewTableEntry {
    private $class = "";
    private string $name;
    private string $surname;
    private string $subject;
    private string $mark;
    private string $teacher;
    
    public function __construct(string $class, string $name, string $surname, string $subject, string $mark, string $teacher) {
        $this->class = $class;
        $this->name = $name;
        $this->surname = $surname;
        $this->subject = $subject;
        $this->mark = $mark;
        $this->teacher = $teacher;
    }

    public function getArray(): array {
        return array($this->class, $this->name, $this->surname, $this->subject, $this->mark, $this->teacher);
    }
}

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

        $importedExams = $this->parse($file->getStream());

        session_start();
        for ($i = 0; $i < count($importedExams); $i++) {
            $_SESSION["importedExams"][$i] = $importedExams[$i]->getPropertyValues();
        }

        $previewEntries = $this->consturctPreviewTableEntries($importedExams);
        
        return $view->renderResponse($request, $response, "/importPreview.php", [
            "entries" => $previewEntries
        ]);
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
        /** @var View  */
        $view = $this->container->get("view");
        
        session_start();
        
        if(!isset($_SESSION["importedExams"])) {
            session_destroy();
            return $view->renderResponse($request, $response, "/error.php", 
                    ["message" => "Chyba s PHP session", "backLink" => "/import/upload"]);
        }

        $database = $this->container->get("database");
        $importedExams = [];
        for ($i = 0; $i < count($_SESSION["importedExams"]); $i++) {
            $importedExams[$i] = new Exam($database);
            $importedExams[$i]->setPropertyValues($_SESSION["importedExams"][$i]);
        }
        
        $database = $this->container->get("database");
        for ($i = 0; $i < count($_SESSION["importedExams"]); $i++) {
            $importedExams[$i]->write();
        }

        $_SESSION["importedExams"] = [];
        session_destroy();
        
        return $this->redirect($response, "/table/priznaky");
    }
    
    public function parse($stream, string $separator = ";") {
        $database = $this->container->get("database");

        $returnValue = [];
        $importedExams = array();
        
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
            $exam->__set("classroom_id", null);
            $exam->__set("final_mark", "");
            $exam->__set("time", new DateTime("1970-01-01"));

            // Získávání ID zkoušejícího učitele
            try {
                $teacher = Teacher::getFromSurname($database, $values[csvMapping["zkousejici"]]);
                $exam->__set("examiner_id", $teacher->__get("id"));
            } catch(\RuntimeException $e) {
                $this->addToImportErrorLog($e->getMessage());
                continue;
            }

            $exam->__set("class_teacher_id", null);
            $exam->__set("chairman_id", null);

            $importedExams[] = $exam;
        }

        return $importedExams;
    }

    public function addToImportErrorLog(string $string) {

    }

    public function consturctPreviewTableEntries($exams): array {
        $database = $this->container->get("database");
        
        $res = array();
        for($i = 0; $i < count($exams); $i++) {
            $student = Student::get($database, intval($exams[$i]->getProperty("student_id")));
            $class = _Class::get($database, intval($student->getProperty("class_id")));
            $subject = Subject::get($database, intval($exams[$i]->getProperty("subject_id")));
            $teacher = Teacher::get($database, intval($exams[$i]->__get("examiner_id")));
            
            $res[] = new PreviewTableEntry(
                $class->getFormatted(),
                $student->getProperty("name"),
                $student->getProperty("surname"),
                $subject->getFormatted(),
                $exams[$i]->getProperty("original_mark"),
                $teacher->getFormatted()
            );
        }

        return $res;
    }
}