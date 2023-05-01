<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Model;

class Restriction {
    // TODO: možná není classname potřeba. stačí property a value
    public string $classname;
    public string $propertyName;
    public mixed $value;
}
