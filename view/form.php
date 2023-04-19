<?php

declare(strict_types=1);

use Spse\NahradniHodnoceni\Model\DatabaseEntity;
use Spse\NahradniHodnoceni\Model\DatabaseEntityProperty;
use Spse\NahradniHodnoceni\Model\DatabaseEntityPropertyType;

// je null pouze když uživatel přidává nový záznam
$item = $args["data"]["item"];

function getDefaultInputValue($item, DatabaseEntityProperty $property): string {
  if (is_null($item) || is_null($item->{$property->name}))
      return "";
  
  if ($property->type === DatabaseEntityPropertyType::DATE_TIME)
    return $item->{$property->name}->format("Y-m-d\TH:i");

  return strval($item->{$property->name});
}

function getInputType(DatabaseEntityPropertyType $propType): string {
  switch ($propType) {
    /*case DatabaseEntityPropertyType::BOOLEAN:
      return "checkbox";*/
    case DatabaseEntityPropertyType::INTEGER:
    /*case DatabaseEntityPropertyType::DOUBLE:*/
      return "number";
    case DatabaseEntityPropertyType::STRING:
      return "text";
    case DatabaseEntityPropertyType::DATE_TIME:
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

  if ($objects == null) {
    $objects = [];
  }

  $objectsMap = [];
  foreach ($objects as $object) {
    $objectsMap[$object->id] = $object->getFormatted();
  }

  return json_encode([
    "objects" => $objectsMap,
    "options" => $options,
  ]);
}

function isSelected($item, $optionName, $value): bool {
  if ($item == null)
    return false;

  return $optionName === $item->$value;
}

?>
<!DOCTYPE html>
<html lang="cs">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Form</title>
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
          <div class="form-row" <?= $property->type === DatabaseEntityPropertyType::INTERMEDIATE_DATA ? sprintf("data-intermediate=\"%s\"", htmlspecialchars(encodeIntermediateDataForFrontend($args["data"]["intermediateData"] === [] ? [] : $args["data"]["intermediateData"][$property->name], $args["data"]["options"][$property->name]))) : "" ?>>
            <label for="<?= $property->name ?>">
              <?= $property->displayName ?>
            </label>
            <div class="col">
              <?php if ($property->type !== DatabaseEntityPropertyType::INTERMEDIATE_DATA): ?>
                <!-- select -->
                <?php if (is_array($property->selectOptionsSource)): ?>
                  <select name="<?= $property->name ?>">
                    <?php if ($property->isNullable): ?>
                      <option value=""></option>
                    <?php endif; ?>
                    <?php foreach ($args["data"]["options"][$property->name] as $optionName => $optionDisplayName): ?>
                      <option value="<?= $optionName ?>" <?= /**$optionName === $value */ isSelected($item, $optionName, $property->name) ? "selected" : "" ?>>
                        <?= $optionDisplayName ?>
                      </option>
                    <?php endforeach; ?>
                  </select>

                <!-- everything else -->
                <?php else: ?>
                  <input name="<?= $property->name ?>"
                    value="<?= getDefaultInputValue($item, $property) ?>"
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