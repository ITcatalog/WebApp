<?php

include("template/categoryCard.php");

if (isset($_GET['cat'])) {

    $category = urldecode($_GET['cat']);
    $sparql = '
        SELECT ?service ?prefLabel ?abstract ?bgColor ?category_card_open ?category_card_service
        {
            ?service (itcat:inCategory | schema:provider) <' . $category . '>;
          skos:prefLabel ?prefLabelLang;
            dcterms:abstract ?abstractLang.
            OPTIONAL{
                GRAPH ?g {
                    <' . $category . '> itcat_app:hasBgColor ?bgColor .
                    itcat_app:category_card_open itcat_app:name ?category_card_open .
                    itcat_app:category_card_service itcat_app:name ?category_card_service .
                    FILTER (langMatches(lang(?category_card_open),"' . LANG . '"))
                    FILTER (langMatches(lang(?category_card_service),"' . LANG . '"))
                }
            }
          FILTER (langMatches(lang(?prefLabelLang),"' . LANG . '"))
            FILTER (langMatches(lang(?abstractLang),"' . LANG . '"))
            BIND (str(?prefLabelLang) AS ?prefLabel)
            BIND (str(?abstractLang) AS ?abstract)
        }
        ORDER BY ?prefLabel
        ';

    $result = $db->query($sparql);
    if (!$result) {
        print $db->errno() . ": " . $db->error() . "\n";
        exit;
    }

    $colorValue = 300;
    while ($row = $result->fetch_array()) {

        if (!isset($row['bgColor'])) {
            $row['bgColor'] = 'grey';
            $colorValue = '';
        }
        showCardTemplate($row['service'], $row['prefLabel'], $row['abstract'], '', $row['bgColor'], '?c=service&service=', $row['category_card_open'], $row['category_card_service'],4, $colorValue);
    }
}
