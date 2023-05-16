<?php

declare(strict_types=1);

use Spse\NahradniHodnoceni\Model\DatabaseEntity;
use Spse\NahradniHodnoceni\Model\DatabaseEntityProperty;
use Spse\NahradniHodnoceni\Model\DatabaseEntityPropertyType;

// je null pouze když uživatel přidává nový záznam
$item = $args["data"]["item"];

/**
 * Převede DatabaseEntityPropertyType na typ HTML inputu
 * @param DatabaseEntityPropertyType $propType typ, který bude převeden
 */
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
 * Vyrendruje property v HTML pro jednu property, která není intermediate
 * @param DatabaseEntityProperty $property daná property
 */
function putProperty(DatabaseEntityProperty $property, $name = null) {
  $name = $property->name;
  
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

/**
 * Vygeneruje textový řetězec, který je zakódovaný JSON, který v sobě obsahuje informace
 * o dané intermediate property (její selecty a hodnoty daných selectů).
 * Příklad:
 * [
 *  {
 *   "name": "subject_id",
 *   "displayName": "Předmět",
 *   "type": "select",
 *   "available": [
 *     {
 *       "value": 1,
 *       "display": "Číslicová Technika"
 *     },
 *     {
 *       "value": 2,
 *       "display": "Servis PC"
 *     },
 *     ...
 *   ]
 *  },
 *  ...
 * ] 
 * 
 * @param mixed $args přeposlané $args z hlavního scope (form.php)
 * @param mixed $intProp daná intermediate property
 */
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