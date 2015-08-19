<?php
if(isset($_GET['service'])){
	$service = urldecode($_GET['service']);

	$sparql = '
  SELECT *
	WHERE {
		  <'.$service.'> rdf:type schema:Service;
    	skos:prefLabel ?prefLabel;
	    dcterms:subject ?subject;
    	dcterms:description ?description;
    	schema:url ?url;
	}
	';

	$result = $db->query( $sparql );
	if( !$result ) { print $db->errno() . ": " . $db->error(). "\n"; exit; }

	$serviceLiteral = $result->fetch_array();
}

function getProperties ($property, $db, $service){
  $sparql = '
  SELECT *
  	WHERE {
  		<'.$service.'> rdf:type schema:Service;
      '.$property.' ?item.
      OPTIONAL{
        ?item skos:prefLabel ?prefLabel.
      }
      OPTIONAL{
        ?item rdfs:label ?label.
      }
  	}
  ';


  #echo htmlspecialchars($sparql);

  $result = $db->query( $sparql );
  if( !$result ) { print $db->errno() . ": " . $db->error(). "\n"; exit; }


  $return = array(
    'num' => $result->num_rows(),
    'result' => $result,
  );

  return $return;

}



?>

<div class="mdl-cell mdl-cell--12-col mdl-grid mdl-color--white mdl-shadow--2dp">

  <ul>
    <li class="mdl-button mdl-js-button <?php if(!isset($_GET['action'])){echo 'mdl-button--raised';}?>"><a href="?c=service&service=<?php echo urlencode($service)?>">Steckbrief</a></li>
    <?php $result = getProperties('schema:isRelatedTo', $db, $service); if($result['num'] > 0){?>
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


<div class="mdl-cell mdl-cell--6-col mdl-grid">

  <div class="service-profile-cat mdl-cell mdl-cell--12-col mdl-color--white mdl-shadow--2dp mdl-card">

    <div class="mdl-card__title" style="display:block;">
      <h2 class="mdl-card__title-text"><?php echo $serviceLiteral['prefLabel']; ?></h2>

			<?php
			$sparql = '
			SELECT *
		  FROM NAMED <'.$dataGraphs['ApplicationGraph'].'>
		  WHERE {
		    ?cat itcat:hasITService <'.$service.'>;
				skos:prefLabel ?catLabel.
		  	GRAPH ?g {
		    	?cat itcat_app:hasBGColor ?bgColor.
		    }
		  }
			';

			$result = $db->query( $sparql );
			if( !$result ) { print $db->errno() . ": " . $db->error(). "\n"; exit; }

			if($result->num_rows() > 0){
				echo '<ul class="category-badge">';
				while( $row = $result->fetch_array() ){
					echo '<li class="mdl-color--'.$row['bgColor'].'-300"><a href="?c=category&cat='.urlencode($row['cat']).'" class="">'.$row['catLabel'].'</a></li>';
				}
				echo '</ul>';
			}

			?>
    </div>

    <div class="mdl-card__supporting-text">
        <div class="service-attribute">
          <div class="service-attribute__title">Subject</div>
          <div class="service-attribute__value">
            <?php echo $serviceLiteral['subject']; ?>
          </div>
        </div>

        <div class="service-attribute">
          <div class="service-attribute__title">Servicebeschreibung</div>
          <div class="service-attribute__value">
            <?php echo $serviceLiteral['description']; ?>
          </div>
        </div>
        <div class="service-attribute">
          <div class="service-attribute__title">URL</div>
          <div class="service-attribute__value">
            <a href="<?php echo $serviceLiteral['url']; ?>" target="_blank"><?php echo $serviceLiteral['url']; ?></a>
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

        </div>
      </div>

      <div class="service-attribute">
        <div class="service-attribute__title">Anbieter</div>
        <div class="service-attribute__value">
          <?php

            $result = getProperties('schema:provider', $db, $service);
            while($row = $result['result']->fetch_array()){
              echo $row['item'] .' <br />';
            }

          ?>
        </div>
      </div>
    </div>
  </div>

<?php if(isset($serviceLiteral['status'])){?>
  <div class="service-profile-cat mdl-cell mdl-cell--12-col mdl-color--white mdl-shadow--2dp mdl-card">
    <div class="mdl-card__title">
      <h2 class="mdl-card__title-text">Monitoring</h2>
    </div>

    <div class="mdl-card__supporting-text">
        <div class="service-attribute">
          <div class="service-attribute__title">Status</div>
          <div class="service-attribute__value">
            <?php echo $serviceLiteral['status']; ?>
          </div>
        </div>
    </div>
  </div>
  <?php } ?>

</div>




<div class="mdl-cell mdl-cell--6-col mdl-grid">

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
            $result = getProperties('schema:user', $db, $service);
            while($row = $result['result']->fetch_array()){
              echo $row['item'] . '<br />';
            }
          ?>
        </div>
      </div>

      <div class="service-attribute">
        <div class="service-attribute__title">Unterstütze Geräte</div>
        <div class="service-attribute__value">
          <?php
            $result = getProperties('usdlagreement:refersTo', $db, $service);
            while($row = $result['result']->fetch_array()){
              echo $row['item'] . '<br />';
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
$result = getProperties('schema:isRelatedTo', $db, $service);
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
          echo '<a class="mdl-button mdl-js-button mdl-button--colored" href="?c=service&service='.urlencode($row['item']).'">' . $row['prefLabel'] . '</a><br />';
        }

      ?>
    </div>
  </div>
  <?php } ?>

</div>

<?php } ?>
