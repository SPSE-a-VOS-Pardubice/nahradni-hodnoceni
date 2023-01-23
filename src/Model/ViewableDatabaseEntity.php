<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Model;

interface ViewableDatabaseEntity {
    public static function getProperties(): array;
    public static function getSelectOptions(Database $database): array;
    public function getIntermediateData(): array;

    public static function parsePostData(Database $database, array $data, int $id = 0): ParsedPostData;

    /**
     * Vyřeší rozdíly mezi `post`nutými daty a tabulkami, odebere přebytečné a přidá chybějící
     * @param ParsedPostData $parsedData
     * @return void
     */
    public static function applyPostData(ParsedPostData $parsedData): void;
}
