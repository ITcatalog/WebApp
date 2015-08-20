<?php

include ('./template/categoryCard.php');

if(isset($_GET['cat'])){

	$category = urldecode($_GET['cat']);
	$sparql = '

	SELECT ?service ?prefLabel ?abstract ?bgColor
	{
		?service (itcat:inCategory | schema:provider) <'.$category.'>;
	  skos:prefLabel ?prefLabelLang;
		dcterms:abstract ?abstractLang.
		OPTIONAL{
			GRAPH ?g {
				<'.$category.'> itcat_app:hasBgColor ?bgColor
			}
		}
	  FILTER (langMatches(lang(?prefLabelLang),"'.LANG.'"))
		FILTER (langMatches(lang(?abstractLang),"'.LANG.'"))
		BIND (str(?prefLabelLang) AS ?prefLabel)
		BIND (str(?abstractLang) AS ?abstract)
	}
	ORDER BY ?prefLabel
	';

	$result = $db->query( $sparql );
	if( !$result ) { print $db->errno() . ": " . $db->error(). "\n"; exit; }


	while( $row = $result->fetch_array() ){
		if(!isset($row['bgColor'])){
	    $row['bgColor'] = 'grey';
	  }
		showCardTemplate ($row['service'], $row['prefLabel'], $row['abstract'], '', $row['bgColor'], '?c=service&service=', 4);
	}
}
