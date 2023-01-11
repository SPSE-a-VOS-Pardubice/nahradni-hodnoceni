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
        $test = $request->getUploadedFiles();
        $a = $test[0];
    }
}