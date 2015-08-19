<?php
require_once( "lib/sparqllib.php" );

define ('LANG', 'de');



$dataGraphs['applicationGraph'] = 'http://fbwsvcdev.fh-brandenburg.de:8080/fuseki/itcat/data/ApplicationGraph';
#$dataGraphs['schemaGraph'] = 'http://fbwsvcdev.fh-brandenburg.de:8080/fuseki/itcat/data/SchemaGraph';

$db = sparql_connect( "http://fbwsvcdev.fh-brandenburg.de:8080/fuseki/itcat/query" );
if( !$db ) { print $db->errno() . ": ". $db->error(). "\n"; exit; }

$db->ns( "owl","http://www.w3.org/2002/07/owl#");
$db->ns( "rdf","http://www.w3.org/1999/02/22-rdf-syntax-ns#");
$db->ns( "xml","http://www.w3.org/XML/1998/namespace");
$db->ns( "xsd","http://www.w3.org/2001/XMLSchema#");
$db->ns( "rdfs","http://www.w3.org/2000/01/rdf-schema#");
$db->ns( "skos","http://www.w3.org/2004/02/skos/core#");
$db->ns( "dcterms","http://www.purl.org/dc/terms/");
$db->ns( "gr","http://purl.org/goodrelations/v1#");
$db->ns( "foaf","http://xmlns.com/foaf/0.1/");
$db->ns( "prov","http://www.w3.org/ns/prov#");
$db->ns( "schema","http://schema.org/");
$db->ns( "usdl-core","http://www.linked-usdl.org/ns/usdl-core#");
$db->ns( "usdl-agreement","http://www.linked-usdl.org/ns/usdl-agreement#");

$db->ns( "itcat","http://th-brandenburg.de/ns/itcat#");
$db->ns( "itcat_app","http://th-brandenburg.de/ns/itcat_app#" );


if(isset($_GET['c'])){
  switch ($_GET['c']){
    case 'home':
      $includePath = 'content/home.php';
      break;

    case 'categories':
      $includePath = 'content/categories.php';
      $pageTitle = 'Service-Kategorien';
      break;

    case 'category':
      $includePath = 'content/category.php';
      break;

    case 'service':
      $includePath = 'content/service.php';
      break;

    case 'map':
      $includePath = 'content/map.php';
      $pageTitle = 'Service-Karte';
      break;

    case 'list':
      $includePath = 'content/list.php';
      $pageTitle = 'Service-Liste';
      break;

    case 'catalog':
      $includePath = 'content/catalog.php';
      $pageTitle = 'Service-Kataloge';
      break;

    case 'reports':
      $includePath = 'content/reports.php';
      $pageTitle = 'Reports';
      break;

  }
}
elseif(isset($_GET['search'])){
  $includePath = 'content/search.php';
  $pageTitle = 'Suche';
}
else {

  $includePath = 'content/home.php';

}


if(isset($_GET['include'])){

  $includePath = 'content/' . $_GET['include'];

}

?>
