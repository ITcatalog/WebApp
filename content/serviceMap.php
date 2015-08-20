<?php
function convertColorNameToHex ($colorName){
  switch($colorName){

    case "lime":
      $bgColorHex = '#DCE775';
      break;

    case "blue":
      $bgColorHex = '#64B5F6';
      break;

    case "light-blue":
      $bgColorHex = '#4FC3F7';
      break;

    case "brown":
      $bgColorHex = '#a1887f';
      break;

    case "deep-purple":
      $bgColorHex = '#9575cd';
      break;

    case "red":
      $bgColorHex = '#e57373';
      break;

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
  SELECT ?category ?category2 ?service ?service2 ?prefLabel ?prefLabel2 ?cat ?cat2 ?bgColor ?bgColor2
  WHERE {
    itcat:HISQIS (^schema:isRelatedTo | schema:isRelatedTo)+ ?service.
    ?service schema:isRelatedTo ?service2.

    OPTIONAL {
      ?service  skos:prefLabel ?prefLabelLang.
      FILTER (langMatches(lang(?prefLabelLang),"'.LANG.'"))
      BIND (str(?prefLabelLang) AS ?prefLabel)
    }
    OPTIONAL{
      ?service2  skos:prefLabel ?prefLabel2Lang.
      FILTER (langMatches(lang(?prefLabel2Lang),"'.LANG.'"))
      BIND (str(?prefLabel2Lang) AS ?prefLabel2)
    }

    OPTIONAL{
      ?service itcat:inCategory ?category.
      ?category a itcat:CatalogCategory.
      GRAPH ?g {
          ?category itcat_app:hasBgColor ?bgColor.
        }.
    }
    OPTIONAL{
      ?service2 itcat:inCategory ?category2.
      ?category2 a itcat:CatalogCategory.
      GRAPH ?g {
          ?category2 itcat_app:hasBgColor ?bgColor2.
        }.
    }
  }
';


$result = $db->query( $sparql );
if( !$result ) { print $db->errno() . ": " . $db->error(). "\n"; exit; }
$nodes = array();

while( $row = $result->fetch_array() ){

  if(!in_array($row['service'], $nodes)){
    if(!isset($row['category'])){
      $row['category'] = 'none';
    }

    if(isset($row['bgColor'])){
      $bgColorHex = convertColorNameToHex($row['bgColor']);
    }
    else{
      $bgColorHex = '#ffffff';
    }

    if(!isset($row['prefLabel'])) { $row['prefLabel'] = 'empty';}

    $nodes[$row['service']] = "{id:'".$row['service']."', label:'".substr($row['prefLabel'], 0, 20)."', color: '".$bgColorHex."', group: '".$row['category']."'}";
  }
  if(!in_array($row['service2'], $nodes)){

    if(!isset($row['category2'])){
      $row['category2'] = 'none';
    }

    if(isset($row['bgColor2'])){
      $bgColorHex = convertColorNameToHex($row['bgColor2']);
    }
    else{
      $bgColorHex = '#ffffff';
    }

    if(!isset($row['prefLabel2'])) { $row['prefLabel2'] = 'empty';}

    $nodes[$row['service2']] = "{id:'".$row['service2']."', label:'".substr($row['prefLabel2'], 0, 20)."', color: '".$bgColorHex."', group: '".$row['category2']."'}";

  }
  $edges[] = "{from: '".$row['service']."', to: '".$row['service2']."'}";
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
