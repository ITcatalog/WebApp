<?php
$searchTerm = $_GET['search'];

include ('./template/categoryCard.php');

$sparql = '

SELECT DISTINCT ?service ?prefLabel ?abstract
WHERE {
  ?service skos:prefLabel ?prefLabelLang.
  ?service dcterms:abstract ?abstractLang.
  FILTER (
    (regex(lcase(str(?prefLabelLang)), lcase("'.$searchTerm.'"))) ||
    (regex(lcase(str(?abstractLang)), lcase("'.$searchTerm.'")))
  ).
	FILTER (langMatches(lang(?prefLabelLang),"'.LANG.'"))
	FILTER (langMatches(lang(?abstractLang),"'.LANG.'"))
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

$result = $db->query( $sparql );
if( !$result ) { print $db->errno() . ": " . $db->error(). "\n"; exit; }

?>
<div class="mdh-expandable-search mdl-cell mdl-cell--12-col mdl-color--white mdl-shadow--6dp">
  <i class="material-icons mdl-color-text--black">search</i>
  <form name="" action="" method="get">
    <input type="text" placeholder="<?php echo $_GET['search']; ?>" size="1" name="search">
  </form>
</div>

  <?php

  while( $row = $result->fetch_array() ){
    if(!isset($row['bgColor'])){
      $row['bgColor'] = 'grey';
    }
    showCardTemplate ($row['service'], $row['prefLabel'], $row['abstract'], '', $row['bgColor'], '?c=service&service=', 4);

  }

  ?>
