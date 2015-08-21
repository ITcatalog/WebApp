<?php

if(isset($_GET['input'])){
  $input = $_GET['input'];

  if (filter_var($input, FILTER_VALIDATE_URL)) {
    $input = urldecode($input);
    $sparql = '
    SELECT *
    WHERE {
      <'.$input.'> a ?type.
    }
    ';
    $uri = urlencode($input);
  }
  else{
    $sparql = '
    SELECT *
    WHERE {
      itcat:'.$input.' a ?type.
    }
  	';
    $uri = urlencode('http://th-brandenburg.de/ns/itcat#'.$input.'');
  }



	$result = $db->query( $sparql );
	if( !$result ) { print $db->errno() . ": " . $db->error(). "\n"; exit; }

  if($result->num_rows() == 1){
    $row = $result->fetch_array();
    $type = $row['type'];

    switch($row['type']){
      case 'http://schema.org/Service':
        $_GET['c'] = 'service';
        $_GET['service'] = $uri;
        break;

      case 'http://th-brandenburg.de/ns/itcat#SubjectCategory':
      case 'http://th-brandenburg.de/ns/itcat#CatalogCategory':
        $_GET['c'] = 'category';
        $_GET['cat'] = $uri;
        break;

      case 'http://xmlns.com/foaf/0.1/Person':
      case 'http://schema.org/Role':
      case 'http://xmlns.com/foaf/0.1/Organization':
        $_GET['search'] = 'in:'.$uri;
        break;
    }
  }
  else{
    echo 'Error';
    exit;
  }

  //Handle input
  //what type?


}


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
