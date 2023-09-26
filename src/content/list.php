<table class="mdl-cell--12-col mdl-data-table mdl-js-data-table mdl-text-table">
    <thead>
    <tr>
        <?php
        $sparql = '
        SELECT ?list_name ?list_description
        {
          GRAPH ?g {
            itcat_app:list_name itcat_app:name ?list_name .
            itcat_app:list_description itcat_app:name ?list_description .
            FILTER (langMatches(lang(?list_name),"' . LANG . '"))
            FILTER (langMatches(lang(?list_description),"' . LANG . '"))
          }
        }
        ';

        $result = $db->query($sparql);
        if (!$result) {
            print $db->errno() . ": " . $db->error() . "\n";
            exit;
        }
        while ($row = $result->fetch_array()) {
            echo '<th class="mdl-data-table__cell--non-numeric">'.$row['list_name'].'</th>';
            echo '<th class="mdl-data-table__cell--non-numeric">'.$row['list_description'].'</th>';
            echo '<th class="mdl-data-table__cell--non-numeric">URL</th>';
        }
        ?>
    </tr>
    </thead>
    <tbody>
    <?php

    $sparql = '
        SELECT ?service ?prefLabel ?abstract ?url
        {
          ?service a schema:Service;
          skos:prefLabel ?prefLabelLang;
          dcterms:abstract ?abstractLang;
          schema:url ?url.
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
    while ($row = $result->fetch_array()) {

        echo '<tr onclick="document.location = \'?c=service&service=' . urlencode($row['service']) . '\';" style="cursor:pointer;">';
        echo '<td class="mdl-data-table__cell--non-numeric">' . $row['prefLabel'] . '</td>';
        echo '<td class="mdl-data-table__cell--non-numeric">' . $row['abstract'] . '</td>';
        echo '<td class="mdl-data-table__cell--non-numeric mdl-data-table--selectable"><a href="' . $row['url'] . '" target="_blank"><i class="material-icons">link</i></a></td>';
        echo '</tr>';

    }


    ?>
    </tbody>
</table>
