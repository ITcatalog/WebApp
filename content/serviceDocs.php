
<div class="mdl-cell--12-col mdl-grid mdl-grid--no-spacing">
  <table class="mdl-cell--12-col mdl-data-table mdl-js-data-table ">
    <thead>
      <tr>
        <th class="mdl-data-table__cell--non-numeric">Dokument</th>
        <th class="mdl-data-table__cell--non-numeric"></th>
      </tr>
    </thead>
    <tbody>
      <?php

      $sparql = '
      SELECT ?document
      WHERE {
        <'.$service.'> foaf:page ?document.
      }
      ';

      $result = $db->query( $sparql );
      if( !$result ) { print $db->errno() . ": " . $db->error(). "\n"; exit; }
      while( $row = $result->fetch_array() ){
        echo '<tr onclick="window.open(\' '. $row['document'] .'\');" style="cursor:pointer;">';
          echo '<td class="mdl-data-table__cell--non-numeric">'.$row['document'].'</td>';
          echo '<td class="mdl-data-table__cell--non-numeric"><a href="'.$row['document'].'" target="_blank"><i class="material-icons">link</i></a></td>';
        echo '</tr>';
      }
     ?>
    </tbody>
  </table>
</div>
