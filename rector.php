<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\DowngradeSetList;
use Rector\Core\ValueObject\PhpVersion;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/config',
        __DIR__ . '/public',
        __DIR__ . '/src',
        __DIR__ . '/view',
    ]);

    // define sets of rules
    $rectorConfig->sets([
        DowngradeSetList::PHP_80,
        DowngradeSetList::PHP_74,
    ]);

    $rectorConfig->phpVersion(PhpVersion::PHP_73);
};
