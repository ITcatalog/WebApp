
<div class="mdl-cell mdl-cell--4-col mdl-cell--8-col-tablet mdl-cell--4-col--phone">
  <div class="mdl-cell mdl-cell--12-col mdl-color--white mdl-shadow--2dp mdl-card navigation-card">

    <div class="mdl-card__title">
      <h2 class="mdl-card__title-text">Provider</h2>
    </div>
    <div class="mdl-card__navigation">
        <a href="?c=reports&action=provider&category=category">Alle Provider</a>
        <a href="?c=reports&action=externProvider&category=category">Externe Provider</a>
    </div>
  </div>
  <div class="mdl-cell mdl-cell--12-col mdl-color--white mdl-shadow--2dp mdl-card navigation-card">
    <div class="mdl-card__title">
      <h2 class="mdl-card__title-text">Dienste</h2>
    </div>
    <div class="mdl-card__navigation">
      <a href="?c=reports&action=in&value=http%3A%2F%2Fth-brandenburg.de%2Fns%2Fitcat%23Staff">Dienste für Mitarbeiter</a>
      <a href="?c=reports&action=in&value=http%3A%2F%2Fth-brandenburg.de%2Fns%2Fitcat%23StaffADM">Dienste für Verwaltung/Zentren </a>
      	<a href="?c=reports&action=in&value=http%3A%2F%2Fth-brandenburg.de%2Fns%2Fitcat%23Smartphone">Dienste für Smartphones</a>
        <a href="?c=reports&action=in&value=http%3A%2F%2Fth-brandenburg.de%2Fns%2Fitcat%23Operation">Dienste in Betrieb</a>
    </div>
  </div>
  <div class="mdl-cell mdl-cell--12-col mdl-color--white mdl-shadow--2dp mdl-card navigation-card">
    <div class="mdl-card__title">
      <h2 class="mdl-card__title-text">Administration</h2>
    </div>
    <div class="mdl-card__navigation">
        <a href="?c=reports&action=find_withoutDocs">Dienste ohne Dokumente</a>
        <a href="?c=reports&action=find_lessThan18">Dienste mit weniger als 18 Eigenschaften</a>
        <a href="?c=reports&action=find_withoutType">Elemente ohne Typ</a>
        <a href="?c=reports&action=statistic&category=category">Statistik</a>
    </div>
  </div>

</div>

<div class="mdl-cell mdl-cell--8-col mdl-cell--8-col-tablet mdl-cell--4-col--phone">


<?php

$urlParameters = '?input=';

if(isset($_GET['category'])){
  switch ($_GET['category']){
    case 'category':
      $urlParameters = '?c=category&cat=';
      break;
  }
}

if(isset($_GET['action'])){

/* Sparql Selector */

  switch ($_GET['action']){

    case 'in':

      $searchTermSparql = urldecode($_GET['value']);

      $sparql = '
      SELECT DISTINCT (?service as ?uri) (?prefLabel as ?Name) (?abstract as ?Beschreibung)
      WHERE {
        ?service ?x <'.$searchTermSparql.'>.
        ?service skos:prefLabel ?prefLabelLang;
        dcterms:abstract      ?abstractLang;
        FILTER (langMatches(lang(?prefLabelLang),"'.LANG.'"))
        FILTER (langMatches(lang(?abstractLang),"'.LANG.'"))
        BIND (str(?prefLabelLang) AS ?prefLabel)
        BIND (str(?abstractLang) AS ?abstract)
      }
      ';
      break;

    case 'provider':
      $sparql = '
      SELECT DISTINCT (?provider AS ?uri) (?prefLabel AS ?Provider) (?title AS ?Beschreibung) (COUNT(?service) AS ?Dienste)
      WHERE {
      	?service schema:provider ?provider.
       	?provider skos:prefLabel ?prefLabelLang;
      	dcterms:title ?titleLang.
      	FILTER (langMatches(lang(?prefLabelLang),"'.LANG.'"))
      	FILTER (langMatches(lang(?titleLang),"'.LANG.'"))
      	BIND (str(?prefLabelLang) AS ?prefLabel)
      	BIND (str(?titleLang) AS ?title)
      }
      GROUP BY ?provider ?prefLabel ?title
      ORDER BY DESC(?prefLabel)
      ';

      break;

      case 'externProvider':
        $sparql = '
        SELECT DISTINCT (?provider AS ?uri) (?prefLabel AS ?Provider) (?title AS ?Beschreibung) (COUNT(?service) AS ?Dienste)
        WHERE{
          {
            SELECT *
            WHERE{
              ?service schema:provider ?provider
            }
          }
          MINUS {
            {
              SELECT *
              WHERE {}
              VALUES (?provider ) {
              	(itcat:TLSO)
              	(itcat:DCS)
              	(itcat:LSCS)
              	(itcat:StaffADM)
              	(itcat:DataCenter)
                (itcat:RegOffice)
        	    }
            }
          }
          ?provider skos:prefLabel ?prefLabelLang;
        	dcterms:title ?titleLang.
          FILTER (langMatches(lang(?prefLabelLang),"'.LANG.'"))
         	FILTER (langMatches(lang(?titleLang),"'.LANG.'"))
         	BIND (str(?prefLabelLang) AS ?prefLabel)
         	BIND (str(?titleLang) AS ?title)
        }
        GROUP BY ?provider ?prefLabel ?title
        ORDER BY DESC(?prefLabel)
        ';

        break;

    case 'find_withoutDocs':
      $sparql = '
      SELECT (?service as ?uri)(?prefLabel as ?Name) (?abstract as ?Beschreibung)
      WHERE {
        ?service a schema:Service;
        skos:prefLabel ?prefLabelLang;
        dcterms:abstract ?abstractLang.

        FILTER (langMatches(lang(?prefLabelLang),"'.LANG.'"))
        FILTER (langMatches(lang(?abstractLang),"'.LANG.'"))
        BIND (str(?prefLabelLang) AS ?prefLabel)
        BIND (str(?abstractLang) AS ?abstract)
        MINUS{
          ?service foaf:page ?doc.
        }
      }
      ';
      break;

    case 'find_withoutType':
      $sparql = '
        SELECT ?uri ?prop ?class
        WHERE {
          ?uri ?prop ?class .
          ?prop rdf:type owl:ObjectProperty.
          MINUS { ?class a ?type . }

        }
      ';
      break;


    case 'find_lessThan18':
      $sparql = '
      SELECT (?service as ?uri)(?prefLabel as ?Name) (?abstract as ?Beschreibung)
      WHERE{
        {
        SELECT ?service (COUNT(?props) AS ?numProps)
        WHERE{
          {
          SELECT DISTINCT ?props ?service
          WHERE{
            ?service a schema:Service.
            ?service ?props ?value.
          }
          }
        }
        GROUP BY ?service
        }

        ?service skos:prefLabel ?prefLabelLang;
        dcterms:abstract ?abstractLang.

        FILTER (langMatches(lang(?prefLabelLang),"'.LANG.'"))
        FILTER (langMatches(lang(?abstractLang),"'.LANG.'"))
        BIND (str(?prefLabelLang) AS ?prefLabel)
        BIND (str(?abstractLang) AS ?abstract)

      }
      HAVING (?numProps < 18)

      ';
      break;

      case '':
        $sparql = '
        SELECT ?category (COUNT(?service) AS ?numService)
        WHERE{
          ?service itcat:inCategory ?category.
          ?category a ?type.
        }
        GROUP BY ?category
        ORDER BY ?type
        ';
        break;

        case 'statistic':
          $sparql = '
          SELECT (?category AS ?uri) (?prefLabel AS ?Kategorie) (?definition AS ?Beschreibung) (COUNT(?service) AS ?Diensteanzahl)
          WHERE{
            ?service itcat:inCategory ?category.
                      ?category a ?type;
                      skos:prefLabel ?prefLabelLang;
                    	skos:definition ?definitionLang.
            FILTER (langMatches(lang(?prefLabelLang),"'.LANG.'"))
                    	FILTER (langMatches(lang(?definitionLang),"'.LANG.'"))
                    	BIND (str(?prefLabelLang) AS ?prefLabel)
                    	BIND (str(?definitionLang) AS ?definition)
          }
          GROUP BY ?category ?prefLabel ?definition ?type
          ORDER BY ?type
          ';
        break;

  }
}
else {

  $sparql =$sparql = '
  SELECT (?service as ?uri)(?prefLabel as ?Name) (?abstract as ?Beschreibung)
  WHERE {
    ?service a schema:Service;
    skos:prefLabel ?prefLabelLang;
    dcterms:abstract ?abstractLang.

    FILTER (langMatches(lang(?prefLabelLang),"'.LANG.'"))
    FILTER (langMatches(lang(?abstractLang),"'.LANG.'"))
    BIND (str(?prefLabelLang) AS ?prefLabel)
    BIND (str(?abstractLang) AS ?abstract)

  }
  ';
}




$result = $db->query( $sparql );
if( !$result ) { print $db->errno() . ": " . $db->error(). "\n"; exit; }

$fields = $result->field_array( $result );

print '<table class="mdl-data-table mdl-js-data-table mdl-text-table">';
  print "<thead>";
    print "<tr>";
      foreach( $fields as $field ){
        if($field != 'uri'){
          print "<th class='mdl-data-table__cell--non-numeric mdl-color--grey-100'>$field</th>";
        }
      }
    print "</tr>";
  print "</thead>";
  print "<tbody>";
    while( $row = $result->fetch_array() )
    {
        echo '<tr onclick="document.location = \' ' . $urlParameters . urlencode($row['uri']) . '\';" style="cursor:pointer;">';
      	foreach( $fields as $field ){
          if($field != 'uri'){
              if (filter_var($row[$field], FILTER_VALIDATE_URL)) {
                //$shortUri = $db->getNs($row[$field]);
                $shortUri = '';
                if($shortUri == '' ){

                  $value = '<a href="'.$row[$field].'" target="_blank">'.$row[$field].'</a>';
                }
                else{
                  $value = $shortUri;
                }
              }

            else{
              $value = $row[$field];
            }
            print "<td class='mdl-data-table__cell--non-numeric'>$value</td>";
          }
      	}
      	print "</tr>";
    }
  print "</tbody>";
print "</table>";
#print "<p >Anzahl: ".$result->num_rows( $result )."</p>";

?>
</div>
