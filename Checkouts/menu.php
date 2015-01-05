<div class="nav">
  <ul>
    <li><a <?= basename($_SERVER['PHP_SELF']) === 'index.php' ? 'ref="self"' : 'href="index.php"'?>>Checkouts</a></li>
    <li><a <?= basename($_SERVER['PHP_SELF']) === 'candy.php' ? 'ref="self"' : 'href="candy.php"' ?>>Candy</a></li>
    <li><a <?= basename($_SERVER['PHP_SELF']) === 'catalog.php' ? 'ref="self"' : 'href="catalog.php"' ?>>Catalog</a></li>
    <li><a <?= basename($_SERVER['PHP_SELF']) === 'people.php' ? 'ref="self"' : 'href="people.php"' ?>>People</a></li>
  </ul>
</div>
