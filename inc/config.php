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
