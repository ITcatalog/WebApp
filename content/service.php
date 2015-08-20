<?php
if(isset($_GET['service'])){
	$service = urldecode($_GET['service']);
}

include('./class/service.class.php');
$serviceController = new serviceController($db, $service);

?>

<div class="mdl-cell mdl-cell--12-col mdl-grid mdl-color--white mdl-shadow--2dp">

  <ul>
    <li class="mdl-button mdl-js-button <?php if(!isset($_GET['action'])){echo 'mdl-button--raised';}?>"><a href="?c=service&service=<?php echo urlencode($service)?>">Steckbrief</a></li>
    <?php $result = $serviceController->getObjectProperty('schema:isRelatedTo'); if($result['num'] > 0){?>
        <li class="mdl-button mdl-js-button <?php if(isset($_GET['action']) && $_GET['action'] == 'map'){echo 'mdl-button--raised';}?>"><a href="?c=service&action=map&service=<?php echo urlencode($service)?>">Landkarte</a></li>
    <?php } ?>
    <li class="mdl-button mdl-js-button  mdl-badge badge-header <?php if(isset($_GET['action']) && $_GET['action'] == 'docs'){echo 'mdl-button--raised';}?>" data-badge="10"><a href="?c=service&action=docs&service=<?php echo urlencode($service)?>">Dokumente</a></li>
  </ul>

</div>


<?php
if(isset($_GET['action']) && $_GET['action'] == 'map'){

	include ('content/serviceMap.php');

}
elseif(isset($_GET['action']) && $_GET['action'] == 'docs'){
	include ('content/serviceDocs.php');
}
else{
 ?>


<div class="mdl-cell mdl-cell--6-col mdl-cell--12-col-phone mdl-cell--12-col-tablet mdl-grid">

  <div class="service-profile-cat mdl-cell mdl-cell--12-col mdl-color--white mdl-shadow--2dp mdl-card">

    <div class="mdl-card__title" style="display:block;">
      <h2 class="mdl-card__title-text"><?php echo $serviceController->getLiteralProperty ('dcterms:title', 'value'); ?></h2>

			<?php
			$sparql = '
			SELECT ?subjectCategory ?prefLabel ?bgColor
			WHERE {
				<'.$service.'> itcat:inCategory ?subjectCategory.
			  ?subjectCategory a itcat:SubjectCategory;
			  skos:prefLabel ?prefLabelLang;
			  FILTER (langMatches(lang(?prefLabelLang),"'.LANG.'"))
				BIND (str(?prefLabelLang) AS ?prefLabel)
			  GRAPH ?g {
			    ?subjectCategory itcat_app:hasBgColor ?bgColor
			  }
			}
			ORDER BY ?prefLabel
			';

			$result = $db->query( $sparql );
			if( !$result ) { print $db->errno() . ": " . $db->error(). "\n"; exit; }

			if($result->num_rows() > 0){
				echo '<ul class="category-badge">';
				while( $row = $result->fetch_array() ){
					echo '<li class="mdl-color--'.$row['bgColor'].'-300"><a href="?c=category&cat='.urlencode($row['subjectCategory']).'" class="">'.$row['prefLabel'].'</a></li>';
				}
				echo '</ul>';
			}
			?>
    </div>

    <div class="mdl-card__supporting-text">
			<?php
				#$serviceController->showLiteralItem('dcterms:title');

				$serviceController->showLiteralItem('dcterms:abstract');

				$serviceController->showLiteralItem('dcterms:description');
			?>
        <div class="service-attribute">
          <div class="service-attribute__title">
							<?php echo $serviceController->getLiteralProperty('schema:url', 'prefLabel'); ?>
					</div>
          <div class="service-attribute__value">
            <a href="<?php echo $serviceController->getLiteralProperty('schema:url', 'value'); ?>" target="_blank"><?php echo $serviceController->getLiteralProperty('schema:url', 'value'); ?></a>
          </div>
        </div>
    </div>
  </div>
  <div class="service-profile-cat mdl-cell mdl-cell--12-col mdl-color--white mdl-shadow--2dp mdl-card">

    <div class="mdl-card__title">
      <h2 class="mdl-card__title-text">Ansprechpartner</h2>
    </div>

    <div class="mdl-card__supporting-text">
      <div class="service-attribute">
        <div class="service-attribute__title">Verantwortlich</div>
        <div class="service-attribute__value">

					<?php

						$result = $serviceController->getObjectProperty('itcat:supporter');
						while($row = $result['result']->fetch_array()){
							echo '<a href="?item='.urlencode($row['uri']).'">'.$row['prefLabel'].'</a> <br />';
						}

					?>

        </div>
      </div>

      <div class="service-attribute">
        <div class="service-attribute__title">Anbieter</div>
        <div class="service-attribute__value">
          <?php

						$result = $serviceController->getObjectProperty('schema:provider');
						while($row = $result['result']->fetch_array()){
							echo '<a href="?item='.urlencode($row['uri']).'">'.$row['prefLabel'].'</a> <br />';
						}

          ?>
        </div>
      </div>

			<div class="service-attribute">
				<div class="service-attribute__title">Kunde</div>
				<div class="service-attribute__value">
					<?php

						$result = $serviceController->getObjectProperty('schema:customer');
						while($row = $result['result']->fetch_array()){
							echo '<a href="?item='.urlencode($row['uri']).'">'.$row['prefLabel'].'</a> <br />';
						}

					?>
				</div>
			</div>
    </div>
  </div>
</div>

<div class="mdl-cell mdl-cell--6-col mdl-cell--12-col-phone mdl-cell--12-col-tablet mdl-grid">

  <div class="service-profile-cat mdl-cell mdl-cell--12-col mdl-color--white mdl-shadow--2dp mdl-card">
    <div class="mdl-card__title">

      <h2 class="mdl-card__title-text">
        Service-Merkmale
      </h2>
    </div>
    <div class="mdl-card__supporting-text">
      <div class="service-attribute">
        <div class="service-attribute__title">Verfügbar für</div>
        <div class="service-attribute__value">
					<?php

						$result = $serviceController->getObjectProperty('itcat:user');
						while($row = $result['result']->fetch_array()){
							echo '<a href="?item='.urlencode($row['uri']).'">'.$row['prefLabel'].'</a> <br />';
						}

          ?>
        </div>
      </div>

      <div class="service-attribute">
        <div class="service-attribute__title">Unterstütze Geräte</div>
        <div class="service-attribute__value">
					<?php

						$result = $serviceController->getObjectProperty('itcat:usableWith');
						while($row = $result['result']->fetch_array()){
							echo '<a href="?item='.urlencode($row['uri']).'">'.$row['prefLabel'].'</a> <br />';
						}
          ?>
        </div>
      </div>
    </div>
  </div>

  <div class="service-profile-cat mdl-cell mdl-cell--12-col mdl-color--white mdl-shadow--2dp mdl-card">
    <div class="mdl-card__title">

      <h2 class="mdl-card__title-text">

      </h2>
      <button class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent" style="width:100%">
        Hilfe Anfordern
      </button>
    </div>
    <div class="mdl-card__supporting-text">

    </div>
  </div>

<?php
$result = $serviceController->getObjectProperty('schema:isRelatedTo');
if($result['num'] > 0){

?>

  <div class="service-profile-cat mdl-cell mdl-cell--12-col mdl-color--white mdl-shadow--2dp mdl-card">
    <div class="mdl-card__title">

      <h2 class="mdl-card__title-text">
        Relevante Dienste
      </h2>
    </div>
    <div class="mdl-card__supporting-text">
			<?php
				while($row = $result['result']->fetch_array()){
					echo '<a class="mdl-button mdl-js-button mdl-button--colored" href="?c=service&service='.urlencode($row['uri']).'">' . $row['prefLabel'] . '</a><br />';
				}
			?>
    </div>
  </div>
  <?php } ?>

	<div class="service-profile-cat mdl-cell mdl-cell--12-col mdl-color--white mdl-shadow--2dp mdl-card">
		<div class="mdl-card__title">
			<h2 class="mdl-card__title-text">Monitoring</h2>
		</div>


			<div class="mdl-card__supporting-text">
				<div class="service-attribute">
					<div class="service-attribute__title">itcat:hasCriticality</div>

					<div class="service-attribute__value">
					<?php

						$result = $serviceController->getObjectProperty('itcat:hasCriticality');
						while($row = $result['result']->fetch_array()){
							echo '<a href="?item='.urlencode($row['uri']).'">'.$row['prefLabel'].'</a> <br />';
						}

					?>
					</div>

				</div>
				<div class="service-attribute">
					<div class="service-attribute__title">itcat:hasPriority</div>

					<div class="service-attribute__value">
					<?php
						$result = $serviceController->getObjectProperty('itcat:hasPriority');
						while($row = $result['result']->fetch_array()){
							echo '<a href="?item='.urlencode($row['uri']).'">'.$row['prefLabel'].'</a> <br />';
						}
					?>
					</div>

				</div>

			</div>
	</div>
</div>

<?php } ?>
