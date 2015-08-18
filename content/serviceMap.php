<?php
$service = urldecode($_GET['service']);


$sparql = '
  SELECT *
  FROM NAMED <http://fbwsvcdev.fh-brandenburg.de:8080/fuseki/testDataSet/data/ApplicationGraph>
  WHERE {
    ?x  rdf:type schema:Service;
        skos:prefLabel ?labelX;
        schema:isRelatedTo ?y.
    ?y  skos:prefLabel ?labelY.
    <'.$service.'> schema:isRelatedTo ?y.
    OPTIONAL{
      ?catX itcat:hasITService ?x.
      GRAPH ?g {
          ?catX itcat_app:hasBGColor ?bgColorX.
        }.
    }
    OPTIONAL{
      ?catY itcat:hasITService ?y.
     GRAPH ?g {
          ?catY itcat_app:hasBGColor ?bgColorY.
        }.
    }
  }
';


$result = $db->query( $sparql );
if( !$result ) { print $db->errno() . ": " . $db->error(). "\n"; exit; }
$nodes = array();

while( $row = $result->fetch_array() ){

  if(!in_array($row['x'], $nodes)){
    if(!isset($row['catX'])){
      $row['catX'] = 'none';
    }

    if(isset($row['bgColorX'])){
      switch($row['bgColorX']){
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

    $nodes[$row['x']] = "{id:'".$row['x']."', label:'".substr($row['labelX'], 0, 20)."', color: '".$bgColorHex."', group: '".$row['catX']."'}";
  }
  if(!in_array($row['y'], $nodes)){
    if(!isset($row['catY'])){
      $row['catY'] = 'none';
    }
    if(isset($row['bgColorY'])){
      switch($row['bgColorY']){
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

    $nodes[$row['y']] = "{id:'".$row['y']."', label:'".substr($row['labelY'], 0, 20)."', color: '".$bgColorHex."', group: '".$row['catY']."'}";
  }

  $edges[] = "{from: '".$row['x']."', to: '".$row['y']."'}";
}

//Mark Selected Service

$sparql = '
  SELECT *
  WHERE {
    <'.$service.'> skos:prefLabel ?prefLabel.
  }
';

$result = $db->query( $sparql );
if( !$result ) { print $db->errno() . ": " . $db->error(). "\n"; exit; }
$row = $result->fetch_array();
$nodes[$service] = "{id:'$service', label:'".$row['prefLabel']."', color: '#CC0000', group: 'center'}";


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
      echo $value . ",\n";
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
      stabilization: true
    },
    nodes:{
      fixed: {
        x:false,
        y:false
      }
    }
  };
  var network = new vis.Network(container, data, options);



  $( document ).ready(function() {
    var ids = ["<?php echo $service; ?>"];
    network.selectNodes(ids);


    setTimeout(function(){
       network.focus('<?php echo $service; ?>');
     }, 1000);


  });


  </script>
