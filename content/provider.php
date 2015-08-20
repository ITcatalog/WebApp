<?php

include ('./template/categoryCard.php');

$sparql = '
SELECT ?provider ?prefLabel ?title ?bgColor (COUNT(?service) AS ?numberOfServices)
{
	?service schema:provider ?provider.
 	?provider skos:prefLabel ?prefLabelLang;
	dcterms:title ?titleLang.
	FILTER (langMatches(lang(?prefLabelLang),"'.LANG.'"))
	FILTER (langMatches(lang(?titleLang),"'.LANG.'"))
	BIND (str(?prefLabelLang) AS ?prefLabel)
	BIND (str(?titleLang) AS ?title)
  	OPTIONAL{
      GRAPH ?g {
          ?provider itcat_app:hasBgColor ?bgColor.
      }.
	}
}
GROUP BY ?provider ?prefLabel ?title ?bgColor
';

$result = $db->query( $sparql );
if( !$result ) { print $db->errno() . ": " . $db->error(). "\n"; exit; }

while( $row = $result->fetch_array() ){
  if(!isset($row['bgColor'])){
    $row['bgColor'] = 'gray';
  }
	showCardTemplate ($row['provider'], $row['prefLabel'], $row['title'], $row['numberOfServices'], $row['bgColor'], '?c=category&cat=', 4);
}

?>
