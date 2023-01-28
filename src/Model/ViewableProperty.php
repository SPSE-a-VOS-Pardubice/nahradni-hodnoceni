<?php 

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Model;

class ViewableProperty {
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $displayName;

    /**
     * @var int
     */
    public $type;

    /**
     * Značí, zda by se měl uživateli zobrazit `<select>` s možnostmi z metody `getSelectOptions`
     * @var bool
     */
    public $isSelect;

    public function __construct(string $name, string $displayName, int $type, bool $isSelect = false) {
        $this->name = $name;
        $this->displayName = $displayName;
        $this->type = $type;
        $this->isSelect = $isSelect;
    }
}
