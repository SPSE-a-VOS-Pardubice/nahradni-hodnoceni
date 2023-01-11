<!DOCTYPE html>
<html lang="cs">

<head>

  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>General Table</title>
  <link rel="shortcut icon" href="/assets/images/favicon.ico" type="image/x-icon">
  <link rel="stylesheet" href="/assets/css/main.css">

</head>

<body>
<div class="container">
  <header>
    <nav>
      <ul>
        <li><a href="/">Home</a></li>
        <li><a href="#">Tabulky</a>
          <ul class="dropdown">
            <li><a href="">Tabulka 1 </a></li>
            <li><a href="">Tabulka 2 </a></li>
            <li><a href="">Tabulka 3</a></li>
            <li><a href="">Tabulka 4</a></li>
            <li><a href="">Tabulka 5</a></li>
            <li><a href="">Tabulka 6</a></li>
            <li><a href="">Tabulka 7</a></li>
            <li><a href="">Tabulka 8</a></li>
          </ul>
        </li>
        <li><a href="#">Import</a></li>
      </ul>
    </nav>

  </header>

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
            <th>Jméno</th>
            <th>Příjmení</th>
            <th>Známka</th>
            <th>
              <div>
                <input type="checkbox" name="checkbox" id="checkbox">
              </div>
            </th>
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
            <td>
              <div>
                <input type="checkbox" name="checkbox" id="checkbox">
              </div>
            </td>
          </tr>
          <tr>
            <td><a href="#">Libor</a></td>
            <td><a href="#">Bajer</a></td>
            <td><a href="#">1</a></td>
            <td>
              <div>
                <input type="checkbox" name="checkbox" id="checkbox">
              </div>
            </td>
          </tr>
          <tr>
            <td><a href="#">Petr</a></td>
            <td><a href="#">Fišar</a></td>
            <td><a href="#">3</a></td>
            <td>
              <div>
                <input type="checkbox" name="checkbox" id="checkbox">
              </div>
            </td>
          </tr>
        </tbody>
      </table>

    </section>
  </main>
</div>
</body>

</html>