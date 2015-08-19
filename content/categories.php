


<?php
$sparql = "
SELECT *
FROM NAMED <'.$dataGraphs['ApplicationGraph'].'>
{
	?cat a itcat:ServiceKategorie;
	skos:prefLabel ?catLabel.

	GRAPH ?g {
    	?cat itcat_app:hasBGColor ?bgColor.
  	}.
}
";

$result = $db->query( $sparql );
if( !$result ) { print $db->errno() . ": " . $db->error(). "\n"; exit; }



while( $row = $result->fetch_array() ){

  $sparql = '
  SELECT (COUNT (DISTINCT ?service) AS ?numberOfServices)
  WHERE {
    <'.$row['cat'].'> a itcat:ServiceKategorie;
    itcat:hasITService ?service.
  }
  ';
  $result2 = $db->query($sparql);
  if( !$result2 ) { print $db->errno() . ": " . $db->error(). "\n"; exit; }
  $row2 = $result2->fetch_array();
  $numberOfServices = $row2['numberOfServices'];
?>



<div class="itcat-category mdl-card mdl-shadow--2dp mdl-cell mdl-cell--4-col mdl-grid mdl-grid--no-spacing">
    <div class="mdl-card__title mdl-card--expand mdl-color--<?php echo $row['bgColor']; ?>-300">
      <h2 class="mdl-card__title-text mdl-badge badge-btn" data-badge="<?php echo $numberOfServices; ?>">
        <?php echo $row['catLabel']; ?>
      </h2>
    </div>
    <?php
    echo '<div class="mdl-card__supporting-text">';
    if(isset($row['catDescription'])){
        echo $row['catDescription'];
    }
    else {
      echo 'keine Beschreibung vorhanden';
    }
    echo '</div>';
    ?>
    <div class="mdl-card__actions mdl-card--border">
      <a class="mdl-button mdl-button--colored mdl-js-button mdl-js-ripple-effect" href="?c=category&cat=<?php echo urlencode($row['cat']);?>">
        Öffnen
      </a>
    </div>
    <!--
    <div class="mdl-card__menu">
      <button class="mdl-button mdl-button--icon mdl-js-button mdl-js-ripple-effect">
        <i class="material-icons">share</i>
      </button>
    </div>
    -->
</div>



<?php
}

?>
