<?php
$searchTerm = $_GET['search'];
$searchTermInput = $searchTerm;


include ('./template/categoryCard.php');

if (strpos($searchTerm,'in:') !== false) {
	$ex = explode(':', $searchTerm, 2);
	$identifier = $ex[0];
	$searchTerm = urldecode($ex[1]);
	if (strpos($searchTerm,'#') !== false) {
		$ex = explode('#', $searchTerm, 2);
		$searchTerm = $ex[1];
	}

	$searchTermInput = $identifier . ':' . $searchTerm;

	$searchTermSparql = 'itcat:' . $searchTerm;

  $sparql = '
	SELECT DISTINCT ?service ?prefLabel ?abstract
	WHERE {
	  ?service ?x '.$searchTermSparql.'.
	  ?service skos:prefLabel ?prefLabelLang;
	  dcterms:abstract      ?abstractLang;
	  FILTER (langMatches(lang(?prefLabelLang),"'.LANG.'"))
		FILTER (langMatches(lang(?abstractLang),"'.LANG.'"))
		BIND (str(?prefLabelLang) AS ?prefLabel)
		BIND (str(?abstractLang) AS ?abstract)
	}

	';
}
else {
	$sparql = '
	SELECT DISTINCT ?service ?prefLabel ?abstract
	WHERE {
		?service a schema:Service.
	  	?service ?prop ?valueLang
	  	{
	      ?prop a owl:AnnotationProperty.
	    }
	    UNION{
	      ?prop a owl:DatatypeProperty.
	    }
	    FILTER (
	      (regex(lcase(str(?valueLang)), lcase("'.$searchTerm.'")))
	  	)
	  ?service skos:prefLabel ?prefLabelLang;
	  dcterms:abstract      ?abstractLang;
	  FILTER (langMatches(lang(?prefLabelLang),"de"))
		FILTER (langMatches(lang(?abstractLang),"de"))
		BIND (str(?prefLabelLang) AS ?prefLabel)
		BIND (str(?abstractLang) AS ?abstract)
	  OPTIONAL{
	    ?service itcat:inCategory ?category.
	    GRAPH ?g {
		     ?category itcat_app:hasBgColor ?bgColor.
	    }
	  }
	}
	';
}



$result = $db->query( $sparql );
if( !$result ) { print $db->errno() . ": " . $db->error(). "\n"; exit; }

?>
<div class="mdh-expandable-search mdl-cell mdl-cell--12-col mdl-color--white mdl-shadow--6dp">
  <i class="material-icons mdl-color-text--black">search</i>
  <form name="" action="" method="get">
    <input type="text" placeholder="<?php echo $searchTermInput ?>" size="1" name="search">
  </form>
</div>

  <?php
	$colorStrength = 300;
  while( $row = $result->fetch_array() ){
    if(!isset($row['bgColor'])){
      $row['bgColor'] = 'grey';
			$colorStrength = '';
    }
    showCardTemplate ($row['service'], $row['prefLabel'], $row['abstract'], '', $row['bgColor'], '?c=service&service=', 4, $colorStrength);

  }

  ?>
