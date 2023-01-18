<?php

declare(strict_types=1);

use Spse\NahradniHodnoceni\Model\DatabaseEntity;
use Spse\NahradniHodnoceni\Model\ViewableProperty;
use Spse\NahradniHodnoceni\Model\ViewablePropertyType;

// je null pouze když uživatel přidává nový záznam
define("item", $args["data"]["item"]);

function getDefaultInputValue(ViewableProperty $property): string {
  if (is_null(item))
      return "";
  
  if ($property->type === ViewablePropertyType::DATETIME)
    return item->{$property->name}->format("Y-m-d\TH:i");

  return strval(item->{$property->name});
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

/**
 * @param array<DatabaseEntity> $objects
 * @param array<string, string> $options
 * @return string
 */
function encodeIntermediateDataForFrontend($objects, $options) {

  $objectsMap = [];
  foreach ($objects as $object) {
    $objectsMap[$object->id] = $object->getFormatted();
  }

  return json_encode([
    "objects" => $objectsMap,
    "options" => $options,
  ]);
}

?>
<!DOCTYPE html>
<html lang="cs">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>General Form</title>
  <link rel="shortcut icon" href="/assets/images/favicon.ico" type="image/x-icon">
  <link rel="stylesheet" href="/assets/css/global.css">
  <link rel="stylesheet" href="/assets/css/form.css">
  <script src="/assets/js/form.js"></script>
</head>

<body onload="onload()">
  <?php include(VIEW_ROOT . "/component/header.php") ?>

  <main>
    <h2 class="general_head">Název</h2>
    <form action="" method="post" class="general_form">

      <!-- řádek s vlastností -->
      <?php foreach ($args["data"]["schema"] as $property): ?>
        <?php if ($property->name !== "id"): ?>
          <div class="form-row" <?= $property->type === ViewablePropertyType::INTERMEDIATE_DATA ? sprintf("data-intermediate=\"%s\"", htmlspecialchars(encodeIntermediateDataForFrontend($args["data"]["intermediateData"][$property->name], $args["data"]["options"][$property->name]))) : "" ?>>
            <label for="<?= $property->name ?>">
              <?= $property->displayName ?>
            </label>
            <div class="col">
              <?php if ($property->type === ViewablePropertyType::INTERMEDIATE_DATA): ?>
                <?php foreach ($args["data"]["intermediateData"][$property->name] as $object): ?>

                  <!-- render select for each object -->
                  <select name="<?= $property->name ?>">
                    <?php foreach ($args["data"]["options"][$property->name] as $optionName => $optionDisplayName): ?>
                      <option value="<?= $optionName ?>" <?= $optionName === $object->id ? "selected" : "" ?>>
                        <?= $optionDisplayName ?>
                      </option>
                    <?php endforeach; ?>
                  </select>

                <?php endforeach; ?>
              <?php else: ?>

                <!-- select -->
                <?php if ($property->isSelect): ?>
                  <select name="<?= $property->name ?>">
                    <?php foreach ($args["data"]["options"][$property->name] as $optionName => $optionDisplayName): ?>
                      <option value="<?= $optionName ?>" <?= $optionName === $value ? "selected" : "" ?>>
                        <?= $optionDisplayName ?>
                      </option>
                    <?php endforeach; ?>
                  </select>

                <!-- everything else -->
                <?php else: ?>
                  <input name="<?= $property->name ?>"
                    value="<?= getDefaultInputValue($property) ?>"
                    type="<?= getInputType($property->type) ?>">
                <?php endif; ?>

                <!-- <div class="form-active form-row">
                  <label for="active" id="active_label">Aktivní</label>
                  <div class="col">
                    <input type="checkbox" name="active" id="checkbox">
                  </div>
                </div> -->
              <?php endif; ?>
            </div>
          </div>
        <?php endif; ?>
      <?php endforeach; ?>

      <input type="submit" value="Aktualizovat" id="submit">
    </form>
  </main>

</body>

</html>