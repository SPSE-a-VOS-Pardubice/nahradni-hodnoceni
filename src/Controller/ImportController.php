<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Controller;

use Spse\NahradniHodnoceni\View;
use Psr\Http\Message\{ResponseInterface as Response, ServerRequestInterface as Request};

const modelNamespace = "Spse\\NahradniHodnoceni\\Model\\";

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
                    ["text" => "Velikost nahraného souboru převyšuje nastavení 'upload_max_filesize' v 'php.ini'", "link" => "/import/upload"]);
            case (UPLOAD_ERR_FORM_SIZE):
                return $view->renderResponse($request, $response, "/error.php", 
                    ["text" => "Velikost nahraného souboru převyšuje nastavení 'MAX_FILE_SIZE' v HTML formuláři", "link" => "/import/upload"]);
            case (UPLOAD_ERR_PARTIAL):
                return $view->renderResponse($request, $response, "/error.php", 
                    ["text" => "Soubor byl pouze částečně nahrán", "link" => "/import/upload"]);
            case (UPLOAD_ERR_NO_FILE):
                return $view->renderResponse($request, $response, "/error.php", 
                    ["text" => "Žádný soubor nebyl nahrán", "link" => "/import/upload"]);
            case (UPLOAD_ERR_NO_TMP_DIR):
                return $view->renderResponse($request, $response, "/error.php", 
                    ["text" => "Chybí temporary folder", "link" => "/import/upload"]);
            case (UPLOAD_ERR_CANT_WRITE):
                return $view->renderResponse($request, $response, "/error.php", 
                    ["text" => "Chyba při zapisování souboru na disk", "link" => "/import/upload"]);
            case (UPLOAD_ERR_EXTENSION):
                return $view->renderResponse($request, $response, "/error.php", 
                    ["text" => "PHP extension zamezila v nahrání souboru", "link" => "/import/upload"]);

        }

        $stream = $file->getStream();
        if($stream == null) {
            return $view->renderResponse($request, $response, "/error.php", 
                    ["text" => "\$file->getStream() je null", "link" => "/import/upload"]);
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
        
        for ($i = 0; $i < 4; $i++) {
            $values = fgetcsv($stream, null, $separator);
        }
    }
}