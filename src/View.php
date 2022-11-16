<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni;

use Psr\Http\Message\{ResponseInterface as Response, ServerRequestInterface as Request};

class View {
    public $root;

    public function __construct(string $root) {
        $this->root = $root;
    }

    public function render(string $path, array $args): string {
        define("VIEW_ROOT", $this->root . "/view");
        ob_start();
        include(VIEW_ROOT . $path);
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }

    public function renderResponse(Request $request, Response $response, string $path, $dataArgs = []): Response {
        // Spoj argumenty z middlewarů s parametrem argumentů
        $args = $request->getAttributes();
        $args["data"] = $dataArgs;

        // Vyrenderuj html a zapiš ho na výstup
        $html = $this->render($path, $args);
        $response->getBody()->write($html);

        return $response;
    }

    public function renderJson(Response $response, $value): Response {
        $serialized = json_encode($value);
        $response->getBody()->write($serialized);

        return $response;
    }
}
