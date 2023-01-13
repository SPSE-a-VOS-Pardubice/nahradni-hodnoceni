<?php 

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Model;
use Type;

class ViewableProperty {
    private string $propertyName;
    private string $name;
    private Type $type;
    private bool $isList;

    public function __construct(string $propertyName, string $name, Type $type, bool $isList = false) {
        $this->propertyName = $propertyName;
        $this->name = $name;
        $this->type = $type;
        $this->isList = $isList;
    }

	/**
	 * @return string
	 */
	public function getPropertyName(): string {
		return $this->propertyName;
	}

	/**
	 * @return string
	 */
	public function getName(): string {
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function getType(): string {
		return $this->type->getType();
	}
	
	/**
	 * @return bool
	 */
	public function isList(): bool {
		return $this->isList;
	}
}