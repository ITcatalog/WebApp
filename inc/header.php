<?php

$pageName = '';
$pageTitle = '';
$pageColor = '';
$currentPath = '';


if(isset($_GET['cat'])){
  $cat = urldecode($_GET['cat']);
	if (strpos($cat, 'http://th-brandenburg.de/ns/itcat#') !== FALSE){
		$cat = explode( 'http://th-brandenburg.de/ns/itcat#', $cat );
    $currentPath .= '/ ' . $cat[1];
	}
}

if(isset($_GET['service'])){
  $service = urldecode($_GET['service']);
	if (strpos($service, 'http://th-brandenburg.de/ns/itcat#') !== FALSE){
		$serviceName = explode( 'http://th-brandenburg.de/ns/itcat#', $service );
    $currentPath .= '/ ' . $serviceName[1];
	}
}


#$currentPath;

?>





<header class="demo-header mdl-layout__header mdl-color--white mdl-color--grey-100 mdl-color-text--grey-600">
  <div class="mdl-layout__header-row">
    <span class="mdl-layout-title"><a href="./">ITcat</a> <?php echo $currentPath ?></span>
    <div class="mdl-layout-spacer"></div>
    <div class="mdl-textfield mdl-js-textfield mdl-textfield--expandable">
      <label class="mdl-button mdl-js-button mdl-button--icon" for="search">
        <i class="material-icons">search</i>
      </label>
      <div class="mdl-textfield__expandable-holder">
        <input class="mdl-textfield__input" type="text" id="search" />
        <label class="mdl-textfield__label" for="search">Enter your query...</label>
      </div>
    </div>
    <button class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--icon" id="hdrbtn">
      <i class="material-icons">more_vert</i>
    </button>
    <ul class="mdl-menu mdl-js-menu mdl-js-ripple-effect mdl-menu--bottom-right" for="hdrbtn">
      <li class="mdl-menu__item">Item</li>
      <li class="mdl-menu__item">FAQ</li>
      <li class="mdl-menu__item">Item2</li>
    </ul>
  </div>
</header>
