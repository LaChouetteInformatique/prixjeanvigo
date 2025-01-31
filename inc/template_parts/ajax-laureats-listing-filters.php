<?php
// Si une requête ajax a eu lieu, $data existe, sinon, valeur par défaut (pas de filtres)

// DB_PLUGIN\Inc\Helpers\pre_print_r($_GET);
?>

<form class="js-ll-filters-form" method="POST" <?php /* action="laureats_listing" name="filter-ll" */ /* Handled via JS only */?>>
  <div class="search-fieldset">
    <label for="ll-search-filter">Recherche</label>
    <input type="text" id="ll-search-filter" name="s" placeholder="Tapez ici" <?= !empty($_POST['s']) ? 'value="'.sanitize_text_field($_POST['s']).'"' : ''?>>
  </div>

  <div class="price-type-fieldset">
    <label for="ll-price-filter">Type de prix</label>
    <select id="ll-price-filter" name="price">

      <?php
      $price = 'default' ; 
      if(array_key_exists('price', $_POST) ) { $price = $_POST['price']; }
      ?>

      <option <?= $price == 'default' ? 'selected' : ''?> value="default">Tous</option>
      <option <?= $price == 'court-metrage' ? 'selected' : ''?> value="court-metrage">Court métrage</option>
      <option <?= $price == 'long-metrage'  ? 'selected' : ''?> value="long-metrage">Long métrage</option>
      <option <?= $price == 'vigo-d-honneur'? 'selected' : ''?> value="vigo-d-honneur">Vigo d'honneur</option>
    </select>
  </div>

  <div class="dates-fieldset">
    <label for="ll-date-filter">Année</label>
    <select id="ll-date-filter" name="date">
      <?php
      $year = 1951; ?>
      <option value="default">Toutes</option> <?php
      while($year <= date('Y') ) : ?>
        <option value="<?= $year ?>"><?= $year ?></option> <?php 
        $year++;
      endwhile; ?>
    </select>
  </div>

  <div class="order-dir-fieldset">
    <label for="ll-sort-filter">Ordre</label>
    <select id="ll-sort-filter" name="order">
      <option value="default">Les plus récents d'abord</option>
      <option value="asc">Les plus anciens d'abord</option>
    </select>
  </div>

  <button type="submit" class="search-ll-btn btn btn-primary"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" height="1em" width="1em" fill="currentColor"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M416 208c0 45.9-14.9 88.3-40 122.7L502.6 457.4c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L330.7 376c-34.4 25.2-76.8 40-122.7 40C93.1 416 0 322.9 0 208S93.1 0 208 0S416 93.1 416 208zM208 352a144 144 0 1 0 0-288 144 144 0 1 0 0 288z"/></svg> Chercher</button>
</form>
