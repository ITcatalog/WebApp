<?php
$sparql = '
    SELECT ?service ?prefLabel ?criticalityLabel ?priorityLabel ?prefLabelCat {
        ?service a schema:Service;
        skos:prefLabel ?prefLabelLang;
        itcat:hasCriticality  ?criticality ;
        itcat:hasPriority     ?priority.
        OPTIONAL{
            ?service itcat:inCategory ?catalog.
            ?catalog a itcat:CatalogCategory.
            ?catalog skos:prefLabel ?prefLabelCatLang.
        }
        ?criticality rdfs:label ?criticalityLabel.
        ?priority rdfs:label ?priorityLabel.
        FILTER (langMatches(lang(?prefLabelCatLang),"de"))
        BIND (str(?prefLabelCatLang) AS ?prefLabelCat)
        FILTER (langMatches(lang(?prefLabelLang),"de"))
        BIND (str(?prefLabelLang) AS ?prefLabel)
    }
    ORDER BY ?criticalityLabel ?priorityLabel
    ';

$result = $db->query($sparql);
if (!$result) {
    print $db->errno() . ": " . $db->error() . "\n";
    exit;
}

while ($row = $result->fetch_array()) {
    //Build Array of Grid
    $grid[$row['criticalityLabel']][$row['priorityLabel']][] = array(
        'uri' => $row['service'],
        'prefLabel' => $row['prefLabel'],
    );
}


function printGridItems($array, $y, $x)
{
    // check if array exists

    $return = '';
    if ((array_key_exists($x, $array[$y]) && array_key_exists($y, $array))) {

        foreach ($array[$y][$x] as $item) {
            #$return .= '<a href="?input='. urlencode($item['uri']) .'">'. $item['prefLabel'] . '</a><br />';
            $return .= '<li><a href="?input=' . urlencode($item['uri']) . '">' . $item['prefLabel'] . '</a></li>';
        }

    } else {
        // do nothing
    }

    return $return;

} ?>

<div class="mdl-cell mdl-cell--12-col mdl-grid mdl-card">
    <h3 style="margin:10px auto;">Kritikalität / Priorität - Matrix</h3>

    <?php
    echo '<table border="0" class="mdl-cell--12-col portfolio-grid">';
    echo '<tr class="HighCriticality">';
    echo '<td class="yaxis--label__header" rowspan="3"><span><h5>Kritikalität</h5></span></td>';
    echo '<td class="yaxis--label" style="transform: rotate(270deg);"><span>Hoch</span></td>';
    echo '<td class="field mdl-color--yellow-50">' . printGridItems($grid, 'HighCriticality', 'LowPriority') . '</td>';
    echo '<td class="field mdl-color--red-50">' . printGridItems($grid, 'HighCriticality', 'MediumPriority') . '</td>';
    echo '<td class="field mdl-color--red-100">' . printGridItems($grid, 'HighCriticality', 'HighPriority') . '</td>';
    echo '</tr>';

    echo '<tr class="MediumCriticality">';
    echo '<td class="yaxis--label" style="transform: rotate(270deg);"><span>Mittel</span></td>';
    echo '<td class="field mdl-color--green-50">' . printGridItems($grid, 'MediumCriticality', 'LowPriority') . '</td>';
    echo '<td class="field mdl-color--yellow-100">' . printGridItems($grid, 'MediumCriticality', 'MediumPriority') . '</td>';
    echo '<td class="field mdl-color--red-50">' . printGridItems($grid, 'MediumCriticality', 'HighPriority') . '</td>';
    echo '</tr>';

    echo '<tr class="LowCriticality">';
    echo '<td class="yaxis--label" style="transform: rotate(270deg);"><span>Niedrig</span></td>';
    echo '<td class="field mdl-color--green-100">' . printGridItems($grid, 'LowCriticality', 'LowPriority') . '</td>';
    echo '<td class="field mdl-color--green-50">' . printGridItems($grid, 'LowCriticality', 'MediumPriority') . '</td>';
    echo '<td class="field mdl-color--yellow-50">' . printGridItems($grid, 'LowCriticality', 'HighPriority') . '</td>';
    echo '</tr>';

    echo '<tr class="xaxis">';
    echo '<td></td>';
    echo '<td></td>';
    echo '<td>Niedrig</td>';
    echo '<td>Mittel</td>';
    echo '<td>Hoch</td>';
    echo '</tr>';
    echo '<tr class="xaxis">';
    echo '<td></td>';
    echo '<td></td>';
    echo '<td colspan="3"><h5>Priorität</h5></td>';
    echo '</tr>';
    echo '</table>';

    ?>

</div>

<div class="mdl-cell mdl-cell--12-col mdl-grid mdl-card">
    <div class="mdl-card__title mdl-card--expand">
        <h2 class="mdl-card__title-text">Information</h2>
    </div>
    <div class="mdl-card__supporting-text">

        <h6>Kritikalität</h6>

        <p>
            Die Kritikalität eines Dienstes gibt an, wie kritisch eine Störung oder der Ausfall eines Dienstes für die
            IT-Versorgung einer Organisation eingeschätzt wird. Kritikalitäten werden nach den folgenden
            Kritikalitätskategorien eingeteilt: hoch, mittel und gering.
        </p>


        <h6>Priorität</h6>

        <p>
            Die Priorität eines Dienstes gibt an, welcher Wert ihm im Rahmen des Dienste-Portfolios einer Organisation
            beigemessen wird. Prioritäten werden nach Prioritätskategorien den folgenden eingeteilt: hoch, mittel und
            gering.
        </p>
    </div>

</div>