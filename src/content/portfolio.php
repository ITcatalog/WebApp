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

$result = $db->query( $sparql );
if( !$result ) { print $db->errno() . ": " . $db->error(). "\n"; exit; }

while($row = $result->fetch_array()){
    //Build Array of Grid
    $grid[$row['criticalityLabel']][$row['priorityLabel']][] = array(
        'uri' => $row['service'],
        'prefLabel' => $row['prefLabel'],
    );
}


function printGridItems($array, $y, $x){
    // check if array exists

    $return = '';
    if((array_key_exists($x, $array[$y]) && array_key_exists($y, $array))){

        foreach ($array[$y][$x] as $item) {
            $return .= '<a href="?input='. urlencode($item['uri']) .'">'. $item['prefLabel'] . '</a><br />';
        }

    }
    else{
        // do nothing
    }

    return $return;

}?>

<div class="mdl-cell mdl-cell--12-col mdl-grid">

    <?php
    echo '<table border="1" style="text-align:center" mdl-cell--12-col>';
        echo '<tr>';
            echo '<td rowspan="3">Criticality</td>';
            echo '<td>High</td>';
            echo '<td>' . printGridItems($grid, 'HighCriticality', 'LowPriority') .'</td>';
            echo '<td>' . printGridItems($grid, 'HighCriticality', 'MediumPriority') .'</td>';
            echo '<td>' . printGridItems($grid, 'HighCriticality', 'HighPriority') .'</td>';
        echo '</tr>';

        echo '<tr>';
            echo '<td>Medium</td>';
            echo '<td>' . printGridItems($grid, 'MediumCriticality', 'LowPriority') .'</td>';
            echo '<td>' . printGridItems($grid, 'MediumCriticality', 'MediumPriority') .'</td>';
            echo '<td>' . printGridItems($grid, 'MediumCriticality', 'HighPriority') .'</td>';
        echo '</tr>';

        echo '<tr>';
            echo '<td>Low</td>';
            echo '<td>' . printGridItems($grid, 'LowCriticality', 'LowPriority') .'</td>';
            echo '<td>' . printGridItems($grid, 'LowCriticality', 'MediumPriority') .'</td>';
            echo '<td>' . printGridItems($grid, 'LowCriticality', 'HighPriority') .'</td>';
        echo '</tr>';

        echo '<tr>';
            echo '<td></td>';
            echo '<td></td>';
            echo '<td>Low</td>';
            echo '<td>Mid</td>';
            echo '<td>High</td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td></td>';
        echo '<td></td>';
        echo '<td colspan="3">Priority</td>';
        echo '</tr>';
    echo '</table>';

    ?>

</div>
