<div class="provider mdl-cell--12-col mdl-grid">
    <?php

    include('./template/categoryCard.php');

    $sparql = '
        SELECT DISTINCT ?provider ?prefLabel ?title ?bgColor ?category_card_open ?category_card_service (COUNT(?service) AS ?numberOfServices)
        {
            ?service schema:provider ?provider.
            ?provider skos:prefLabel ?prefLabelLang;
            dcterms:title ?titleLang.
            FILTER (langMatches(lang(?prefLabelLang),"' . LANG . '"))
            FILTER (langMatches(lang(?titleLang),"' . LANG . '"))
            BIND (str(?prefLabelLang) AS ?prefLabel)
            BIND (str(?titleLang) AS ?title)
            OPTIONAL{
              GRAPH ?g {
                  ?provider itcat_app:hasBgColor ?bgColor.
              }.
            }
            GRAPH ?g {
                itcat_app:category_card_open itcat_app:name ?category_card_open .
                itcat_app:category_card_service itcat_app:name ?category_card_service .
                FILTER (langMatches(lang(?category_card_open),"' . LANG . '"))
                FILTER (langMatches(lang(?category_card_service),"' . LANG . '"))
            }
        }
        GROUP BY ?provider ?prefLabel ?title ?bgColor ?category_card_open ?category_card_service
        ORDER BY DESC(?bgColor)
        ';

    $result = $db->query($sparql);
    if (!$result) {
        print $db->errno() . ": " . $db->error() . "\n";
        exit;
    }

    while ($row = $result->fetch_array()) {
        if (!isset($row['bgColor'])) {
            $row['bgColor'] = 'grey';
        }
        showCardTemplate($row['provider'], $row['prefLabel'], $row['title'], $row['numberOfServices'], $row['bgColor'], '?c=category&cat=',  $row['category_card_open'], $row['category_card_service'], 4, '', 'Dienste');
    }

    ?>
</div>
