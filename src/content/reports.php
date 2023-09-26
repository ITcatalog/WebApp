<?php
$sparql = '
    SELECT ?provider ?subProvider1 ?subProvider2 ?service ?subService1 ?subService2 ?subService3 ?subService4 ?admin ?subAdmin1 ?subAdmin2 ?subAdmin3 ?subAdmin4
        {
          GRAPH ?g {
            itcat_app:report_provider	itcat_app:name ?provider ;
                                      itcat_app:subCategorie1 ?subProvider1 ;
                                      itcat_app:subCategorie2 ?subProvider2 .
    
    		itcat_app:report_service	itcat_app:name ?service ;
                                      itcat_app:subCategorie1 ?subService1 ;
                                      itcat_app:subCategorie2 ?subService2 ;
                                      itcat_app:subCategorie3 ?subService3 ;
                                      itcat_app:subCategorie4 ?subService4 .
    
    		itcat_app:report_administration	itcat_app:name ?admin ;
                                      itcat_app:subCategorie1 ?subAdmin1 ;
                                      itcat_app:subCategorie2 ?subAdmin2 ;
                                      itcat_app:subCategorie3 ?subAdmin3 ;
                                      itcat_app:subCategorie4 ?subAdmin4 .
    
            FILTER (langMatches(lang(?provider),"' . LANG . '"))
            FILTER (langMatches(lang(?subProvider1),"' . LANG . '"))
            FILTER (langMatches(lang(?subProvider2),"' . LANG . '"))
            FILTER (langMatches(lang(?service),"' . LANG . '"))
            FILTER (langMatches(lang(?subService1),"' . LANG . '"))
            FILTER (langMatches(lang(?subService2),"' . LANG . '"))
            FILTER (langMatches(lang(?subService3),"' . LANG . '"))
            FILTER (langMatches(lang(?subService4),"' . LANG . '"))
            FILTER (langMatches(lang(?admin),"' . LANG . '"))		
            FILTER (langMatches(lang(?subAdmin1),"' . LANG . '"))
            FILTER (langMatches(lang(?subAdmin2),"' . LANG . '"))
            FILTER (langMatches(lang(?subAdmin3),"' . LANG . '"))
            FILTER (langMatches(lang(?subAdmin4),"' . LANG . '"))
          }
        }';

$result = $db->query($sparql);
if (!$result) {
    print $db->errno() . ": " . $db->error() . "\n";
    exit;
}

$providerName = '';
$subProvider1 = '';
$subProvider2 = '';
$serviceName = '';
$subService1 = '';
$subService2 = '';
$subService3 = '';
$subService4 = '';
$adminName = '';
$subAdmin1 = '';
$subAdmin2 = '';
$subAdmin3 = '';
$subAdmin4 = '';

if ($result->num_rows() > 0) {
    while ($row = $result->fetch_array()) {
        $providerName = $row['provider'];
        $subProvider1 = $row['subProvider1'];
        $subProvider2 = $row['subProvider2'];
        $serviceName = $row['service'];
        $subService1 = $row['subService1'];
        $subService2 = $row['subService2'];
        $subService3 = $row['subService3'];
        $subService4 = $row['subService4'];
        $adminName = $row['admin'];
        $subAdmin1 = $row['subAdmin1'];
        $subAdmin2 = $row['subAdmin2'];
        $subAdmin3 = $row['subAdmin3'];
        $subAdmin4 = $row['subAdmin4'];
    }
}

?>

<div class="mdl-cell mdl-cell--4-col mdl-cell--8-col-tablet mdl-cell--4-col--phone">
    <div class="mdl-cell mdl-cell--12-col mdl-color--white mdl-shadow--2dp mdl-card navigation-card">

        <div class="mdl-card__title">
            <h2 class="mdl-card__title-text"><?php echo $providerName ?></h2>
        </div>
        <div class="mdl-card__navigation">
            <a href="?c=reports&action=provider&category=category"><?php echo $subProvider1 ?></a>
            <a href="?c=reports&action=externProvider&category=category"><?php echo $subProvider2 ?></a>
        </div>
    </div>
    <div class="mdl-cell mdl-cell--12-col mdl-color--white mdl-shadow--2dp mdl-card navigation-card">
        <div class="mdl-card__title">
            <h2 class="mdl-card__title-text"><?php echo $serviceName ?></h2>
        </div>
        <div class="mdl-card__navigation">
            <a href="?c=reports&action=in&value=http%3A%2F%2Fth-brandenburg.de%2Fns%2Fitcat%23Staff"><?php echo $subService1 ?></a>
            <a href="?c=reports&action=in&value=http%3A%2F%2Fth-brandenburg.de%2Fns%2Fitcat%23StaffADM"><?php echo $subService2 ?></a>
            <a href="?c=reports&action=in&value=http%3A%2F%2Fth-brandenburg.de%2Fns%2Fitcat%23Smartphone"><?php echo $subService3 ?></a>
            <a href="?c=reports&action=in&value=http%3A%2F%2Fth-brandenburg.de%2Fns%2Fitcat%23Operation"><?php echo $subService4 ?></a>
        </div>
    </div>
    <div class="mdl-cell mdl-cell--12-col mdl-color--white mdl-shadow--2dp mdl-card navigation-card">
        <div class="mdl-card__title">
            <h2 class="mdl-card__title-text"><?php echo $adminName ?></h2>
        </div>
        <div class="mdl-card__navigation">
            <a href="?c=reports&action=find_withoutDocs"><?php echo $subAdmin1 ?></a>
            <a href="?c=reports&action=find_lessThan18"><?php echo $subAdmin2 ?></a>
            <a href="?c=reports&action=find_withoutType"><?php echo $subAdmin3 ?></a>
            <a href="?c=reports&action=statistic&category=category"><?php echo $subAdmin4 ?></a>
        </div>
    </div>

</div>

<div class="mdl-cell mdl-cell--8-col mdl-cell--8-col-tablet mdl-cell--4-col--phone">

<?php

    $provider = array(array("?Anbieter", "?Beschreibung", "?Dienste"), array("?Provider", "?Description", "?Services"));
    $general = array(array("?Name", "?Beschreibung"), array("?Name", "?Description"));
    $statistic = array(array("?Kategorie", "?Beschreibung", "?Diensteanzahl"), array("?Category", "?Description", "?Number_of_services"));

    $pos = 0;
    if (LANG == 'en') {
        $pos = 1;
    }

    $urlParameters = '?input=';

    if (isset($_GET['category'])) {
        switch ($_GET['category']) {
            case 'category':
                $urlParameters = '?c=category&cat=';
                break;
        }
    }

    if (isset($_GET['action'])) {

        /* Sparql Selector */

        switch ($_GET['action']) {

            case 'in':

                $searchTermSparql = urldecode($_GET['value']);

                $sparql = '
                  SELECT DISTINCT (?service as ?uri) (?prefLabel as '.$general[$pos][0].') (?abstract as '.$general[$pos][1].')
                  WHERE {
                    ?service ?x <' . $searchTermSparql . '>.
                    ?service skos:prefLabel ?prefLabelLang;
                    dcterms:abstract      ?abstractLang;
                    FILTER (langMatches(lang(?prefLabelLang),"' . LANG . '"))
                    FILTER (langMatches(lang(?abstractLang),"' . LANG . '"))
                    BIND (str(?prefLabelLang) AS ?prefLabel)
                    BIND (str(?abstractLang) AS ?abstract)
                  }
                  ';
                break;

            case 'provider':
                $sparql = '
                  SELECT DISTINCT (?provider AS ?uri) (?prefLabel AS '.$provider[$pos][0].') (?title AS '.$provider[$pos][1].') (COUNT(?service) AS '.$provider[$pos][2].')
                  WHERE {
                    ?service schema:provider ?provider.
                    ?provider skos:prefLabel ?prefLabelLang;
                    dcterms:title ?titleLang.
                    FILTER (langMatches(lang(?prefLabelLang),"' . LANG . '"))
                    FILTER (langMatches(lang(?titleLang),"' . LANG . '"))
                    BIND (str(?prefLabelLang) AS ?prefLabel)
                    BIND (str(?titleLang) AS ?title)
                  }
                  GROUP BY ?provider ?prefLabel ?title
                  ORDER BY DESC(?prefLabel)
                  ';

                break;

            case 'externProvider':
                $sparql = '
                    SELECT DISTINCT (?provider AS ?uri) (?prefLabel AS '.$provider[$pos][0].') (?title AS '.$provider[$pos][1].') (COUNT(?service) AS '.$provider[$pos][2].')
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
                      FILTER (langMatches(lang(?prefLabelLang),"' . LANG . '"))
                        FILTER (langMatches(lang(?titleLang),"' . LANG . '"))
                        BIND (str(?prefLabelLang) AS ?prefLabel)
                        BIND (str(?titleLang) AS ?title)
                    }
                    GROUP BY ?provider ?prefLabel ?title
                    ORDER BY DESC(?prefLabel)
                    ';

                            break;

                        case 'find_withoutDocs':
                            $sparql = '
                  SELECT (?service as ?uri)(?prefLabel as '.$general[$pos][0].') (?abstract as '.$general[$pos][1].')
                  WHERE {
                    ?service a schema:Service;
                    skos:prefLabel ?prefLabelLang;
                    dcterms:abstract ?abstractLang.

                    FILTER (langMatches(lang(?prefLabelLang),"' . LANG . '"))
                    FILTER (langMatches(lang(?abstractLang),"' . LANG . '"))
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
                    SELECT (?service as ?uri)(?prefLabel as '.$general[$pos][0].') (?abstract as '.$general[$pos][1].')
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

                    FILTER (langMatches(lang(?prefLabelLang),"' . LANG . '"))
                    FILTER (langMatches(lang(?abstractLang),"' . LANG . '"))
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
                    SELECT (?category AS ?uri) (?prefLabel AS '.$statistic[$pos][0].') (?definition AS '.$statistic[$pos][1].') (COUNT(?service) AS '.$statistic[$pos][2].')
                    WHERE{
                    ?service itcat:inCategory ?category.
                              ?category a ?type;
                              skos:prefLabel ?prefLabelLang;
                                skos:definition ?definitionLang.
                    FILTER (langMatches(lang(?prefLabelLang),"' . LANG . '"))
                                FILTER (langMatches(lang(?definitionLang),"' . LANG . '"))
                                BIND (str(?prefLabelLang) AS ?prefLabel)
                                BIND (str(?definitionLang) AS ?definition)
                    }
                    GROUP BY ?category ?prefLabel ?definition ?type
                    ORDER BY ?type
                    ';
                break;

        }
    } else {

        $sparql = '
            SELECT (?service as ?uri)(?prefLabel as '.$general[$pos][0].') (?abstract as '.$general[$pos][1].')
            WHERE {
                ?service a schema:Service;
                skos:prefLabel ?prefLabelLang;
                dcterms:abstract ?abstractLang.

                FILTER (langMatches(lang(?prefLabelLang),"' . LANG . '"))
                FILTER (langMatches(lang(?abstractLang),"' . LANG . '"))
                BIND (str(?prefLabelLang) AS ?prefLabel)
                BIND (str(?abstractLang) AS ?abstract)
            }
        ';
    }


    $result = $db->query($sparql);
    if (!$result) {
        print $db->errno() . ": " . $db->error() . "\n";
        exit;
    }

    $fields = $result->field_array($result);

    print '<table class="mdl-data-table mdl-js-data-table mdl-text-table">';
    print "<thead>";
    print "<tr>";
    foreach ($fields as $field) {
        if ($field != 'uri') {
            print "<th class='mdl-data-table__cell--non-numeric mdl-color--grey-100'>$field</th>";
        }
    }
    print "</tr>";
    print "</thead>";
    print "<tbody>";
    while ($row = $result->fetch_array()) {
        echo '<tr onclick="document.location = \' ' . $urlParameters . urlencode($row['uri']) . '\';" style="cursor:pointer;">';
        foreach ($fields as $field) {
            if ($field != 'uri') {
                if (filter_var($row[$field], FILTER_VALIDATE_URL)) {
                    //$shortUri = $db->getNs($row[$field]);
                    $shortUri = '';
                    if ($shortUri == '') {

                        $value = '<a href="' . $row[$field] . '" target="_blank">' . $row[$field] . '</a>';
                    } else {
                        $value = $shortUri;
                    }
                } else {
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
