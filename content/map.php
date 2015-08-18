<?php

#$service = 'itcat:Moodle';

$sparql = '
  SELECT *
  FROM NAMED <http://fbwsvcdev.fh-brandenburg.de:8080/fuseki/testDataSet/data/ApplicationGraph>
  WHERE {
    ?service rdf:type schema:Service;
    skos:prefLabel ?labelX.
    OPTIONAL {
      ?cat itcat:hasITService ?service.
      GRAPH ?g {
        ?cat itcat_app:hasBGColor ?bgColor.
      }.
    }
  }
';

$result = $db->query( $sparql );
if( !$result ) { print $db->errno() . ": " . $db->error(). "\n"; exit; }

while( $row = $result->fetch_array() ){

  if(!isset($row['cat'])){
    $row['cat'] = 'none';
  }

  if(isset($row['bgColor'])){
    switch($row['bgColor']){
      case 'purple':
        $bgColorHex = '#BA68C8';
        break;

      case 'cyan':
        $bgColorHex = '#4DD0E1';
        break;

      case 'orange':
        $bgColorHex = '#FFB74D';
        break;

      case 'green':
        $bgColorHex = '#81C784';
        break;

      default:
        $bgColorHex = '#ffffff';
        break;
    }
  }
  else{
    $bgColorHex = '#ffffff';
  }

  $nodes[] = "{id:'".$row['service']."', label:'".substr($row['labelX'], 0, 20)."', color: '".$bgColorHex."', group: '".$row['cat']."'}";
}

$sparql = '
  SELECT *
  WHERE {
    ?x  rdf:type schema:Service;
  	    skos:prefLabel ?labelX;
	      schema:isRelatedTo ?y.
  	?y  skos:prefLabel ?labelY.
  }
';

$result = $db->query( $sparql );
if( !$result ) { print $db->errno() . ": " . $db->error(). "\n"; exit; }

while( $row = $result->fetch_array() ){
  $edges[] = '{from: \''. $row['x'] .'\', to: \''. $row['y'] .'\'}';
}


?>

<div >


</div>

<div id="mynetwork" class="mdl-cell mdl-cell--12-col mdl-grid mdl-shadow--2dp mdl-card" style="height:700px;"></div>

<script type="text/javascript">
  // create an array with nodes

  var nodes = new vis.DataSet([
    <?php
    foreach ($nodes as $value) {
      echo $value . ", \n";
    }
    ?>
  ]);
  // create an array with edges
  var edges = new vis.DataSet([
    <?php
    foreach ($edges as $value) {
      echo $value . ", \n";
    }
    ?>
  ]);
  // create a network
  var container = document.getElementById('mynetwork');
  var data = {
    nodes: nodes,
    edges: edges
  };
  var options = {
    layout: {
      randomSeed:1
    },
    physics: {
      stabilization: false
    }

  };
  var network = new vis.Network(container, data, options);
</script>
