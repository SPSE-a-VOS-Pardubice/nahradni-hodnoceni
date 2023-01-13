<?php

declare(strict_types=1);

use Spse\NahradniHodnoceni\Model\Predmet;

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
        <?php foreach ($args["data"]["schema"] as $vlastnost): ?>
            <?php if ($vlastnost->getPropertyName() !== "id"): ?>
                <div>
                    <?php $warType?>
                    <label for="<?= $vlastnost->getPropertyName() ?>">
                        <?= $vlastnost->getName() ?>
                    </label>

                    <br>

                    <?php if ($vlastnost->getType() === gettype([])): ?>

                        <select name="<?= $vlastnost->getPropertyName() ?>" data-isList="<?= $vlastnost->isList() ? 1 : 0 ?>">
                            <?php foreach ($args["data"]["options"][$vlastnost->getPropertyName()] as $name => $displayName): ?>
                                <option value="<?= $name ?>">
                                    <?= $displayName ?>
                                </option>
                            <?php endforeach; ?>

                        </select>
                    <?php else: ?>
                        <input name="<?= $vlastnost->getPropertyName() ?>"
                            value="<?= $args["data"]["item"] !== null ? ($vlastnost->getType() == "datetime" ? $args["data"]["item"]->{$vlastnost->getPropertyName()}->format("Y-m-d\TH:i") : $args["data"]["item"]->{$vlastnost->getPropertyName()}) : "" ?>"
                            type="<?php
                                switch ($vlastnost->getType()) {
                                    case "boolean":
                                        echo "checkbox";
                                        break;
                                    case "double":
                                    case "integer":
                                        echo "number";
                                        break;
                                    case "string":
                                        echo "text";
                                        break;
                                    case "datetime":
                                        echo "datetime-local";
                                        break;
                                    }
                            ?>">
                    <?php endif; ?>
                    
                    <br>
                </div>

            <?php endif; ?>
        <?php endforeach; ?>

        <input type="submit" value="Submit">
    </form>
</body>

</html>