<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Model;

interface FormattableDatabaseEntity {
    public function getFormatted(): string;
}
