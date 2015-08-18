<?php
require_once( "lib/sparqllib.php" );

$db = sparql_connect( "http://fbwsvcdev.fh-brandenburg.de:8080/fuseki/testDataSet/query" );
if( !$db ) { print $db->errno() . ": " . $db->error(). "\n"; exit; }

//# bei Namespaces notwendig
$db->ns( "rdf","http://www.w3.org/1999/02/22-rdf-syntax-ns#" );
$db->ns( "rdfs","http://www.w3.org/2000/01/rdf-schema#" );
$db->ns( "dcterms","http://purl.org/dc/terms/");
$db->ns( "skos","http://www.w3.org/2004/02/skos/core#");
$db->ns( "schema","https://schema.org#");
$db->ns( "usdlagreement","http://www.linked-usdl.org/ns/usdl-agreement#");
$db->ns( "itcat","http://th-brandenburg.de/ns/itcat#" );
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
