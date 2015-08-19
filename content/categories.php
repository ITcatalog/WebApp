<?php
$sparql = '
SELECT ?subjectCategory ?prefLabel ?definition ?bgColor (COUNT(?service) AS ?numberOfServices)
{
	?subjectCategory a itcat:SubjectCategory;
  skos:prefLabel ?prefLabelLang;
	skos:definition ?definitionLang.
	OPTIONAL{
    	?service itcat:inCategory ?subjectCategory
	}

	GRAPH ?g {
    	?subjectCategory itcat_app:hasBgColor ?bgColor
	}

	FILTER (langMatches(lang(?prefLabelLang),"'.LANG.'"))
	FILTER (langMatches(lang(?definitionLang),"'.LANG.'"))
	BIND (str(?prefLabelLang) AS ?prefLabel)
	BIND (str(?definitionLang) AS ?definition)
}
GROUP BY ?subjectCategory ?prefLabel ?definition ?bgColor

';

$result = $db->query( $sparql );
if( !$result ) { print $db->errno() . ": " . $db->error(). "\n"; exit; }

while( $row = $result->fetch_array() ){
?>

<div class="itcat-category mdl-card mdl-shadow--2dp mdl-cell mdl-cell--4-col mdl-cell--12-col-phone mdl-grid mdl-grid--no-spacing">
    <div class="mdl-card__title mdl-card--expand mdl-color--<?php echo $row['bgColor']; ?>-300">
      <h2 class="mdl-card__title-text mdl-badge badge-btn" data-badge="<?php echo $row['numberOfServices']; ?>">
        <?php echo $row['prefLabel']; ?>
      </h2>
    </div>
    <?php
    echo '<div class="mdl-card__supporting-text">';
    if(isset($row['definition'])){
        echo $row['definition'];
    }
    else {
      echo 'keine Beschreibung vorhanden';
    }
    echo '</div>';
    ?>
    <div class="mdl-card__actions mdl-card--border">
      <a class="mdl-button mdl-button--colored mdl-js-button mdl-js-ripple-effect" href="?c=category&cat=<?php echo urlencode($row['subjectCategory']);?>">
        Öffnen
      </a>
    </div>
</div>



<?php
}

?>
