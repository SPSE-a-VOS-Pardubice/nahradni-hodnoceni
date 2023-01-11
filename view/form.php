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
        <?php foreach ($args["data"]["schema"] as $vlastnost): ?>
            <?php if ($vlastnost["propertyName"] !== "id"): ?>
                <div>
                    
                    <label for="<?= $vlastnost["propertyName"] ?>">
                    <?= $vlastnost["name"] ?>
                    </label>
                    
                    <br>

                    <?php if ($vlastnost["type"] === gettype([])):?>
                        
                        <select name="<?= $vlastnost["propertyName"] ?>" data-isList="<?= $vlastnost["isList"] ? 1 : 0 ?>" >
                            <?php foreach ($args["data"]["options"][$vlastnost["propertyName"]] as $name => $displayName): ?>
                                <option value="<?= $name?>">
                                    <?= $displayName?>
                                </option>   
                            <?php endforeach; ?>
                           
                        </select>
                    <?php  else:  ?>
                        <input name="<?= $vlastnost["propertyName"] ?>" value="<?= $args["data"]["item"]!== null ? ($vlastnost["type"] == "datetime" ? $args["data"]["item"]->{$vlastnost["propertyName"]}->format("Y-m-d\TH:i") : $args["data"]["item"]->{$vlastnost["propertyName"]}) : "" ?>" type="<?php
                            switch ($vlastnost["type"]) { 
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
                                case "datetime": //TODO: prasárna
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