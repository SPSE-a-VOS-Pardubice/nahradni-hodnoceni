<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Model;

enum DatabaseEntityPropertyType {
    case String;
    case Integer;
    case DateTime;
    case Intermediate_data;
}