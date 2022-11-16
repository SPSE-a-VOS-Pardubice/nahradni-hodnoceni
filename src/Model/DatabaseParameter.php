<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Model;

class DatabaseParameter {
    public string   $name;
    public          $value;
    public int      $type;

    public function __construct(string $name, $value, int $type = -1) {

        // Automaticky zjisti typ hodnoty pokud je -1.
        if ($type === -1) {
            switch (gettype($value)) {
                case "boolean":
                    $type = \PDO::PARAM_BOOL;
                    break;

                case "integer":
                    $type = \PDO::PARAM_INT;
                    break;

                case "double": # Hodnota "double" ve skutečnosti značí float.
                case "string":
                    $type = \PDO::PARAM_STR;
                    break;

                case "NULL":
                    $type = \PDO::PARAM_NULL;
                    break;

                default:
                    throw new \InvalidArgumentException("Nelze určit typ proměnné.");
            }
        }

        $this->name = $name;
        $this->value = $value;
        $this->type = $type;
    }
}
