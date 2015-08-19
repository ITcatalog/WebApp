<?php
function convertColorNameToHex ($colorName){
  switch($colorName){
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
  return $bgColorHex;
}


$service = urldecode($_GET['service']);


$sparql = '
  SELECT *
  FROM NAMED <'.$dataGraphs['ApplicationGraph'].'>
  WHERE {
    <'.$service.'> (^schema:isRelatedTo | schema:isRelatedTo)+ ?service.
    ?service schema:isRelatedTo ?serviceX.
    OPTIONAL{
      ?service  skos:prefLabel ?prefLabel.
    }
    OPTIONAL{
      ?serviceX  skos:prefLabel ?prefLabelX.
    }
    OPTIONAL{
      ?cat itcat:hasITService ?service.
      GRAPH ?g {
          ?cat itcat_app:hasBGColor ?bgColor.
        }.
    }
    OPTIONAL{
      ?catX itcat:hasITService ?serviceX.
      GRAPH ?g {
          ?catX itcat_app:hasBGColor ?bgColorX.
        }.
    }
  }
';


$result = $db->query( $sparql );
if( !$result ) { print $db->errno() . ": " . $db->error(). "\n"; exit; }
$nodes = array();

while( $row = $result->fetch_array() ){

  if(!in_array($row['service'], $nodes)){
    if(!isset($row['cat'])){
      $row['cat'] = 'none';
    }

    if(isset($row['bgColor'])){
      $bgColorHex = convertColorNameToHex($row['bgColor']);
    }
    else{
      $bgColorHex = '#ffffff';
    }

    if(!isset($row['prefLabel'])) { $row['prefLabel'] = 'empty';}

    $nodes[$row['service']] = "{id:'".$row['service']."', label:'".substr($row['prefLabel'], 0, 20)."', color: '".$bgColorHex."', group: '".$row['cat']."'}";
  }
  if(!in_array($row['serviceX'], $nodes)){

    if(!isset($row['catX'])){
      $row['catX'] = 'none';
    }

    if(isset($row['bgColorX'])){
      $bgColorHex = convertColorNameToHex($row['bgColorX']);
    }
    else{
      $bgColorHex = '#ffffff';
    }

    if(!isset($row['prefLabelX'])) { $row['prefLabelX'] = 'empty';}

    $nodes[$row['serviceX']] = "{id:'".$row['serviceX']."', label:'".substr($row['prefLabelX'], 0, 20)."', color: '".$bgColorHex."', group: '".$row['catX']."'}";

  }
  $edges[] = "{from: '".$row['service']."', to: '".$row['serviceX']."'}";
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
