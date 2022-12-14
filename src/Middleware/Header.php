<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Middleware;

use Psr\Container\ContainerInterface as Container;
use Psr\Http\Message\{ResponseInterface as Response, ServerRequestInterface as Request};
use \Slim\Handlers\Strategies\RequestHandler as RequestHandler;

const tables = [
    "predmety" => "Predmety",
    "studenti" => "Studenti",
    "tridy" => "Tridy",
    "zkousky" => "Zkousky",
    "ucitele" => "Ucitele",
    "priznaky" => "Priznaky",
    "ucebny" => "Ucebny"
];

class Header {
    protected Container $container;


    public function __construct(Container $container) {
        $this->container = $container;
    }

    public function __invoke(Request $request, RequestHandler $handler): Response {

        $request = $request->withAttribute("header", [
            "tables" => tables
        ]);

        return $handler->handle($request);
    }
}
