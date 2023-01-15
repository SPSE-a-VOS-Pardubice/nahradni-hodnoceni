<?php 

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Model;

class ViewableProperty {
    public string $name;
    public string $displayName;
    public ViewablePropertyType $type;

    /**
     * Značí, zda by se měl uživateli zobrazit `<select>` s možnostmi z metody `getSelectOptions`
     * @var bool
     */
    public bool $isSelect;

    public function __construct(string $name, string $displayName, ViewablePropertyType $type, bool $isSelect = false) {
        $this->name = $name;
        $this->displayName = $displayName;
        $this->type = $type;
        $this->isSelect = $isSelect;
    }
}
