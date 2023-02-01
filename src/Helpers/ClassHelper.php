<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Helpers;

class ClassHelper {
    /**
     * Rozdělí textový řetězec názevu třídy ve formátu [ročník].[písneno] (např.: 1.A)
     * do dictionary na "grade" a "label".
     * @param string $name
     * @throws \Exception TODO
     * @return array
     */
    public static function parseClassName(string $name): array {
        $explodedClass = explode(".", $name);
        //TODO: Zkontrolovat validitu textového řetězce třídy
        if(false) {
            throw new \Exception("Jméno třídy " . $name . " je ve špatném formátu");
        }

        return ["grade" => intval($explodedClass[0]), "label" => $explodedClass[1]];
    }
}
