<?php

declare(strict_types=1);

use Spse\NahradniHodnoceni\Model\DatabaseEntity;
use Spse\NahradniHodnoceni\Model\ViewableProperty;
use Spse\NahradniHodnoceni\Model\ViewablePropertyType;

define("itemsIntermediateData", $args["data"]["itemsIntermediateData"]);
define("options",               $args["data"]["options"]);

/**
 * Sestav zobrazitelnout hodnotu pro políčko v tabulce
 * @param ViewableProperty $property
 * @param int $index
 * @param DatabaseEntity $item
 * @return string
 */
function getDisplayText(ViewableProperty $property, int $index, DatabaseEntity $item): string {
  if ($property->type === ViewablePropertyType::INTERMEDIATE_DATA) {
    $items = itemsIntermediateData[$index][$property->name];
    return join(
      ", ",
      array_map(function ($value) {
        return $value->getFormatted();
      }, $items),
    );
  }

  $value = $item->{$property->name};
  if ($property->isSelect)
    $value = options[$property->name][$value];

  if ($property->type === ViewablePropertyType::DATETIME)
    return $value->format("j. n. Y");
  return strval($value);
}

?>
<!DOCTYPE html>
<html lang="cs">

<head>

  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>General Table</title>
  <link rel="shortcut icon" href="/assets/images/favicon.ico" type="image/x-icon">
  <link rel="stylesheet" href="/assets/css/global.css">
  <link rel="stylesheet" href="/assets/css/table.css">

</head>

<body>
  <?php include(VIEW_ROOT . "/component/header.php") ?>

  <main>
    <section class="filter_table_form">
      <h2 class="filter_head">Filtr</h2>
      <form action="" method="get" class="form_filter">
        <div class="form-row form-first_name">
          <!-- <label for="first_name">Jméno</label> -->
          <input type="text" placeholder="Jméno" name="first_name" id="first_name">
        </div>
        <div class="form-row form-last_name">
          <!-- <label for="last_name">Příjmení</label> -->
          <input type="text" placeholder="Příjmení" name="last_name" id="last_name">
        </div>
        <div class="form-row form-marks">
          <!-- <label for="marks">Známka</label> -->
          <input type="number" placeholder="Známka" name="marks" id="marks" min="1" max="5">
        </div>
        <div class="form-submit">
          <input type="submit" value="Najít" id="submit">
        </div>
      </form>
    </section>
    <section class="table_sec">
      <div class="table_buttons">
        <div class="plus_row-button">
          <button id="plus_row">+</button>
        </div>
        <div class="delete_export-buttons">
          <button id="delete">Delete</button>
          <button id="export">Export</button>
        </div>
      </div>

      <table>
        <thead>
          <tr class="tr-inputs">
            <?php foreach ($args["data"]["schema"] as $property): ?>
              <th>
                <?= $property->displayName ?>
              </th>
            <?php endforeach; ?>
            <th>
              <div>
                <input type="checkbox" name="checkbox" id="checkbox">
              </div>
            </th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($args["data"]["items"] as $index => $item): ?>
            <tr>
              <?php foreach ($args["data"]["schema"] as $property): ?>
              <td>
                <a href="<?= $args["data"]["path"] . $item->id ?>">
                  <div style="width: 100%;height: 100%;"><?= getDisplayText($property, $index, $item) ?></div>
                </a>
              </td>
              <?php endforeach; ?>

              <td>
                <div>
                  <input type="checkbox" name="checkbox" id="checkbox">
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </section>
  </main>
</body>

</html>