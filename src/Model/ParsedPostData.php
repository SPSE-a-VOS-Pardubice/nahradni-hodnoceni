<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Model;

class ParsedPostData {
    /**
     * @var ViewableDatabaseEntity
     */
    public $model;

    /**
     * @var array<string, array<string, string>>
     */
    public $intermediate;

    /**
     * @param ViewableDatabaseEntity $model
     * @param array<string, array<string, string>> $intermediate
     */
    public function __construct(ViewableDatabaseEntity $model, array $intermediate) {
        $this->model        = $model;
        $this->intermediate = $intermediate;
    }
}
