<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Model;

abstract class EditableDatabaseEntity extends ViewableDatabaseEntity{
    public abstract static function getSelectOptions(Database $database): array;
}