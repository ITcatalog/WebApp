<?php

include ('./template/categoryCard.php');

$sparql = '
SELECT ?catalog ?prefLabel ?definition ?bgColor (COUNT(?service) AS ?numberOfServices)
{
	?catalog a itcat:CatalogCategory;
  skos:prefLabel ?prefLabelLang;
	skos:definition ?definitionLang.
	OPTIONAL{
    	?service itcat:inCategory ?catalog
	}
  GRAPH ?g {
    	?catalog itcat_app:hasBgColor ?bgColor
	}
	FILTER (langMatches(lang(?prefLabelLang),"'.LANG.'"))
	FILTER (langMatches(lang(?definitionLang),"'.LANG.'"))
	BIND (str(?prefLabelLang) AS ?prefLabel)
	BIND (str(?definitionLang) AS ?definition)
}
GROUP BY ?catalog ?prefLabel ?definition ?bgColor

';

$result = $db->query( $sparql );
if( !$result ) { print $db->errno() . ": " . $db->error(). "\n"; exit; }


while( $row = $result->fetch_array() ){
	showCardTemplate ($row['catalog'], $row['prefLabel'], $row['definition'], $row['numberOfServices'], $row['bgColor'], '?c=category&cat=', 6);
}




?>
