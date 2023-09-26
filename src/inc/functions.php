<?php

if (isset($_GET['input'])) {
    $input = $_GET['input'];

    if (filter_var($input, FILTER_VALIDATE_URL)) {
        $input = urldecode($input);
        $sparql = '
            SELECT *
            WHERE {
              <' . $input . '> a ?type.
            }
            ';
        $uri = urlencode($input);
    } else {
        $sparql = '
            SELECT *
            WHERE {
              itcat:' . $input . ' a ?type.
            }
            ';
        $uri = urlencode('http://th-brandenburg.de/ns/itcat#' . $input . '');
    }


    $result = $db->query($sparql);
    if (!$result) {
        print $db->errno() . ": " . $db->error() . "\n";
        exit;
    }

    if ($result->num_rows() >= 1) {
        $row = $result->fetch_array();
        $type = $row['type'];

        switch ($row['type']) {
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
                $_GET['search'] = 'in:' . $uri;
                break;
        }
    } else {
        echo 'Error';
        exit;
    }

    //Handle input
    //what type?


}

$subPath = array(array("Service-Kategorien", "Service-Karte", "Service-Liste", "Service-Kataloge", "Reports", "Impressum", "Suche"), array("Service-Categories", "Service-Map", "Service-Lists", "Service-Catalog", "Reports", "Imprint", "Search"));
$pos = 0;
if (LANG == 'en') {
    $pos = 1;
}

if (isset($_GET['c'])) {
    switch ($_GET['c']) {
        case 'home':
            $includePath = 'content/home.php';
            break;

        case 'categories':
            $includePath = 'content/categories.php';
            $pageTitle = $subPath[$pos][0];
            break;

        case 'category':
            $includePath = 'content/category.php';
            break;

        case 'service':
            $includePath = 'content/service.php';
            break;

        case 'map':
            $includePath = 'content/map.php';
            $pageTitle = $subPath[$pos][1];
            break;

        case 'list':
            $includePath = 'content/list.php';
            $pageTitle = $subPath[$pos][2];
            break;

        case 'catalog':
            $includePath = 'content/catalog.php';
            $pageTitle = $subPath[$pos][3];
            break;

        case 'reports':
            $includePath = 'content/reports.php';
            $pageTitle = $subPath[$pos][4];
            break;

        case 'portfolio':
            $includePath = 'content/portfolio.php';
            $pageTitle = 'Portfolio';
            break;

        case 'help':
            $includePath = 'content/help.php';
            $pageTitle = 'Hilfe';
            break;
			
	    case 'imprint':
	        $includePath = 'content/imprint.php';
	        $pageTitle = $subPath[$pos][5];
	        break;

    }
} elseif (isset($_GET['search'])) {
    $includePath = 'content/search.php';
    $pageTitle = $subPath[$pos][6];
} else {

    $includePath = 'content/home.php';

}


if (isset($_GET['include'])) {

    $includePath = 'content/' . $_GET['include'];

}
