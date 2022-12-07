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


    <h1 for=""><?= $args["data"]["type"] ?></h1>
    <form action="<?= $args["data"]["path"]?>" method="post">
        <?php foreach ($args["data"]["header"] as $key => $nazev): ?>
            <label for="<?= $key?>"><?= $nazev?></label>
            <br>
            <input name="<?= $key?>" value=<?= $args["data"]["item"]->$key?>>
            <br>
        <?php endforeach; ?>
            
        <input type="submit" value="Submit">
    </form>
</body>

</html>