<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Model;

abstract class ViewablePropertyType {
    const BOOLEAN = 1;
    const INTEGER = 2;
    const DOUBLE = 3;
    const STRING = 4;
    const DATETIME = 5;
    const INTERMEDIATE_DATA = 6;
}
