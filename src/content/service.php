<?php
if (isset($_GET['service'])) {
    $service = urldecode($_GET['service']);
}

include('./class/service.class.php');
$serviceController = new serviceController($db, $service);

?>

<div class="mdl-cell mdl-cell--12-col mdl-grid">

    <div class="mdl-tabs">
        <div class="mdl-tabs__tab-bar">
            <a href="?c=service&service=<?php echo urlencode($service) ?>"
               class="mdl-tabs__tab <?php if (!isset($_GET['action'])) {
                   echo 'homeTabBarActive';
               } ?>">Steckbrief</a>
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
                   } ?>">Dokumente</a>
            <?php } ?>
            <?php $result = $serviceController->getObjectProperty('(schema:isRelatedTo | ^schema:isRelatedTo)');
            if ($result['num'] > 0) { ?>
                <a href="?c=service&action=map&service=<?php echo urlencode($service) ?>"
                   class="mdl-tabs__tab <?php if ($_GET['action'] == 'map') {
                       echo 'homeTabBarActive';
                   } ?>">Landkarte</a>
            <?php } ?>
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
                    SELECT ?lifeCycleStage ?prefLabel ?comment
                    WHERE {
                        ?lifeCycleStage a itcat:LifeCycleStage;
                            skos:prefLabel ?prefLabel_lang;
                            rdfs:comment ?comment_lang.

                        FILTER (langMatches(lang(?prefLabel_lang),"' . LANG . '"))
                        BIND (str(?prefLabel_lang) AS ?prefLabel)
                        FILTER (langMatches(lang(?comment_lang),"' . LANG . '"))
                        BIND (str(?comment_lang) AS ?comment)
                    }';

                $resultLifeCycleStage = $db->query($sparqlLifeCycleStage);
                if (!$resultLifeCycleStage) {
                    print $db->errno() . ": " . $db->error() . "\n";
                    exit;
                }

                echo '<div class="lifeCycleTimeline mdl-card__menu">';

                while ($LifeCycleStageRow = $resultLifeCycleStage->fetch_array()) {
                    echo '<a href="?search=in:' . urlencode($row['uri']) . '" class="dot ' . ($LifeCycleStageRow['lifeCycleStage'] == $row['uri'] ? 'complete' : '') . '" id="' . $LifeCycleStageRow['lifeCycleStage'] . '"></a>';
                    echo '<div class="mdl-tooltip" for="' . $LifeCycleStageRow['lifeCycleStage'] . '">' . $LifeCycleStageRow['prefLabel'] . '</div>';
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
                        Relevante Dienste
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
                <h2 class="mdl-card__title-text">Ansprechpartner</h2>
            </div>

            <div class="mdl-card__supporting-text">

                <?php
                $serviceController->showObjectProperty('Verantwortlich', 'itcat:supporter');

                $serviceController->showObjectProperty('Anbieter', 'schema:provider');

                $serviceController->showObjectProperty('Kunde', 'schema:customer');

                ?>

                <button class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent"
                        style="width:100%">
                    Hilfe Anfordern
                </button>
            </div>
        </div>

        <div class="service-profile-cat mdl-cell mdl-cell--12-col mdl-color--white mdl-shadow--2dp mdl-card">
            <div class="mdl-card__title">

                <h2 class="mdl-card__title-text">
                    Service-Merkmale
                </h2>
            </div>
            <div class="mdl-card__supporting-text">

                <?php

                $serviceController->showObjectProperty('Verfügbar für', 'itcat:user');

                $serviceController->showObjectProperty('Unterstützte Geräte', 'itcat:usableWith');

                ?>

            </div>
        </div>

        <div class="service-profile-cat mdl-cell mdl-cell--12-col mdl-color--white mdl-shadow--2dp mdl-card">
            <div class="mdl-card__title">
                <h2 class="mdl-card__title-text">Service-Bewertung</h2>
            </div>


            <div class="mdl-card__supporting-text">

                <?php

                $serviceController->showObjectProperty('Kritikalität', 'itcat:hasCriticality');

                $serviceController->showObjectProperty('Priorität', 'itcat:hasPriority');

                ?>


            </div>
        </div>
    </div>

<?php } ?>
