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
    <a href="<?= $args["data"]["path"]?>new">Přidat nový</a>

    <table>
        <tr>
            <?php foreach ($args["data"]["schema"] as $vlastnost): ?>
            <th>
                <?= $vlastnost["name"] ?>
            </th>
            <?php endforeach; ?>
        </tr>
        <?php foreach ($args["data"]["items"] as $predmet): ?>
        <tr>
            <?php foreach ($args["data"]["schema"]  as $vlastnost): ?>
            <td>
                <a href="<?= $args["data"]["path"] . $predmet->id ?>">
                    <?= $predmet->{$vlastnost["propertyName"]} ?>
                </a>
            </td>
            <?php endforeach; ?>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php foreach ($args["header"]["tables"]  as $key => $name): ?>
    <a href="<?= "/table/" . $key ?>">
        <?= $name ?>
    </a>
    <?php endforeach; ?>
</body>

</html>