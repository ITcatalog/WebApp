<?php

$sparql = '
    SELECT ?provider_tab ?catalog_tab ?category_tab ?detail_contact ?detail_feature
    {    
        GRAPH ?g {
            itcat_app:provider_tab itcat_app:name ?provider_tab .
    		itcat_app:catalog_tab itcat_app:name ?catalog_tab .
    		itcat_app:category_tab itcat_app:name ?category_tab .
    		itcat_app:detail_contact 	itcat_app:name ?detail_contact .
    		itcat_app:detail_feature itcat_app:subCategorie1 ?detail_feature .
            FILTER (langMatches(lang(?provider_tab),"' . LANG . '"))
            FILTER (langMatches(lang(?catalog_tab),"' . LANG . '"))
            FILTER (langMatches(lang(?category_tab),"' . LANG . '"))
            FILTER (langMatches(lang(?detail_contact),"' . LANG . '"))
            FILTER (langMatches(lang(?detail_feature),"' . LANG . '"))
        }
    }';

$result = $db->query($sparql);
if (!$result) {
    print $db->errno() . ": " . $db->error() . "\n";
    exit;
}

$provider_tab = '';
$catalog_tab = '';
$category_tab = '';
$detail_contact = '';
$detail_feature = '';

if ($result->num_rows() > 0) {
    while ($row = $result->fetch_array()) {
        $provider_tab = $row['provider_tab'];
        $catalog_tab = $row['catalog_tab'];
        $category_tab = $row['category_tab'];
        $detail_contact = $row['detail_contact'];
        $detail_feature = $row['detail_feature'];
    }
}

$searchTerm = $_GET['search'];
$searchTermInput = $searchTerm;
$sparqlFilter = '';
$filterItemsHtml = '';

if (isset($_GET['arg'])) {

    if ($_GET['arg'] == 'true') {
        $conditionArray = array();
    } else {
        $conditionArray = unserialize(urldecode($_GET['arg']));
    }

    if (isset($_GET['action'])) {

        $val = urldecode($_GET['action']);

        if (($key = array_search($val, $conditionArray)) !== false) {
            // remove from array
            unset($conditionArray[$key]);
        } else {
            //Add to array
            $conditionArray[] = $val;
        }
    }
    $args = urlencode(serialize($conditionArray));


} else {
    $args = 'true';
}


if (isset($conditionArray)) {

    foreach ($conditionArray as $key => $value) {
        $sparqlFilter .= '?service ?prop_' . $key . ' <' . $value . '> .';

        $valueLabel = explode('#', $value);
        $valueLabel = $valueLabel[1];

        $filterItemsHtml .= '<a href="?search=' . $searchTerm . '&arg=' . $args . '&action=' . urlencode($value) . '"">
				' . $valueLabel . ' <i class="material-icons">close</i>
			</a> ';
    }


}
?>

<script>
    $(function () {
        $('.mdl-collapse__content').each(function () {
            var content = $(this);
            content.css('margin-top', -content.height());
        });

        $(document.body).on('click', '.mdl-collapse__button', function () {
            $(this).parent().parent('.mdl-collapse').toggleClass('mdl-collapse--opened');
        });

        /*$("[data-badge='1']").hide();*/
    })
</script>


<?php

/*
 *
 * Query for Serach Results
 *
 */

$general = array(array("?Name", "?Beschreibung"), array("?Name", "?Description"));
$pos = 0;
if (LANG == 'en') {
    $pos = 1;
}

if (strpos($searchTerm, 'in:') !== false) {
    $ex = explode(':', $searchTerm, 2);
    $identifier = $ex[0];
    $searchTerm = urldecode($ex[1]);
    if (strpos($searchTerm, '#') !== false) {
        $ex = explode('#', $searchTerm, 2);
        $searchTerm = $ex[1];
    }

    $searchTermInput = $identifier . ':' . $searchTerm;

    $searchTermSparql = 'itcat:' . $searchTerm;

    $sparqlResult = '
        SELECT DISTINCT (?service AS ?uri) ?prefLabel ?abstract
        WHERE {
          ?service ?x ' . $searchTermSparql . '.
          ?service skos:prefLabel ?prefLabelLang;
          dcterms:abstract      ?abstractLang;
          FILTER (langMatches(lang(?prefLabelLang),"' . LANG . '"))
            FILTER (langMatches(lang(?abstractLang),"' . LANG . '"))
            BIND (str(?prefLabelLang) AS ?prefLabel)
            BIND (str(?abstractLang) AS ?abstract)
        }
	';


    $sparqlFilter = '
        SELECT  ?category (MIN(?prop) AS ?propX) (MIN(?catType) AS ?catTypeX) (MIN(?categoryPrefLabel) AS ?categoryPrefLabelX) (COUNT(?service) AS ?numService)
        WHERE {
            ?service ?prop  ?category.
            ?prop a owl:ObjectProperty.
            ?category a ?catType.

            ?category skos:prefLabel ?categoryPrefLabelLang.
            FILTER (langMatches(lang(?categoryPrefLabelLang),"' . LANG . '"))
            BIND (str(?categoryPrefLabelLang) AS ?categoryPrefLabel)
            {
              SELECT DISTINCT ?service
              WHERE{
                ?service ?x ' . $searchTermSparql . '.
                        ' . $sparqlFilter . '

                  ?service ?property ?valueLang
                  {
                      ?property a owl:AnnotationProperty.
                  }
                  UNION{
                      ?property a owl:DatatypeProperty.
                  }
             }
          }
        }
        GROUP BY ?category
        ORDER BY ?propX ?catTypeX';
} else {
    $sparqlResult = '
        SELECT DISTINCT (?service AS ?uri) (?prefLabel AS '.$general[$pos][0].') (?abstract AS '.$general[$pos][1].')
        WHERE {
            ?service a schema:Service.
            ?service ?prop ?valueLang
            {
              ?prop a owl:AnnotationProperty.
            }
            UNION{
              ?prop a owl:DatatypeProperty.
            }
            FILTER (
              (regex(lcase(str(?valueLang)), lcase("' . $searchTerm . '")))
            )

            ' . $sparqlFilter . '

            ?service skos:prefLabel ?prefLabelLang;
          dcterms:abstract      ?abstractLang;
          FILTER (langMatches(lang(?prefLabelLang),"' . LANG . '"))
            FILTER (langMatches(lang(?abstractLang),"' . LANG . '"))
            BIND (str(?prefLabelLang) AS ?prefLabel)
            BIND (str(?abstractLang) AS ?abstract)
          OPTIONAL{
            ?service itcat:inCategory ?category.
            GRAPH ?g {
                 ?category itcat_app:hasBgColor ?bgColor.
            }
          }
        }
        ';


    $sparqlFilter = '
        SELECT  ?category (MIN(?prop) AS ?propX) (MIN(?catType) AS ?catTypeX) (MIN(?categoryPrefLabel) AS ?categoryPrefLabelX) (COUNT(?service) AS ?numService)
        WHERE {
            ?service ?prop  ?category.
            ?prop a owl:ObjectProperty.
            ?category a ?catType.

            ?category skos:prefLabel ?categoryPrefLabelLang.
            FILTER (langMatches(lang(?categoryPrefLabelLang),"' . LANG . '"))
            BIND (str(?categoryPrefLabelLang) AS ?categoryPrefLabel)
            {
              SELECT DISTINCT ?service
              WHERE{
                ?service a schema:Service.
                        ' . $sparqlFilter . '

                  ?service ?property ?valueLang
                  {
                      ?property a owl:AnnotationProperty.
                  }
                  UNION{
                      ?property a owl:DatatypeProperty.
                  }
                  FILTER (
                      (regex(lcase(str(?valueLang)), lcase("' . $searchTerm . '")))
                  )
             }
          }
        }
        GROUP BY ?category
        ORDER BY ?propX ?catTypeX';
}


$result = $db->query($sparqlFilter);
if (!$result) {
    print $db->errno() . ": " . $db->error() . "\n";
    exit;
}


$navCategoryArray = array(
    #"http://schema.org/customer" => "Kunde",
    "http://schema.org/provider" => $provider_tab,
    #"http://th-brandenburg.de/ns/itcat#user" => "User",
    #"http://th-brandenburg.de/ns/itcat#hasCriticality" => "Kritikalität",
    #"http://th-brandenburg.de/ns/itcat#hasPriority" => "Priorität",
    #"http://th-brandenburg.de/ns/itcat#hasStage" => "LifeCycleStage",
    "http://th-brandenburg.de/ns/itcat#inCategory" => $category_tab,
    "http://th-brandenburg.de/ns/itcat#supporter" => $detail_contact,
    "http://th-brandenburg.de/ns/itcat#usableWith" => $detail_feature,
    #"http://schema.org/isRelatedTo" => "Verwandte Dienste",
    "http://th-brandenburg.de/ns/itcat#SubjectCategory" => $category_tab,
    "http://th-brandenburg.de/ns/itcat#CatalogCategory" => $catalog_tab,

);

$tmpCat = '';
$tmpProp = '';
$firstRun = true;
$navigationHtml = '';


while ($row = $result->fetch_array()) {

    if (array_key_exists($row['propX'], $navCategoryArray) || array_key_exists($row['catTypeX'], $navCategoryArray)) {
        if ($row['propX'] != $tmpProp) {
            $newCategory = true;
            $categoryName = $navCategoryArray[$row['propX']];
            $tmpProp = $row['propX'];
        }

        if ($row['catTypeX'] != $tmpCat && array_key_exists($row['catTypeX'], $navCategoryArray)) {
            $newCategory = true;
            $categoryName = $navCategoryArray[$row['catTypeX']];
            $tmpCat = $row['catTypeX'];

        }

        #category

        if ($newCategory == true) {
            if ($firstRun == false) {
                $navigationHtml .= '</div></div></div>';
            }

            $navigationHtml .= '<div class="navigation-card mdl-collapse mdl-cell mdl-cell--12-col mdl-color--white mdl-shadow--2dp mdl-card">';
            $navigationHtml .= '<div class="mdl-card__title">';
            $navigationHtml .= '<a class="mdl-collapse__button">';
            $navigationHtml .= '<h2 class="mdl-card__title-text">' . $categoryName . '</h2>';
            $navigationHtml .= '</a>';
            $navigationHtml .= '<i class="material-icons mdl-collapse__icon mdl-animation--default">expand_more</i>';
            $navigationHtml .= '</div>';


            $navigationHtml .= '<div class="mdl-collapse__content-wrapper">';
            $navigationHtml .= '<div class="mdl-collapse__content mdl-animation--default mdl-card__navigation ">';
            $newCategory = false;
        }

        $navigationHtml .= '
			<a href="?search=' . $searchTermInput . '&arg=' . $args . '&action=' . urlencode($row['category']) . '" class="mdl-badge mdl-badge-navigation" data-badge="' . $row['numService'] . '">
				' . $row['categoryPrefLabelX'] . '
			</a> ';

        $firstRun = false;
    }


}
$navigationHtml .= '</div></div></div>';


include('./template/categoryCard.php');


$result = $db->query($sparqlResult);
if (!$result) {
    print $db->errno() . ": " . $db->error() . "\n";
    exit;
}

?>


<div class="mdh-expandable-search mdl-cell mdl-cell--12-col mdl-color--white mdl-shadow--6dp">
    <i class="material-icons mdl-color-text--black">search</i>

    <form name="" action="" method="get">
        <input type="text" placeholder="<?php echo $searchTermInput ?>" size="1" name="search">
    </form>
</div>

<div class="mdl-cell mdl-cell--12-col">
    <div class="search-condintions">
        <?php echo $filterItemsHtml; ?>
    </div>
</div>


<div class="mdl-cell mdl-cell--4-col mdl-cell--8-col-tablet mdl-cell--4-col--phone">

    <?php
    echo $navigationHtml;
    ?>

</div>


<div class="mdl-cell mdl-cell--8-col mdl-cell--8-col-tablet mdl-cell--4-col--phone">
    <?php

    $urlParameters = '?input=';

    $result = $db->query($sparqlResult);
    if (!$result) {
        print $db->errno() . ": " . $db->error() . "\n";
        exit;
    }

    $fields = $result->field_array($result);

    print '<table class="mdl-data-table mdl-js-data-table mdl-text-table">';
    print "<thead>";
    print "<tr>";
    foreach ($fields as $field) {
        if ($field != 'uri') {
            print "<th class='mdl-data-table__cell--non-numeric mdl-color--grey-100'>$field</th>";
        }
    }
    print "</tr>";
    print "</thead>";
    print "<tbody>";
    while ($row = $result->fetch_array()) {
        echo '<tr onclick="document.location = \' ' . $urlParameters . urlencode($row['uri']) . '\';" style="cursor:pointer;">';
        foreach ($fields as $field) {
            if ($field != 'uri') {
                if (filter_var($row[$field], FILTER_VALIDATE_URL)) {
                    //$shortUri = $db->getNs($row[$field]);
                    $shortUri = '';
                    if ($shortUri == '') {

                        $value = '<a href="' . $row[$field] . '" target="_blank">' . $row[$field] . '</a>';
                    } else {
                        $value = $shortUri;
                    }
                } else {
                    $value = $row[$field];
                }
                print "<td class='mdl-data-table__cell--non-numeric'>$value</td>";
            }
        }
        print "</tr>";
    }
    print "</tbody>";
    print "</table>";

    /*


    $colorStrength = 300;
  while( $row = $result->fetch_array() ){
    if(!isset($row['bgColor'])){
      $row['bgColor'] = 'grey';
            $colorStrength = '';
    }
    showCardTemplate ($row['service'], $row['prefLabel'], $row['abstract'], '', $row['bgColor'], '?c=service&service=', 4, $colorStrength);

  }
*/
    ?>
</div>
