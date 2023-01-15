<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Model;

interface ViewableDatabaseEntity {
    public static function getProperties(): array;
    public static function getSelectOptions(Database $database): array;
    public function getIntermediateData(): array;
}
