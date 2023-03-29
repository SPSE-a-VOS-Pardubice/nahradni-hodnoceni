<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Model;

enum DatabaseEntityPropertyType {
    case String;
    case Integer;
    case DateTime;
    case EXTERNAL_DATA; // externí entita (jedno ID)
    case INTERMEDIATE_DATA; // list dat z mezitabulky (více ID) // TODO def hodnota <- nechápu TODO comment @vfosnar
}
