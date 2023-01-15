<?php

declare(strict_types=1);

use Spse\NahradniHodnoceni\Model\ViewableProperty;
use Spse\NahradniHodnoceni\Model\ViewablePropertyType;

// je null pouze když uživatel přidává nový záznam
define("item", $args["data"]["item"]);

function getValue(ViewableProperty $property): mixed {
    if ($property->type === ViewablePropertyType::DATETIME) {
        return item->{$property->name}->format("Y-m-d\TH:i");
    }

    return item->{$property->name};
}

function getDefaultInputValue(ViewableProperty $property): string {
    if (is_null(item))
        return "";

    return strval(getValue($property));
}

function getInputType(ViewablePropertyType $propType): string {
    switch ($propType) {
        case ViewablePropertyType::BOOLEAN:
            return "checkbox";
        case ViewablePropertyType::INTEGER:
        case ViewablePropertyType::DOUBLE:
            return "number";
        case ViewablePropertyType::STRING:
            return "text";
        case ViewablePropertyType::DATETIME:
            return "datetime-local";
    }

    throw new InvalidArgumentException();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>

    <h1 for="">
        <?= $args["data"]["type"]?>
    </h1>
    <form action="<?= $args["data"]["path"] ?>" method="post">

        <!-- řádek s vlastností -->
        <?php foreach ($args["data"]["schema"] as $property): ?>
            <?php if ($property->name !== "id"): ?>
                <div data-isList="<?= $property->isIntermediate ? 1 : 0 ?>">
                    <label for="<?= $property->name ?>">
                        <?= $property->displayName ?>
                    </label>
                    <br>

                    <!-- pokud je ve vlastnosti více hodnot, vyrenderuj všechny -->
                    <?php
                    $values = getValue($property);
                    if (!$property->isIntermediate) {
                        $values = [$values];
                    }
                    ?>
                    <?php foreach ($values as $value): ?>
                        <?php if ($property->isSelect): ?>
                            <select name="<?= $property->name ?>">
                                <?php foreach ($args["data"]["options"][$property->name] as $optionName => $optionDisplayName): ?>
                                    <option value="<?= $optionName ?>" <?= $optionName === $value ? "selected" : "" ?>>
                                        <?= $optionDisplayName ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        <?php else: ?>
                            <input name="<?= $property->name ?>"
                                value="<?= getDefaultInputValue($property) ?>"
                                type="<?= getInputType($property->type) ?>">
                        <?php endif; ?>
                        
                        <br>
                    <?php endforeach; ?>

                    <!-- pokud je ve vlastnosti více hodnot, vytvoř možnost přidat další -->
                    <!-- TODO -->
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
        <input type="submit" value="Submit">
    </form>
</body>

</html>