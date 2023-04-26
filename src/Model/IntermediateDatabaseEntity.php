<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Model;

abstract class IntermediateDatabaseEntity extends DatabaseEntity
{
    public function write(): void {
        // TODO: insert|update
    }

    public function remove(): void {
        // TODO: delete
    }

    // TODO vytvořít statické metody pro manipulaci se záznamy při POSTu (updateData)

    /**
     * @var array<FullDatabaseEntity> omezení, podle kterých se vyfitrují záznamy z mezitabulky
     * @return array<IntermediateDatabaseEntity> vyfiltrované záznamy z mezitabulky
     * 
     * $restrictions je pole restrikcí `Restriction[]`
     */
    public static function getRestricted(Database $database, array $restrictions): array {
        // TODO obecná metoda pro získání záznamů pomocí restrikce
        // blokováno QueryBuilderem
        return [];
    }
}