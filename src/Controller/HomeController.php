<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Controller;

use Spse\NahradniHodnoceni\View;
use Psr\Http\Message\{ResponseInterface as Response, ServerRequestInterface as Request};

class HomeController extends AbstractController {
    public function home(Request $request, Response $response, array $args): Response {
        /** @var View */
        $view = $this->container->get("view");

        // Vyrenderuj webovou stránku.
        return $view->renderResponse($request, $response, "/home.php");
    }
}
