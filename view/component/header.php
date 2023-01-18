<header>
  <nav>
    <ul>
      <li><a href="/">Home</a></li>
      <li><a href="#">Tabulky</a>
        <ul class="dropdown">
          <?php foreach ($args["header"]["tables"] as $key => $name): ?>
          <li>
            <a href="<?="/table/" . $key ?>">
              <?= $name ?>
            </a>
          </li>
          <?php endforeach; ?>
        </ul>
      </li>
      <li><a href="/import/upload">Import</a></li>
    </ul>
  </nav>
</header>
