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
        <?= $args["data"]["type"] ?>
    </h1>
    
    <form action="<?= $args["data"]["path"] ?>" method="post">
        <?php foreach ($args["data"]["header"] as $key => $nazev): ?>
            <?php if ($key !== "id") { ?>
                <div>
                    <label for="<?= $key ?>">
                    <?= $nazev ?>
                    </label>
                    <br>
                
                    <?php if (gettype($args["data"]["item"]->$key) === gettype([])) { ?>
                        <select name="<?= $key ?>">
                            <?php foreach ($args["data"]["item"]->$key as $val): ?>
                            <option value="<?= $val?>">
                                <?= $val?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    <?php } else { ?>
                        <input name="<?= $key ?>" value=<?= $args["data"]["item"]->$key ?> type="<?php
                            switch (gettype($args["data"]["item"]->$key)) { 
                                case "boolean":
                                    echo "checkbox";
                                    break;  
                                case "double": 
                                case "integer": 
                                    echo "number";
                                break;
                                case "string":
                                    echo "string";
                                    break;
                            ?>">
                            <?php } ?>
                    <?php } ?>

                    <br>
                </div>
            <?php } ?>
        <?php endforeach; ?>

        <input type="submit" value="Submit">
    </form>
</body>

</html>