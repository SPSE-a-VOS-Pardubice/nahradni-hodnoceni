<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Model;

enum ViewablePropertyType {
    case BOOLEAN;
    case INTEGER;
    case DOUBLE;
    case STRING;
    case DATETIME;
    case INTERMEDIATE_DATA;
}
