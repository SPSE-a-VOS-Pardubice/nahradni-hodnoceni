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
            <?php foreach ($args["data"]["header"] as $nazev): ?>
            <th>
                <?= $nazev; ?>
            </th>
            <?php endforeach; ?>
        </tr>
        <?php foreach ($args["data"]["items"] as $predmet): ?>
        <tr>
            <?php foreach ($args["data"]["header"]  as $key => $nazev): ?>
            <td>
                <a href="<?= $args["data"]["path"] . $predmet->id ?>">
                    <?= $predmet->$key ?>
                </a>
            </td>
            <?php endforeach; ?>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php foreach ($args["data"]["list"]  as $table): ?>
    <a href="<?= "/table/" . $table ?>">
        <?= $table ?>
    </a>
    <?php endforeach; ?>
</body>

</html>