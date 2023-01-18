<?php

declare(strict_types=1);

use Spse\NahradniHodnoceni\Model\DatabaseEntity;
use Spse\NahradniHodnoceni\Model\ViewableProperty;
use Spse\NahradniHodnoceni\Model\ViewablePropertyType;

?>
<!DOCTYPE html>
<html lang="cs">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import dat</title>
    <link rel="shortcut icon" href="/assets/images/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="/assets/css/global.css">
    <link rel="stylesheet" href="/assets/css/table.css">
</head>

<body>
    <?php include(VIEW_ROOT . "/component/header.php") ?>
    <main>
        <p>Importing files</p>
        <form action="" method="post">
            <button name="foo" value="upvote">Upload</button>
        </form>
    </main>
</body>

</html>