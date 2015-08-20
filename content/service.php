<?php
if(isset($_GET['service'])){
	$service = urldecode($_GET['service']);
}

include('./class/service.class.php');
$serviceController = new serviceController($db, $service);

?>

<div class="mdl-cell mdl-cell--12-col mdl-grid">

  <div class="mdl-tabs">
    <div class="mdl-tabs__tab-bar">
			<a href="?c=service&service=<?php echo urlencode($service)?>" class="mdl-tabs__tab <?php if(!isset($_GET['action'])){echo 'homeTabBarActive';}?>">Steckbrief</a>
			<?php
	    $result = $db->query('SELECT (COUNT(?document) AS ?numberOfDocuments) WHERE { <'.$service.'> foaf:page ?document.   FILTER (?document != "")}' );
	    if( !$result ) { print $db->errno() . ": " . $db->error(). "\n"; exit; }
	    $row = $result->fetch_array();
			if($row['numberOfDocuments'] > 0){?>
			<a href="?c=service&action=docs&service=<?php echo urlencode($service)?>" class="mdl-tabs__tab <?php if($_GET['action'] == 'docs'){echo 'homeTabBarActive';}?>">Dokumente</a>
			<?php } ?>
			<?php $result = $serviceController->getObjectProperty('schema:isRelatedTo'); if($result['num'] > 0){?>
			<a href="?c=service&action=map&service=<?php echo urlencode($service)?>" class="mdl-tabs__tab <?php if($_GET['action'] == 'map'){echo 'homeTabBarActive';}?>">Landkarte</a>
			<?php } ?>
    </div>
  </div>

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
      <h2 class="mdl-card__title-text"><?php echo $serviceController->getLiteralProperty ('skos:prefLabel', 'value'); ?></h2>
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
				$serviceController->showLiteralItem('dcterms:title');

				$serviceController->showLiteralItem('dcterms:abstract');

				$serviceController->showLiteralItem('dcterms:description');
			?>
			<?php
			if($serviceController->checkForValuegetLiteralProperty('schema:url') == true){

			 ?>
        <div class="service-attribute">
          <div class="service-attribute__title">
							<?php echo $serviceController->getLiteralProperty('schema:url', 'prefLabel'); ?>
					</div>
          <div class="service-attribute__value">
            <a href="<?php echo $serviceController->getLiteralProperty('schema:url', 'value'); ?>" target="_blank"><?php echo $serviceController->getLiteralProperty('schema:url', 'value'); ?></a>
          </div>
        </div>
				<?php } ?>
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

</div>

<div class="mdl-cell mdl-cell--6-col mdl-cell--12-col-phone mdl-cell--12-col-tablet mdl-grid">

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
							echo '<a href="?search=in:'.urlencode($row['uri']).'">'.$row['prefLabel'].'</a> <br />';
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
							echo '<a href="?search=in:'.urlencode($row['uri']).'">'.$row['prefLabel'].'</a> <br />';
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
							echo '<a href="?search=in:'.urlencode($row['uri']).'">'.$row['prefLabel'].'</a> <br />';
						}

					?>
				</div>
			</div>
			<button class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent" style="width:100%">
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
      <div class="service-attribute">
        <div class="service-attribute__title">Verfügbar für</div>
        <div class="service-attribute__value">
					<?php

						$result = $serviceController->getObjectProperty('itcat:user');
						while($row = $result['result']->fetch_array()){
							echo '<a href="?search=in:'.urlencode($row['uri']).'">'.$row['prefLabel'].'</a> <br />';
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
							echo '<a href="?search=in:'.urlencode($row['uri']).'">'.$row['prefLabel'].'</a> <br />';
						}
          ?>
        </div>
      </div>
    </div>
  </div>

	<div class="service-profile-cat mdl-cell mdl-cell--12-col mdl-color--white mdl-shadow--2dp mdl-card">
		<div class="mdl-card__title">
			<h2 class="mdl-card__title-text">Service-Bewertung</h2>
		</div>


			<div class="mdl-card__supporting-text">
				<div class="service-attribute">
					<div class="service-attribute__title">Kritikalität </div>

					<div class="service-attribute__value">
					<?php

						$result = $serviceController->getObjectProperty('itcat:hasCriticality');
						while($row = $result['result']->fetch_array()){
							echo '<a href="?search=in:'.urlencode($row['uri']).'">'.$row['prefLabel'].'</a> <br />';
						}

					?>
					</div>

				</div>
				<div class="service-attribute">
					<div class="service-attribute__title">Priorität</div>

					<div class="service-attribute__value">
					<?php
						$result = $serviceController->getObjectProperty('itcat:hasPriority');
						while($row = $result['result']->fetch_array()){
							echo '<a href="?search=in:'.urlencode($row['uri']).'">'.$row['prefLabel'].'</a> <br />';
						}
					?>
					</div>

				</div>

			</div>
	</div>
</div>

<?php } ?>
