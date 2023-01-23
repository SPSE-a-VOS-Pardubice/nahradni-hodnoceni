<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Helpers;

class DateTimeHelper {
    /**
     * Serialize a DateTime object
     * @param \DateTime $datetime
     * @return string
     */
    public static function serialize($datetime) {
        return $datetime->format("Y-m-d H:i:s");
    }
}
