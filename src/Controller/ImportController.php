<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Controller;

use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use Spse\NahradniHodnoceni\Model\Classroom;
use Spse\NahradniHodnoceni\Model\Database;
use Spse\NahradniHodnoceni\Model\Restriction;
use Spse\NahradniHodnoceni\Model\_Class;
use Spse\NahradniHodnoceni\View;
use Spse\NahradniHodnoceni\Model\Exam;
use Spse\NahradniHodnoceni\Model\Student;
use Spse\NahradniHodnoceni\Model\Subject;
use Spse\NahradniHodnoceni\Model\Teacher;
use Spse\NahradniHodnoceni\Helpers\ClassHelper;
use Psr\Http\Message\{ResponseInterface as Response, ServerRequestInterface as Request};
use DateTime;

define("IMPORT_BACK_LINK", "/import/upload");

const csvMapping = [
    "trida"         => 0,
    "jmeno"         => 1,
    "prijmeni"      => 2,
    "predmet"       => 3,
    "znamka"        => 4,
    "zkousejici"    => 5
];

class ImportController extends AbstractController {
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
        
        /** @var UploadedFileInterface */
        $file = $request->getUploadedFiles()["import"];
        if(!$file) {
            return $view->renderResponse($request, $response, "/error.php", 
                    ["message" => "Nahrání souboru selhalo", "backLink" => IMPORT_BACK_LINK]);
        }

        switch ($file->getError()) {
            case (UPLOAD_ERR_INI_SIZE):
                return $view->renderResponse($request, $response, "/error.php", 
                    ["message" => "Velikost nahraného souboru převyšuje nastavení 'upload_max_filesize' v 'php.ini'", "backLink" => IMPORT_BACK_LINK]);
            case (UPLOAD_ERR_FORM_SIZE):
                return $view->renderResponse($request, $response, "/error.php", 
                    ["message" => "Velikost nahraného souboru převyšuje nastavení 'MAX_FILE_SIZE' v HTML formuláři", "backLink" => IMPORT_BACK_LINK]);
            case (UPLOAD_ERR_PARTIAL):
                return $view->renderResponse($request, $response, "/error.php", 
                    ["message" => "Soubor byl pouze částečně nahrán", "backLink" => IMPORT_BACK_LINK]);
            case (UPLOAD_ERR_NO_FILE):
                return $view->renderResponse($request, $response, "/error.php", 
                    ["message" => "Žádný soubor nebyl nahrán", "backLink" => IMPORT_BACK_LINK]);
            case (UPLOAD_ERR_NO_TMP_DIR):
                return $view->renderResponse($request, $response, "/error.php", 
                    ["message" => "Chybí temporary folder", "backLink" => IMPORT_BACK_LINK]);
            case (UPLOAD_ERR_CANT_WRITE):
                return $view->renderResponse($request, $response, "/error.php", 
                    ["message" => "Chyba při zapisování souboru na disk", "backLink" => IMPORT_BACK_LINK]);
            case (UPLOAD_ERR_EXTENSION):
                return $view->renderResponse($request, $response, "/error.php", 
                    ["message" => "PHP extension zamezila v nahrání souboru", "backLink" => IMPORT_BACK_LINK]);

        }

        $stream = $file->getStream();
        if($stream == null) {
            return $view->renderResponse($request, $response, "/error.php", 
                    ["message" => "Chyba při nahrávání souboru", "backLink" => IMPORT_BACK_LINK]);
        }

        try {
            this->fromDatabase($file->getStream());
        } catch (\Exception $e) {
            return $view->renderResponse($request, $response, "/error.php", 
                    ["message" => "Chyba při zpracování souboru: ${$e->getMessage()}", "backLink" => IMPORT_BACK_LINK]);
        }
        
        return $this->redirect($response, "/table/zkousky");
    }

    public function fromStream(StreamInterface $stream, string $separator = null): ImportedData {
        /** @var Database  */
        $database = $this->container->get("database");

        $content = $stream->getContents();
        $content = str_replace("\r", "", $content); // podpora Windows newline
        $lines = explode("\n", $content);
        if ($lines === false)
            throw new \Exception("nastala neočekávaná chyba", 1);
        array_pop($lines); // odstraň poslední prázdný žádek
        // TODO odstranit automaticky všechny prázdné řádky

        $header = array_shift($lines);
        if ($header === null)
            throw new \Exception("neplatný CSV soubor: chybí hlavička", 1);
        // TODO zkontroluj hlavičku
        if ($separator === null) {
            // TODO detekuj separátor z hlavičky
            $separator = ";";
        }

        for ($i=0; $i < count($lines); $i++) { 
            $items = str_getcsv($lines[$i], $separator);
            $fullClassName = $items[0];
            $name = $items[1];
            $surname = $items[2];
            $subjectAbbrev = $items[3];
            $mark = $items[4];
            $teacherSurname = $items[5];

            $classNameComponents = explode(".", $fullClassName);
            $classGrade = $classNameComponents[0];
            $classLabel = $classNameComponents[1];
            $classYear = intval($classGrade) + intval(date("Y")) - 1;
            $_class = _Class::getRestricted($database, [
                new Restriction("year", $classYear),
                new Restriction("label", $classLabel)
            ]);
            
            $student = null;
            if ($_class === null) {
                $_class = new _Class($database);
                $_class->year = $classYear;
                $_class->label = $classLabel;
                $_class->write();
            } else {
                $student = Student::getRestricted($database, [
                    new Restriction("name", $name),
                    new Restriction("surname", $surname),
                    new Restriction("class_id", $_class->id)
                ]);
            }

            if ($student === null) {
                $student = new Student($database);
                $student->name = $name;
                $student->surname = $surname;
                $student->class_id = $_class->id;
                $student->write();
            }

            // TODO ziskat predmet
            $subject = Subject::getRestricted($database, [
                new Restriction("abbreviation", $subjectAbbrev),
            ]);
            if ($subject === null)
                throw new \Exception("Předmět se zkratkou \"$subjectAbbrev\" neexistuje.");
            
            // TODO ziskat ucitele podle prijmeni
            $teacher = Teacher::getRestricted($database, [
                new Restriction("surname", $teacherSurname),
            ]);
            if ($teacher === null)
                throw new \Exception("Učitel s přijmením \"$teacherSurname\" neexistuje.");

            $exam = new Exam($database);
            $exam->student_id = $student->id;
            $exam->subject_id = $subject->id;
            $exam->original_mark = $mark;
            $exam->examiner_id = $teacher;
            
            // TODO sestavit exam
        }
    }
}
