<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Controller;

use Psr\Container\ContainerInterface as Container;
use Psr\Http\Message\{ResponseInterface as Response, ServerRequestInterface as Request};

abstract class AbstractController {
    /**
     * @var Container
     */
    protected $container;

    public function __construct(Container $container) {
        $this->container = $container;
    }

    public function redirect(Response $response, string $url, int $status_code = 302): Response {
        return $response
            ->withHeader("Location", $url)
            ->withStatus($status_code);
    }
}
