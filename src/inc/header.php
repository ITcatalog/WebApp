<?php

$pageName = '';
$pageColor = '';
$currentPath = '';

if(isset($pageTitle)){

  $currentPath .= '/ ' . $pageTitle;

}


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


?>


<header class="demo-header mdl-layout__header mdl-color--white mdl-color--grey-100 mdl-color-text--grey-600">
  <div class="mdl-layout__header-row">
    <span class="mdl-layout-title">
      <img src="assets/images/thb_logo_rgb.png" style="height:18px;margin-right:5px; padding-bottom:3px;">
      <a href="./" class="mdl-cell--hide-phone" style="color: #000">IT-Dienste der TH Brandenburg</a>
      <a href="./" class="mdl-cell--hide-desktop mdl-cell--hide-tablet">IT-Dienste THB</a>
      <?php echo $currentPath ?>
    </span>
    <div class="mdl-layout-spacer"></div>
    <div class="mdl-textfield mdl-js-textfield mdl-textfield--expandable">
      <label class="mdl-button mdl-js-button mdl-button--icon" for="search">
        <i class="material-icons">search</i>
      </label>
      <div class="mdl-textfield__expandable-holder">
        <form name="" action="" method="get">
          <input class="mdl-textfield__input" type="text" id="search" name="search"/>
          <label class="mdl-textfield__label" for="search">Enter your query...</label>
        </form>
      </div>
    </div>
    <button class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--icon" id="hdrbtn">
      <i class="material-icons">more_vert</i>
    </button>
    <ul class="mdl-menu mdl-js-menu mdl-js-ripple-effect mdl-menu--bottom-right" for="hdrbtn">
      <a href="https://github.com/ITcatalog/" target="_blank" class="mdl-menu__item">Github</a>
      <a href="https://github.com/ITcatalog/WebApp/issues" target="_blank" class="mdl-menu__item">Report</a>
    </ul>
  </div>
</header>
