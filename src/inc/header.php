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

$sparql = '
    SELECT ?home_title ?home_title_mobile
    {    
        GRAPH ?g {
            itcat_app:home_title itcat_app:name ?home_title .
            itcat_app:home_title_mobile itcat_app:name ?home_title_mobile .
            FILTER (langMatches(lang(?home_title),"' . LANG . '"))
            FILTER (langMatches(lang(?home_title_mobile),"' . LANG . '"))
        }
    }';

    $result = $db->query($sparql);
    if (!$result) {
        print $db->errno() . ": " . $db->error() . "\n";
    exit;
    }
?>


<header class="demo-header mdl-layout__header mdl-color--white mdl-color--grey-100 mdl-color-text--grey-600">
  <div class="mdl-layout__header-row">
    <span class="mdl-layout-title">
      <img src="assets/images/thb_logo_rgb.png" style="height:18px;margin-right:5px; padding-bottom:3px;">
        <?php
        if ($result->num_rows() > 0) {
            while ($row = $result->fetch_array()) {
                echo '<a href="./" class="mdl-cell--hide-phone" style="color: #000">'.$row['home_title'].'</a>';
                echo '<a href="./" class="mdl-cell--hide-desktop mdl-cell--hide-tablet">'.$row['home_title_mobile'].'</a>';
            }
        }
        ?>
      <!--<a href="./" class="mdl-cell--hide-phone" style="color: #000">IT-Dienste der TH Brandenburg</a>
      <a href="./" class="mdl-cell--hide-desktop mdl-cell--hide-tablet">IT-Dienste THB</a>-->
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

      <button class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--icon" id="language">
          <i class="material-icons">language</i>
      </button>
      <ul class="mdl-menu mdl-js-menu mdl-js-ripple-effect mdl-menu--bottom-right" for="language">
          <a class="mdl-menu__item langBtn" id="en">English</a>
          <a class="mdl-menu__item langBtn" id="de">Deutsch</a>
      </ul>
    <button class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--icon" id="hdrbtn">
      <i class="material-icons">more_vert</i>
    </button>
    <ul class="mdl-menu mdl-js-menu mdl-js-ripple-effect mdl-menu--bottom-right" for="hdrbtn">
       <a href="https://github.com/ITcatalog/" target="_blank" class="mdl-menu__item">Github</a>
       <a href="https://github.com/ITcatalog/WebApp/issues" target="_blank" class="mdl-menu__item">Report</a>
    </ul>
  </div>
</header>

<script>
    $(document).ready(function() {
        $("#en").click(function(){
            $.ajax({
                type: "post",
                url: "index.php",
                data: {"en": "en"},
                success: function (){
                    $(document).ajaxStop(function(){
                        location.reload();
                    });
                }
            });

        });
        $("#de").click(function(){
            $.ajax({
                type: "post",
                url: "index.php",
                data: {"de": "de"},
                success: function (){
                    $(document).ajaxStop(function(){
                        location.reload();
                    });
                }
            });
        });

    });
</script>
