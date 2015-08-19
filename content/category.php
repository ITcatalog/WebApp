<?php

if(isset($_GET['cat'])){

	$category = urldecode($_GET['cat']);
	$sparql = '

	SELECT ?service ?prefLabel ?abstract ?bgColor
	{
		?service itcat:inCategory <'.$category.'>;
	  skos:prefLabel ?prefLabelLang;
		dcterms:abstract ?abstractLang.

		GRAPH ?g {
			<'.$category.'> itcat_app:hasBgColor ?bgColor
		}
	  	FILTER (langMatches(lang(?prefLabelLang),"'.LANG.'"))
		FILTER (langMatches(lang(?abstractLang),"'.LANG.'"))
		BIND (str(?prefLabelLang) AS ?prefLabel)
		BIND (str(?abstractLang) AS ?abstract)
	}
	';

	$result = $db->query( $sparql );
	if( !$result ) { print $db->errno() . ": " . $db->error(). "\n"; exit; }


	while( $row = $result->fetch_array() ){
	?>
  <div class="itcat-service mdl-card mdl-shadow--2dp mdl-cell mdl-cell--4-col mdl-cell--12-col-phone mdl-grid mdl-grid--no-spacing">
      <div class="mdl-card__title mdl-card--expand mdl-color--<?php echo $row['bgColor']; ?>-300">
        <h2 class="mdl-card__title-text">
          <?php echo $row['prefLabel']; ?>
        </h2>
      </div>
      <?php
      echo '<div class="mdl-card__supporting-text">';
      if(isset($row['abstract'])){
          echo $row['abstract'];
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
