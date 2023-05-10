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
    case DatabaseEntityPropertyType::INTEGER:
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

function putProperty(DatabaseEntityProperty $property, $name = null) {
  if($name == null) {
    $name = $property->name;
  }
  
  ob_start(); ?>
  <div class="form-row">
    <label for="<?= $name ?>"><?= $property->displayName ?></label>
    <div class="col">
    <?php if (is_array($property->selectOptionsSource)): ?>
      <select name="<?= $name ?>">
        <?php if ($property->isNullable): ?>
          <option value=""></option>
        <?php endif; ?>
        <?php foreach($property->selectOptionsSource as $option): ?>
          <option value="<?= $option ?>"><?= $option ?></option> <!-- TODO: Přidat human-readable text a načítání vybrané možnosti -->
        <?php endforeach; ?>
      </select>
    <?php else: ?>
      <input name="<?= $name ?>"
            value="<?= is_null($property->defaultValue) ? "" : $property->defaultValue; ?>"
            type="<?= getInputType($property->type) ?>">
    <?php endif; ?>
    </div>
  </div>
  
  <?php return ob_get_clean();
}

function putExternal(DatabaseEntityProperty $property, $name, $index, array $availableOptions, array $intermediateData) {
  ob_start(); ?>
  <div class="form-row">
    <label for="<?= $name ?>"><?= $property->displayName ?></label>
    <div class="col">
      <select name="<?= $name ?>">
        <?php foreach($availableOptions as $optionIndex => $option): ?>
          <option value="<?= $optionIndex ?>" <?= $intermediateData[$index][$property->name] == $optionIndex ? "selected" : "" ?>> <!-- TODO: Zkontrolovat jestli dává smysl... -->
            <?= $option ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
  </div> 
  <?php return ob_get_clean();
}

function genIntProps($args, $intProp) {
  $semantics = [];
  
  foreach($intProp->selectOptionsSource::getProperties() as $prop) {
    $arr = [];
    
    if($prop->selectOptionsSource == $args["data"]["item"]::class) {
      continue;
    }
    
    $arr["name"] = $prop->name;
    $arr["displayName"] = $prop->displayName;
    $arr["type"] = "text";
    if(is_array($prop->selectOptionsSource) || $prop->type == DatabaseEntityPropertyType::EXTERNAL_DATA) {
      $arr["type"] = "select";
      if($prop->type == DatabaseEntityPropertyType::EXTERNAL_DATA) {
        foreach($args["data"]["explicatedExternal"][$prop->selectOptionsSource::getTableName()] as $value => $display) {
          $arr["available"][] = array("value" => $value, "display" => strval($display));
        }
      } else {
        if($prop->isNullable) {
          $arr["available"][] = array("value" => NAN, "display" => ""); // TODO: Popřemýšlet o hodnodě null
        }
        
        foreach($prop->selectOptionsSource as $value => $display) {
          $arr["available"][] = array("value" => $value, "display" => $display);
        }
      }
    }
    
    $semantics[] = $arr;
  }

  $encoded = json_encode($semantics);
  $encoded = htmlentities($encoded, ENT_QUOTES, 'UTF-8');
  return $encoded;
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
      <!-- projít všechny properties (nezahrnuje id), které nejsou intermediate data -->
      <?php foreach(array_filter($args["data"]["schema"], function($p) { 
        return $p->type !== DatabaseEntityPropertyType::INTERMEDIATE_DATA;}) 
      as $property): ?>
        <?php echo(putProperty($property)); ?>
      <?php endforeach; ?>

      <!-- projít všechny intermediate properties -->
      <?php foreach(array_filter($args["data"]["schema"], function($p) { return $p->type == DatabaseEntityPropertyType::INTERMEDIATE_DATA;}) as $property): ?>
        <div class="intermediate" data-name="<?= $property->name; ?>" data-properties="<?php echo(genIntProps($args, $property)); ?>">
          <p class="intermediate-name"><?= $property->displayName; ?></p>
          <button class="" type="button" onclick="addRecord(this);">Přidat</button>
          <?php foreach ($args["data"]["intermediateValues"][$property->name] as $entPropIdx => $entProp): ?>
            <div class="intermediate-property" data-value="<?= htmlentities(json_encode($args["data"]["intermediateValues"][$property->name][$entPropIdx]), ENT_QUOTES, 'UTF-8'); ?>"></div>  
          <?php endforeach; ?>
        </div>
      <?php endforeach; ?>

      <input type="submit" value="Aktualizovat" id="submit">
    </form>
  </main>

</body>

</html>