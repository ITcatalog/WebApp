<div class="catalog mdl-cell--12-col mdl-grid">
    <?php

    include("./template/categoryCard.php");

    $sparql = '
        SELECT ?catalog ?prefLabel ?definition ?bgColor ?category_card_open ?category_card_service (COUNT(?service) AS ?numberOfServices)
        {
            ?catalog a itcat:CatalogCategory;
          skos:prefLabel ?prefLabelLang;
            skos:definition ?definitionLang.
            OPTIONAL{
                ?service itcat:inCategory ?catalog
            }
          GRAPH ?g {
                ?catalog itcat_app:hasBgColor ?bgColor .
                itcat_app:category_card_open itcat_app:name ?category_card_open .
                itcat_app:category_card_service itcat_app:name ?category_card_service .
                FILTER (langMatches(lang(?category_card_open),"' . LANG . '"))
                FILTER (langMatches(lang(?category_card_service),"' . LANG . '"))
            }
            FILTER (langMatches(lang(?prefLabelLang),"' . LANG . '"))
            FILTER (langMatches(lang(?definitionLang),"' . LANG . '"))
            BIND (str(?prefLabelLang) AS ?prefLabel)
            BIND (str(?definitionLang) AS ?definition)
        }
        GROUP BY ?catalog ?prefLabel ?definition ?bgColor ?category_card_open ?category_card_service
        ORDER BY ?prefLabel
        ';

    $result = $db->query($sparql);
    if (!$result) {
        print $db->errno() . ": " . $db->error() . "\n";
        exit;
    }


    while ($row = $result->fetch_array()) {
        showCardTemplate($row['catalog'], $row['prefLabel'], $row['definition'], $row['numberOfServices'], $row['bgColor'], '?c=category&cat=', $row['category_card_open'], $row['category_card_service'], 6, 600);
    }
    ?>
</div>
