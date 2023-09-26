<?php
if (isset($_GET['service'])) {
    $service = urldecode($_GET['service']);
}

include('./class/service.class.php');
$serviceController = new serviceController($db, $service);

$sparql = '
    SELECT ?detail_tab1 ?detail_tab2 ?detail_relevantService ?detail_contact ?contact1 ?contact2 ?contact3 ?detail_contactRequest ?detail_feature ?feature1 ?feature2 ?detail_rating ?rating1 ?rating2
        {
          GRAPH ?g {
            itcat_app:detail_tab1	itcat_app:name ?detail_tab1 .
			itcat_app:detail_tab2	itcat_app:name ?detail_tab2 .
			itcat_app:detail_relevantService	itcat_app:name ?detail_relevantService .
			itcat_app:detail_contact 	itcat_app:name ?detail_contact ;
					itcat_app:subCategorie1 ?contact1 ;
					itcat_app:subCategorie2 ?contact2 ;
					itcat_app:subCategorie3 ?contact3 .
    		itcat_app:detail_contactRequest itcat_app:name ?detail_contactRequest .
    		itcat_app:detail_feature 	itcat_app:name ?detail_feature ;
                                      itcat_app:subCategorie1 ?feature1 ;
                                      itcat_app:subCategorie2 ?feature2 .
    		itcat_app:detail_rating	itcat_app:name ?detail_rating ;
                                      itcat_app:subCategorie1 ?rating1 ;
                                      itcat_app:subCategorie2 ?rating2 .
    
            FILTER (langMatches(lang(?detail_tab1),"' . LANG . '"))
            FILTER (langMatches(lang(?detail_tab2),"' . LANG . '"))
            FILTER (langMatches(lang(?detail_relevantService),"' . LANG . '"))
            FILTER (langMatches(lang(?detail_contact),"' . LANG . '"))
            FILTER (langMatches(lang(?contact1),"' . LANG . '"))
            FILTER (langMatches(lang(?contact2),"' . LANG . '"))
            FILTER (langMatches(lang(?contact3),"' . LANG . '"))
            FILTER (langMatches(lang(?detail_contactRequest),"' . LANG . '"))
            FILTER (langMatches(lang(?detail_feature),"' . LANG . '"))		
            FILTER (langMatches(lang(?feature1),"' . LANG . '"))
            FILTER (langMatches(lang(?feature2),"' . LANG . '"))
            FILTER (langMatches(lang(?detail_rating),"' . LANG . '"))
            FILTER (langMatches(lang(?rating1),"' . LANG . '"))
            FILTER (langMatches(lang(?rating2),"' . LANG . '"))
          }
        }';

$result = $db->query($sparql);
if (!$result) {
    print $db->errno() . ": " . $db->error() . "\n";
    exit;
}

$detail_tab1 = '';
$detail_tab2 = '';
$detail_relevantService = '';
$detail_contact = '';
$contact1 = '';
$contact2 = '';
$contact3 = '';
$detail_contactRequest = '';
$detail_feature = '';
$feature1= '';
$feature2 = '';
$detail_rating = '';
$rating1 = '';
$rating2 = '';

if ($result->num_rows() > 0) {
    while ($row = $result->fetch_array()) {
        $detail_tab1 = $row['detail_tab1'];
        $detail_tab2 = $row['detail_tab2'];
        $detail_relevantService = $row['detail_relevantService'];
        $detail_contact = $row['detail_contact'];
        $contact1 = $row['contact1'];
        $contact2 = $row['contact2'];
        $contact3 = $row['contact3'];
        $detail_contactRequest = $row['detail_contactRequest'];
        $detail_feature = $row['detail_feature'];
        $feature1 = $row['feature1'];
        $feature2 = $row['feature2'];
        $detail_rating = $row['detail_rating'];
        $rating1 = $row['rating1'];
        $rating2 = $row['rating2'];
    }
}

?>

<div class="mdl-cell mdl-cell--12-col mdl-grid">

    <div class="mdl-tabs">
        <div class="mdl-tabs__tab-bar">
            <a href="?c=service&service=<?php echo urlencode($service) ?>"
               class="mdl-tabs__tab <?php if (!isset($_GET['action'])) {
                   echo 'homeTabBarActive';
               } ?>"><?php echo $detail_tab1 ?></a>
            <?php
            $result = $db->query('SELECT (COUNT(?document) AS ?numberOfDocuments) WHERE { <' . $service . '> foaf:page ?document.   FILTER (?document != "")}');
            if (!$result) {
                print $db->errno() . ": " . $db->error() . "\n";
                exit;
            }
            $row = $result->fetch_array();
            if ($row['numberOfDocuments'] > 0) {
                ?>
                <a href="?c=service&action=docs&service=<?php echo urlencode($service) ?>"
                   class="mdl-tabs__tab <?php if ($_GET['action'] == 'docs') {
                       echo 'homeTabBarActive';
                   } ?>"><?php echo $detail_tab2 ?></a>
            <?php } ?>
            <?php /* $result = $serviceController->getObjectProperty('(schema:isRelatedTo | ^schema:isRelatedTo)');
            if ($result['num'] > 0) { ?>
                <a href="?c=service&action=map&service=<?php echo urlencode($service) ?>"
                   class="mdl-tabs__tab <?php if ($_GET['action'] == 'map') {
                       echo 'homeTabBarActive';
                   } ?>">Landkarte</a>
            <?php } */ ?>
        </div>
    </div>

</div>


<?php
if (isset($_GET['action']) && $_GET['action'] == 'map') {

    include('content/serviceMap.php');

} elseif (isset($_GET['action']) && $_GET['action'] == 'docs') {
    include('content/serviceDocs.php');
} else {

    ?>


    <div class="mdl-cell mdl-cell--6-col mdl-cell--12-col-phone mdl-cell--12-col-tablet mdl-grid">

        <div class="service-profile-cat mdl-cell mdl-cell--12-col mdl-color--white mdl-shadow--2dp mdl-card">

            <div class="mdl-card__title" style="display:block;">
                <h2 class="mdl-card__title-text">
                    <?php echo $serviceController->getLiteralProperty('skos:prefLabel', 'value'); ?>
                </h2>
                <?php

                /* Get categories */

                $sparql = '
                    SELECT ?subjectCategory ?prefLabel ?bgColor
                    WHERE {
                        <' . $service . '> itcat:inCategory ?subjectCategory.
                      ?subjectCategory a itcat:SubjectCategory;
                      skos:prefLabel ?prefLabelLang;
                      FILTER (langMatches(lang(?prefLabelLang),"' . LANG . '"))
                        BIND (str(?prefLabelLang) AS ?prefLabel)
                      GRAPH ?g {
                        ?subjectCategory itcat_app:hasBgColor ?bgColor
                      }
                    }
                    ORDER BY ?prefLabel
                    ';

                $result = $db->query($sparql);
                if (!$result) {
                    print $db->errno() . ": " . $db->error() . "\n";
                    exit;
                }

                if ($result->num_rows() > 0) {
                    echo '<ul class="category-badge">';
                    while ($row = $result->fetch_array()) {
                        echo '<li class="mdl-color--' . $row['bgColor'] . '-300"><a href="?c=category&cat=' . urlencode($row['subjectCategory']) . '" class="">' . $row['prefLabel'] . '</a></li>';
                    }
                    echo '</ul>';
                }
                ?>
            </div>


            <?php
            $result = $serviceController->getObjectProperty('itcat:hasStage');
            if ($result['num'] == 1) {
                $row = $result['result']->fetch_array();

                switch ($row['uri']) {
                    case 'http://th-brandenburg.de/ns/itcat#Planning':
                        $textColor = 'yellow';
                        break;

                    case 'http://th-brandenburg.de/ns/itcat#Implementation':
                        $textColor = 'lime';
                        break;

                    case 'http://th-brandenburg.de/ns/itcat#Operation':
                        $textColor = 'green';
                        break;

                    case 'http://th-brandenburg.de/ns/itcat#InRelief':
                        $textColor = 'deep-orange';
                        break;

                    case 'http://th-brandenburg.de/ns/itcat#Off':
                        $textColor = 'red';
                        break;

                    default:
                        $textColor = 'black';
                }

                $sparqlLifeCycleStage = '
                
                    SELECT ?lifeCycleStage ?prefLabel ?definition
                    WHERE {
                        ?lifeCycleStage a itcat:LifeCycleStage;
                            skos:prefLabel ?prefLabel_lang ;
                            skos:definition ?definition_lang.
                        FILTER (langMatches(lang(?prefLabel_lang),"' . LANG . '"))
                        BIND (str(?prefLabel_lang) AS ?prefLabel)
                        FILTER (langMatches(lang(?definition_lang),"' . LANG . '"))
                        BIND (str(?definition_lang) AS ?definition)                                            
                    }';

                $resultLifeCycleStage = $db->query($sparqlLifeCycleStage);
                if (!$resultLifeCycleStage) {
                    print $db->errno() . ": " . $db->error() . "\n";
                    exit;
                }

                echo '<div class="lifeCycleTimeline mdl-card__menu">';

                while ($LifeCycleStageRow = $resultLifeCycleStage->fetch_array()) {
                    echo '<a href="?search=in:' . urlencode($LifeCycleStageRow['lifeCycleStage']) . '" class="dot ' . ($LifeCycleStageRow['lifeCycleStage'] == $row['uri'] ? 'complete' : '') . '" id="' . $LifeCycleStageRow['lifeCycleStage'] . '"></a>';
                    echo '<div class="mdl-tooltip" for="' . $LifeCycleStageRow['lifeCycleStage'] .  '" id="' . $LifeCycleStageRow['lifeCycleStage'] . '#tooltip'. '">' . $LifeCycleStageRow['prefLabel'] . '</div>';
                }
                echo '</div>';
            }
            ?>


            <div class="mdl-card__supporting-text">
                <?php
                $serviceController->showLiteralItem('dcterms:title');

                $serviceController->showLiteralItem('dcterms:abstract');

                $serviceController->showLiteralItem('dcterms:description');
                ?>
                <?php
                if ($serviceController->checkForValuegetLiteralProperty('schema:url') == true) {

                    ?>
                    <div class="service-attribute">
                        <div class="service-attribute__title">
                            <?php echo $serviceController->getLiteralProperty('schema:url', 'prefLabel'); ?>
                        </div>
                        <div class="service-attribute__value">
                            <a href="<?php echo $serviceController->getLiteralProperty('schema:url', 'value'); ?>"
                               target="_blank"><?php echo $serviceController->getLiteralProperty('schema:url', 'value'); ?></a>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>

        <?php
        $result = $serviceController->getObjectProperty('(schema:isRelatedTo | ^schema:isRelatedTo)');
        if ($result['num'] > 0) {

            ?>

            <div class="service-profile-cat mdl-cell mdl-cell--12-col mdl-color--white mdl-shadow--2dp mdl-card">
                <div class="mdl-card__title">

                    <h2 class="mdl-card__title-text">
                        <?php echo $detail_relevantService ?>
                    </h2>
                </div>
                <div class="mdl-card__supporting-text related-services">
                    <?php
                    while ($row = $result['result']->fetch_array()) {
                        echo '<a class="" style="text-align:left; min-width:8px;" href="?c=service&service=' . urlencode($row['uri']) . '">' . $row['prefLabel'] . '</a>';
                    }
                    ?>
                </div>
            </div>
        <?php } ?>

    </div>

    <div class="mdl-cell mdl-cell--6-col mdl-cell--12-col-phone mdl-cell--12-col-tablet mdl-grid">

        <div class="service-profile-cat mdl-cell mdl-cell--12-col mdl-color--white mdl-shadow--2dp mdl-card">

            <div class="mdl-card__title">
                <h2 class="mdl-card__title-text"><?php echo $detail_contact ?></h2>
            </div>

            <div class="mdl-card__supporting-text">

                <?php
                $serviceController->showObjectProperty($contact1, 'itcat:supporter');

                $serviceController->showObjectProperty($contact2, 'schema:provider');

                $serviceController->showObjectProperty($contact3, 'schema:customer');
                ?>

                <a class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent"
                        style="width:100%" href="https://thb.freshservice.com/support/tickets/new" target="_blank">
                    <?php echo $detail_contactRequest ?>
                </a>
            </div>

            <?php
            $serviceController->showObjectPropertyHelp($detail_contact, array(
                    array('label' => $contact1,'value' => 'itcat:supporter',),
                    array('label' => $contact2,'value' => 'schema:provider',),
                    array('label' => $contact3, 'value' => 'schema:customer',)
                ));
            ?>
        </div>

        <div class="service-profile-cat mdl-cell mdl-cell--12-col mdl-color--white mdl-shadow--2dp mdl-card">
            <div class="mdl-card__title">

                <h2 class="mdl-card__title-text">
                    <?php echo $detail_feature ?>
                </h2>
            </div>
            <div class="mdl-card__supporting-text">

                <?php

                $serviceController->showObjectProperty($feature1, 'itcat:user');

                $serviceController->showObjectProperty($feature2, 'itcat:usableWith');

                ?>

            </div>
            <?php
            $serviceController->showObjectPropertyHelp($detail_feature, array(
                array('label' => $feature1,'value' => 'itcat:user',),
                array('label' => $feature2,'value' => 'itcat:usableWith',)
            ));
            ?>
        </div>

        <div class="service-profile-cat mdl-cell mdl-cell--12-col mdl-color--white mdl-shadow--2dp mdl-card">
            <div class="mdl-card__title">
                <h2 class="mdl-card__title-text"><?php echo $detail_rating ?></h2>
            </div>

            <div class="mdl-card__supporting-text">
                <?php

                $serviceController->showObjectProperty($rating1, 'itcat:hasCriticality');

                $serviceController->showObjectProperty($rating2, 'itcat:hasPriority');

                ?>
            </div>
            <?php
                $serviceController->showObjectPropertyHelp($detail_rating, array(
                    array('label' => $rating1,'value' => 'itcat:hasCriticality',),
                    array('label' => $rating2,'value' => 'itcat:hasPriority',)
                ));
            ?>


        </div>
    </div>

<?php } ?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script>
    $('.dot').on('click', function(e) {
        $(this).next().attr("class", "mdl-tooltip");
    });
</script>
