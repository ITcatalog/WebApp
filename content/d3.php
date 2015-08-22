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

$sparql = '
SELECT ?service ?prefLabel ?bgColor ?service2 ?prefLabel2 ?bgColor2 ?category2 ?category
WHERE {
  ?service a schema:Service.
  OPTIONAL {
    ?service skos:prefLabel ?prefLabelLang.
  FILTER (langMatches(lang(?prefLabelLang),"de"))
    BIND (str(?prefLabelLang) AS ?prefLabel)
    OPTIONAL{
      ?service itcat:inCategory ?category.
      ?category a itcat:CatalogCategory.
      GRAPH ?g {
          ?category itcat_app:hasBgColor ?bgColor.
        }.
    }
  }
  OPTIONAL{
    ?service schema:isRelatedTo+ ?service2.
    OPTIONAL{
        ?service2 skos:prefLabel ?prefLabel2Lang.
        FILTER (langMatches(lang(?prefLabel2Lang),"de"))
        BIND (str(?prefLabel2Lang) AS ?prefLabel2)
    OPTIONAL{
      ?service2 itcat:inCategory ?category2.
      ?category2 a itcat:CatalogCategory.
      GRAPH ?g {
          ?category2 itcat_app:hasBgColor ?bgColor2.
        }.
    }
    }
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

    $nodes[$row['service']] = array(
      "name" => $row['prefLabel'],
      "value" => 336,
      "group" => 2,
    );
    "{id:'".$row['service']."', , color: '".$bgColorHex."', group: '".$row['category']."'}";
  }

  if(isset($row['service']) && isset($row['service2'])){
    $edges[] = array(
      "source"  => $row['service']."',
      "target"  => $row['service2'],
      "value"   => 2
    );
  }

}


$nodes = array(
  "name" => "dce",
  "nbIncomingLinks" => 336,
  "group" => 2,
);


$links = array(
  "source"  => 1,
  "target"  => 0,
  "value"   => 2
);

echo json_encode($nodes, JSON_FORCE_OBJECT);

echo json_encode($links, JSON_FORCE_OBJECT);


?>



<?php
foreach ($nodes as $value) {
  #echo $value . ", \n";
}
?>

<script src="bower_components/d3/d3.min.js" charset="utf-8"></script>



<div class="visus">
          <div class="visusContainerVocab">
            <div id="visIn" style="width:50%; float:left;"> </div>
            <div id="visOut" style="width:50%; float:left;"></div>
          </div>
        </div>
        <ul class="visusLegend">
          <li><span style="background-color:#f7b6d2" class="color"></span><span class="linkName">Metadata</span></li>
          <li><span style="background-color:#2ca02c" class="color"> </span><span class="linkName">Extends</span></li>
          <li><span style="background-color:#1f77b4" class="color"> </span><span class="linkName">Specializes</span></li>
          <li><span style="background-color:#aec7e8" class="color"> </span><span class="linkName">Generalizes</span></li>
          <li><span style="background-color:#7f7f7f" class="color"> </span><span class="linkName">Has Equivalences with</span></li>
          <li><span style="background-color:#c7c7c7" class="color"> </span><span class="linkName">Has Disjunction with</span></li>
          <li><span style="background-color:#d62728" class="color"> </span><span class="linkName">Imports</span></li>
        </ul>


<script>


var width = 600,
height = 600,
nodeMinSize = 20,
nodeMaxSize = 60,
linkDistance = 60,
chargeFactor = 30;
var category20 = d3.scale.category20();
var color=[];
for(i=0; i<20; i++){color[i]=(category20(i));}

var graph = {
  "nodes":[
    {"name":"dce","value":3,"group":2},
    {"name":"dce","value":3,"group":2},
    {"name":"rdf","value":5,"group":13},
    {"name":"skos","value":100,"group":13}
  ],
  "links":[
    {"source":1,"target":0,"value":2},
    {"source":2,"target":0,"value":2},
    {"source":3,"target":0,"value":2}
  ]
};


var dataOutMin = d3.min(graph.nodes, function(d) { return d.value; });
var dataOutMax = d3.max(graph.nodes, function(d) { return d.value; });
var linearScale = d3.scale.linear().domain([dataOutMin,dataOutMax]).range([nodeMinSize,nodeMaxSize]);

var force = d3.layout.force()
.charge(function(d) {return -chargeFactor * linearScale(d.value);})
.linkDistance(linkDistance)
.size([width, height])
.nodes(graph.nodes)
.links(graph.links)
.start();

var svg = d3.select("#visOut").append("svg")
.attr("viewBox","0 0 600 600")
.attr("perserveAspectRatio","xMinYMin")
.attr("width", width)
.attr("height", height)
.attr("class", "bubble");

var link = svg.selectAll(".link")
.data(graph.links)
.enter().append("line")
.attr("class", "link")
.style("stroke", "#000000")
.style("stroke-opacity", ".6")
.style("stroke-width", function(d) { return d.value; });

var node = svg.selectAll(".node")
.data(graph.nodes).enter().append('g').classed('node', true)
.on("click", function(d) { self.location= "?service="+ d.name});;

node.append("circle")
.attr("r", function(d) { return linearScale(d.value); })
.style("fill", function(d) { return color[d.group]; });

node.append("text")
.style("text-anchor", "middle")
.attr("dy", ".35em")
.text(function(d) { return d.name.substring(0, linearScale(d.value) / 2); })
.style("font-size", function (d) { return ((linearScale(d.value) / 20)) + "em"; });

node.append("title")
.text(function(d) { return d.name; });

force.on("tick", function() {
link.attr("x1", function(d) { return d.source.x; })
.attr("y1", function(d) { return d.source.y; })
.attr("x2", function(d) { return d.target.x; })
.attr("y2", function(d) { return d.target.y; });

node.attr("transform", function(d) { return "translate(" + d.x + "," + d.y + ")"; });
});


/*


var dataInMin = d3.min(graphIn.nodes, function(d) { return d.nbIncomingLinks; });
var dataInMax = d3.max(graphIn.nodes, function(d) { return d.nbIncomingLinks; });
var linearScaleIn = d3.scale.linear().domain([dataInMin,dataInMax]).range([nodeMinSize,nodeMaxSize]);

var forceIn = d3.layout.force()
.charge(function(d) {return -chargeFactor * linearScaleIn(d.nbIncomingLinks);})
.linkDistance(linkDistance)
.size([width, height])
.nodes(graphIn.nodes)
.links(graphIn.links)
.start();

var svgIn = d3.select("#visIn").append("svg")
.attr("viewBox","0 0 600 600")
.attr("perserveAspectRatio","xMinYMin")
.attr("width", width)
.attr("height", height)
.attr("class", "bubble2");

var linkIn = svgIn.selectAll(".link")
.data(graphIn.links)
.enter().append("line")
.attr("class", "link")
.style("stroke", "#000000")
.style("stroke-opacity", ".6")
.style("stroke-width", function(d) { return d.value; });

var nodeIn = svgIn.selectAll(".node")
.data(graphIn.nodes).enter().append('g').classed('node', true)
.on("click", function(d) { self.location= "/dataset/lov/vocabs/"+ d.name});;

nodeIn.append("circle")
.attr("r", function(d) { return linearScaleIn(d.nbIncomingLinks); })
.style("fill", function(d) { return color[d.group]; });

nodeIn.append("text")
.style("text-anchor", "middle")
.attr("dy", ".35em")
.text(function(d) { return d.name.substring(0, linearScaleIn(d.nbIncomingLinks) / 2); })
.style("font-size", function (d) { return ((linearScaleIn(d.nbIncomingLinks) / 20)) + "em"; });

nodeIn.append("title")
.text(function(d) { return d.name; });

forceIn.on("tick", function() {
linkIn.attr("x1", function(d) { return d.source.x; })
.attr("y1", function(d) { return d.source.y; })
.attr("x2", function(d) { return d.target.x; })
.attr("y2", function(d) { return d.target.y; });

nodeIn.attr("transform", function(d) { return "translate(" + d.x + "," + d.y + ")"; });
});



var graphIn = {
  "nodes":[
    {"name":"dce","nbIncomingLinks":336,"group":2},
    {"name":"eac-cpf","nbIncomingLinks":1,"group":13},
    {"name":"awol","nbIncomingLinks":2,"group":13},
    {"name":"va","nbIncomingLinks":1,"group":13},
    {"name":"oc","nbIncomingLinks":7,"group":13},
    {"name":"being","nbIncomingLinks":1,"group":13},
    {"name":"trait","nbIncomingLinks":2,"group":13},
    {"name":"crsw","nbIncomingLinks":1,"group":13},
    {"source":3,"target":0,"value":2},
    {"source":4,"target":0,"value":2},
    {"source":5,"target":0,"value":2},
    ]
};

var chartVis = $(".bubble"),
aspectVis = chartVis.width() / chartVis.height(),
containerVis = chartVis.parent();
var chartVisIn = $(".bubble2"),
aspectVisIn = chartVisIn.width() / chartVisIn.height(),
containerVisIn = chartVisIn.parent();
$(window).on("resize", function() {
var targetWidthVis = containerVis.width();
chartVis.attr("width", targetWidthVis);
chartVis.attr("height", Math.round(targetWidthVis / aspectVis));
var targetWidthVisIn = containerVisIn.width();
chartVisIn.attr("width", targetWidthVisIn);
chartVisIn.attr("height", Math.round(targetWidthVisIn / aspectVisIn));
}).trigger("resize");
*/


</script>
