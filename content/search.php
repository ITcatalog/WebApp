<?php
$searchTerm = $_GET['search'];


$sparql = '
SELECT *
WHERE {
    ?s skos:prefLabel ?label.
    ?s dcterms:description ?serviceDescription.
    FILTER regex(lcase(str(?label)), lcase("'.$searchTerm.'")) .
}
';

$result = $db->query( $sparql );
if( !$result ) { print $db->errno() . ": " . $db->error(). "\n"; exit; }

?>


<div class="mdl-cell mdl-cell--12-col mdl-grid mdl-color--white mdl-shadow--2dp">
  <form name="" action="" method="get">
    <div class="mdl-textfield mdl-js-textfield searchBoxDiv ">
      <input class="mdl-textfield__input" type="text" id="searchBox" name="search" />
      <label class="mdl-textfield__label" for="searchBox"><?php echo $_GET['search']; ?></label>
    </div>
  </form>
</div>


  <?php

  while( $row = $result->fetch_array() ){

    ?>

    <div class="itcat-service mdl-card mdl-shadow--2dp mdl-cell mdl-cell--4-col mdl-grid mdl-grid--no-spacing">
        <div class="mdl-card__title mdl-card--expand mdl-color--orange-300">
          <h2 class="mdl-card__title-text">
            <?php echo $row['label']; ?>
          </h2>
        </div>
        <?php
        echo '<div class="mdl-card__supporting-text">';
        if(isset($row['serviceDescription'])){
            echo $row['serviceDescription'];
        }
        else {
          echo 'keine Beschreibung vorhanden';
        }
        echo '</div>';
        ?>
        <div class="mdl-card__actions mdl-card--border">
          <a class="mdl-button mdl-button--colored mdl-js-button mdl-js-ripple-effect" href="?c=service&service=<?php echo urlencode($row['s']); ?>">
            Öffnen
          </a>
        </div>
    </div>

    <?php

  }

  ?>



<div class="mdl-cell mdl-cell--12-col mdl-grid mdl-color--white mdl-shadow--2dp searchBoxDiv">

  <input name="" type="text" style="width:100%">

</div>

<div class="mdl-cell mdl-cell--8-col mdl-grid">

  <div class="service-profile-cat mdl-cell mdl-cell--12-col mdl-color--white mdl-shadow--2dp mdl-card">

    <div class="mdl-card__title">
      <h2 class="mdl-card__title-text mdl-badge" data-badge="5">Ergebnis</h2>
    </div>

    <div class="mdl-card__supporting-text">

    </div>
  </div>

</div>

<div class="mdl-cell mdl-cell--4-col mdl-grid">

  <div class="service-profile-cat mdl-cell mdl-cell--12-col mdl-color--white mdl-shadow--2dp mdl-card">

    <div class="mdl-card__title">
      <h2 class="mdl-card__title-text">Optionen</h2>
    </div>

    <div class="mdl-card__supporting-text">

    </div>
  </div>

</div>
