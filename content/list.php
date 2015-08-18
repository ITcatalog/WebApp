

<table class="mdl-cell--12-col mdl-data-table mdl-js-data-table ">
  <thead>
    <tr>
      <th class="mdl-data-table__cell--non-numeric">Service-Name</th>
      <th class="mdl-data-table__cell--non-numeric">Beschreibung</th>
      <th class="mdl-data-table__cell--non-numeric">URL</th>
    </tr>
  </thead>
  <tbody>
    <?php

    $sparql = "
    SELECT *
  	WHERE {
  		?service rdf:type schema:Service;
      	skos:prefLabel ?prefLabel;
  	    dcterms:subject ?subject;
      	dcterms:description ?description;
      	schema:url ?url;
  	}
    ";

    $result = $db->query( $sparql );
    if( !$result ) { print $db->errno() . ": " . $db->error(). "\n"; exit; }
    while( $row = $result->fetch_array() ){

      echo '<tr onclick="document.location = \'?c=service&service='.urlencode($row['service']).'\';" style="cursor:pointer;">';
        echo '<td class="mdl-data-table__cell--non-numeric">'.$row['prefLabel'].'</td>';
        echo '<td class="mdl-data-table__cell--non-numeric">'.$row['subject'].'</td>';
        echo '<td class="mdl-data-table__cell--non-numeric"><a href="'.$row['url'].'" target="_blank"><i class="material-icons">link</i></a></td>';
      echo '</tr>';

    }



     ?>
  </tbody>
</table>
