<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Model;

class Restriction {
    public string $propertyName;
    public mixed $value;

    public function __construct(string $propertyName, mixed $value) {
        $this->propertyName = $propertyName;
        $this->value = $value;
    }
}
