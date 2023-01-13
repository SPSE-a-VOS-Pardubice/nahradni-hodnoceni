<?php

declare(strict_types=1);

enum Type {
    case array;
    case string;
    case integer;
    case datetime;

    public function getType(): string {
        return $this->name;
    }
}