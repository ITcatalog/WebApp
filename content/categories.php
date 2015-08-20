
<div class="categories mdl-cell--12-col mdl-grid">
<?php

include ('./template/categoryCard.php');

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
ORDER BY ?prefLabel
';

$result = $db->query( $sparql );
if( !$result ) { print $db->errno() . ": " . $db->error(). "\n"; exit; }

while( $row = $result->fetch_array() ){
	showCardTemplate ($row['subjectCategory'], $row['prefLabel'], $row['definition'], $row['numberOfServices'], $row['bgColor'], '?c=category&cat=', 4);
}

?>
</div>
