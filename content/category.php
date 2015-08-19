<?php

if(isset($_GET['cat'])){

	$category = urldecode($_GET['cat']);
	$sparql = '
	SELECT *
  FROM NAMED <'.$dataGraphs['ApplicationGraph'].'>
  WHERE {
    <'.$category.'> a itcat:ServiceKategorie;
		itcat:hasITService ?service.
		?service skos:prefLabel ?label;
    dcterms:description ?serviceDescription.
  	GRAPH ?g {
    	<'.$category.'> itcat_app:hasBGColor ?bgColor.
    }.
  }
	';

	$result = $db->query( $sparql );
	if( !$result ) { print $db->errno() . ": " . $db->error(). "\n"; exit; }


	while( $row = $result->fetch_array() ){
	?>
  <div class="itcat-service mdl-card mdl-shadow--2dp mdl-cell mdl-cell--4-col mdl-grid mdl-grid--no-spacing">
      <div class="mdl-card__title mdl-card--expand mdl-color--<?php echo $row['bgColor']; ?>-300">
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
        <a class="mdl-button mdl-button--colored mdl-js-button mdl-js-ripple-effect" href="?c=service&service=<?php echo urlencode($row['service']); ?>">
          Öffnen
        </a>
      </div>
  </div>
<?php
	}
}
