<?php

declare(strict_types=1);

use Spse\NahradniHodnoceni\Model\DatabaseEntity;
use Spse\NahradniHodnoceni\Model\ViewableProperty;
use Spse\NahradniHodnoceni\Model\ViewablePropertyType;

use Spse\NahradniHodnoceni\Controller\PreviewTableEntry;

?>
<!DOCTYPE html>
<html lang="cs">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Import dat - table</title>
    <link rel="shortcut icon" href="/assets/images/favicon.ico" type="image/x-icon" />
    <link rel="stylesheet" href="/assets/css/global.css" />
    <link rel="stylesheet" href="/assets/css/import.css" />
</head>

<body>
    <?php include(VIEW_ROOT . "/component/header.php") ?>

    <main>
        <section class="table_sec" style="justify-content:normal;">
            <table>
                <thead>
                    <tr class="tr-inputs">
                        <th>Třída</th>    
                        <th>Jméno</th>
                        <th>Příjmení</th>
                        <th>Předmět</th>
                        <th>Známka</th>
                        <th>Zkoušející</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($args["data"]["entries"] as $entry): ?>
                    <tr>
                    <?php foreach ($entry->getArray() as $value): ?>
                        <td><a href="#"><?= $value ?></a></td>
                    <?php endforeach; ?>
                    </tr>
                </tbody>
                <?php endforeach; ?>
            </table>
            <form action="/import/preview" method="post">
                <button type="submit">Akceptovat</button>
            </form>
        </section>
    </main>

</body>

</html>