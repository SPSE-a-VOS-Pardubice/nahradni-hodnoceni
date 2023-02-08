<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Controller;

use Spse\NahradniHodnoceni\Model\Classroom;
use Spse\NahradniHodnoceni\Model\_Class;
use Spse\NahradniHodnoceni\View;
use Spse\NahradniHodnoceni\Model\Exam;
use Spse\NahradniHodnoceni\Model\Student;
use Spse\NahradniHodnoceni\Model\Subject;
use Spse\NahradniHodnoceni\Model\Teacher;
use Spse\NahradniHodnoceni\Helpers\ClassHelper;
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

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $surname;

    /**
     * @var string
     */
    private $subject;

    /**
     * @var string
     */
    private $mark;
    
    /**
     * @var string
     */
    private $teacher;
    
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

        $importedData = $this->parse($file->getStream());

        session_start();
        foreach ($importedData as $k => $v) {
            foreach($importedData[$k] as $index => $object) {
                $_SESSION["importedData"][$k][$index] = $object->getPropertyValues();
            }
        }

        $previewEntries = $this->consturctPreviewTableEntries($importedData);
        
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
        
        if(!isset($_SESSION["importedData"])) {
            session_destroy();
            return $view->renderResponse($request, $response, "/error.php", 
                    ["message" => "Chyba s PHP session", "backLink" => "/import/upload"]);
        }

        $database = $this->container->get("database");
        $importedData = array();

        // Deserializace proměnné session
        foreach ($_SESSION["importedData"] as $k => $v) {
            foreach($_SESSION["importedData"][$k] as $index => $object) {
                // TODO: Najít lepší způsob....
                if($k == "exams") {
                    $importedData[$k][$index] = new Exam($database);
                } else if($k == "students") {
                    $importedData[$k][$index] = new Student($database);
                } else if($k == "classes") {
                    $importedData[$k][$index] = new _Class($database);
                }
                
                $importedData[$k][$index]->setPropertyValues($_SESSION["importedData"][$k][$index]);
            }
        }

        foreach($importedData["classes"] as $index => $object) {
            $object->write();
            if(isset($importedData["students"][$index])) {
                $importedData["students"][$index]->__set("class_id", $object->__get("id"));
            }
            $id = $importedData["students"][$index]->class_id;
            echo "zapisuji tridu $id\n<br>";
        }

        foreach($importedData["students"] as $index => $object) {
            if ($object->class_id == 0)
                continue; // TODO "oprava"
            $object->write();
            if(isset($importedData["exams"][$index])) {
                $importedData["exams"][$index]->student_id = $object->id;
            }
        }
        
        foreach($importedData["exams"] as $index => $object) {
            $object->write();
        }

        $_SESSION["importedData"] = [];
        session_destroy();
        
        return $this->redirect($response, "/table/zkousky");
    }
    
    public function parse($stream, string $separator = ";") {
        $database = $this->container->get("database");

        $importedClasses = array();
        $importedExams = array();
        $importedStudents = array();
        
        $strings = explode("\n", $stream->__toString());
        $lastIndex = count($strings) - 1;
        if($strings[$lastIndex] == "") {
            unset($strings[$lastIndex]);
        }

        for ($i = 1; $i < count($strings); $i++) {
            $values = str_getcsv($strings[$i], $separator);

            $student = null;
            $class = null;
            
            $exam = new Exam($database);
            $exam->__set("id", 0);
            
            // Získávání student ID
            try {
                // $student = Student::getFromNameSurnameClass($database, $values[csvMapping["jmeno"]], $values[csvMapping["prijmeni"]], $values[csvMapping["trida"]]);
                // $exam->__set("student_id", $student->__get("id"));
                // $student = null;
                throw new \RuntimeException();
            } catch(\RuntimeException $e) {
                // Vytvořit záznam nového studenta, pokud neexistuje v databázi
                $student = new Student($database);
                $student->__set("id", 0);
                $student->__set("name", $values[csvMapping["jmeno"]]);
                $student->__set("surname", $values[csvMapping["prijmeni"]]);
                $student->__set("class_id", 0);
                
                // Najít, jestli již v databázi existuje záznam o třídě studenta
                $parsedClassName = ClassHelper::parseClassName($values[csvMapping["trida"]]);
                // $allClasses = _Class::getAll($database);
                // for($j = 0; $j < count($allClasses); $j++) {
                //     if ($allClasses[$j]->getProperty("grade") == $parsedClassName["grade"] &&
                //         $allClasses[$j]->getProperty("label") == $parsedClassName["label"]) {
                //         $student->__set("class_id", $allClasses[$j]->getProperty("id"));
                //         break;
                //     }
                // }
                
                if($student->class_id == 0) {
                    // Vytvořit nový zázam třídy, pokud není již v databázi
                    $class = new _Class($database);
                    $class->__set("id", 0);
                    $class->__set("year", intval(date("Y")));
                    $class->__set("grade", $parsedClassName["grade"]);
                    $class->__set("label", $parsedClassName["label"]);
                    $class->__set("class_teacher_id", null);
                }

                $importedStudents[$i] = $student;
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
            $exam->__set("final_mark", null);
            $exam->__set("time", null);

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

            if($class != null) {
                $importedClasses[$i] = $class;
            }

            if($student != null) {
                $importedStudents[$i] = $student;
            }
            
            $importedExams[$i] = $exam;
        }

        return ["classes" => $importedClasses, "exams" => $importedExams, "students" => $importedStudents];
    }

    public function addToImportErrorLog(string $string) {
        var_dump($string);
    }

    public function consturctPreviewTableEntries($data): array {
        $database = $this->container->get("database");

        $exams = $data["exams"];
        
        $res = array();
        foreach ($exams as $i => $value) {
            $student = null;
            $class = null;

            if(isset($data["students"][$i])) {
                $student = $data["students"][$i];
            } else {
                $student = Student::get($database, intval($exams[$i]->getProperty("student_id")));
            }
            
            if(isset($data["classes"][$i])) {
                $class = $data["classes"][$i];
            } else {
                $class = _Class::get($database, intval($student->getProperty("class_id")));
            }
            
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