<?php

declare(strict_types=1);

use Spse\NahradniHodnoceni\Model\DatabaseEntity;
use Spse\NahradniHodnoceni\Model\ViewableProperty;
use Spse\NahradniHodnoceni\Model\ViewablePropertyType;

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
        <section class="table_sec">


            <table>
                <thead>
                    <tr class="tr-inputs">
                        <th>Jméno</th>
                        <th>Příjmení</th>
                        <th>Známka</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <a href="#">
                                <div style="width: 100%;height: 100%;">Vojtěch</div>
                            </a>
                        </td>
                        <td>
                            <a href="#">
                                <div style="width: 100%;height: 100%;">Fošnár</div>
                            </a>
                        </td>
                        <td>
                            <a href="#">
                                <div style="width: 100%;height: 100%;">5</div>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td><a href="#">Libor</a></td>
                        <td><a href="#">Bajer</a></td>
                        <td><a href="#">1</a></td>
                    </tr>
                    <tr>
                        <td><a href="#">Petr</a></td>
                        <td><a href="#">Fišar</a></td>
                        <td><a href="#">3</a></td>
                    </tr>
                </tbody>
            </table>
            <form action="" method="post">
                <button type="submit">Akceptovat</button>
            </form>
        </section>
    </main>

</body>

</html>