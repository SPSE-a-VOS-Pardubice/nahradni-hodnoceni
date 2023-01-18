<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Controller;

use Spse\NahradniHodnoceni\View;
use Psr\Http\Message\{ResponseInterface as Response, ServerRequestInterface as Request};

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

        $this->parse($file->getStream());
        return $this->redirect($response, "/import/preview");
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
    
    public function parse($stream, string $separator = ";") {
        $result = [];

        $strings = explode("\n", $stream->__toString());
        $lastIndex = count($strings) - 1;
        if($strings[$lastIndex] == "") {
            unset($strings[$lastIndex]);
        }

        for ($i = 0; $i < count($strings); $i++) {
            $values = str_getcsv($strings[$i], $separator);
            $predmet = $values[csvMapping["predmet"]];
        }
    }
}