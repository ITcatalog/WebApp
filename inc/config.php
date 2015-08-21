<?php
require_once( "lib/sparqllib.php" );


/***************************************
/*
/*      CONFIG Parameters
/*
***************************************/

define ('LANG', 'de');

define ('SPARQL_ENDPOINT', 'http://fbwsvcdev.fh-brandenburg.de:8080/fuseki/itcat/query');

/***************************************/
/***************************************/


#Sparql Endpoint
$db = sparql_connect( SPARQL_ENDPOINT );


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


?>
