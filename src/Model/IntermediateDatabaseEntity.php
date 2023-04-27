<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Model;

abstract class IntermediateDatabaseEntity extends DatabaseEntity {
    public function write(): void {
        // TODO: insert|update
    }

    public function remove(): void {
        // TODO: delete
    }

    // TODO vytvořít statické metody pro manipulaci se záznamy při POSTu (updateData)

    /**
     * @return ?string název vlastnosti, která odkazuje na 
     * 
     * probléby metody, vrátí pouze první výskyt, nepočítá s tím, že jedna mezitabulka odkazuje dvakrát do stejné tabulky
     */
    public static function getPropNameFromClass(string $class): ?string {
        $properties = array_filter(static::getProperties(), function (DatabaseEntityProperty $property) {
            return $property->type !== DatabaseEntityPropertyType::EXTERNAL_DATA;
        });

        foreach ($properties as $property) { 
            if ($property->selectOptionsSource === $class) {
                return $property->name;
            }
        }

        return null;
    }
  
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