<?php

$sparql = '
    SELECT ?nav_home ?nav_report ?nav_map ?nav_list ?nav_category ?nav_catalog ?nav_privacy
    {    
        GRAPH ?g {
            itcat_app:nav_home itcat_app:name ?nav_home .
			itcat_app:nav_report itcat_app:name ?nav_report .
			itcat_app:nav_map itcat_app:name ?nav_map .
			itcat_app:nav_list itcat_app:name ?nav_list .
			itcat_app:nav_category itcat_app:name ?nav_category .
			itcat_app:nav_catalog itcat_app:name ?nav_catalog .
			itcat_app:nav_privacy itcat_app:name ?nav_privacy .
            FILTER (langMatches(lang(?nav_home),"' . LANG . '"))
            FILTER (langMatches(lang(?nav_report),"' . LANG . '"))
            FILTER (langMatches(lang(?nav_map),"' . LANG . '"))
    		FILTER (langMatches(lang(?nav_list),"' . LANG . '"))
            FILTER (langMatches(lang(?nav_category),"' . LANG . '"))
            FILTER (langMatches(lang(?nav_catalog),"' . LANG . '"))
    		FILTER (langMatches(lang(?nav_privacy),"' . LANG . '"))
        }
    }';

$result = $db->query($sparql);
if (!$result) {
    print $db->errno() . ": " . $db->error() . "\n";
    exit;
}

?>

<div class="demo-drawer mdl-layout__drawer mdl-color--blue-grey-900 mdl-color-text--blue-grey-50">
  <header class="demo-drawer-header">
    <div style="padding: 10px">
      <img src="assets/images/user.jpg" class="demo-avatar">
    </div>
    <div class="demo-avatar-dropdown">
      <span style="padding: 10px">BMaKE</span>
      <div class="mdl-layout-spacer"></div>
    </div>
  </header>

  <nav class="demo-navigation mdl-navigation mdl-color--blue-grey-800">
      <?php
          if ($result->num_rows() > 0) {
              while ($row = $result->fetch_array()) {
                  echo '<a class="mdl-navigation__link" href="?c=home"><i class="mdl-color-text--blue-grey-400 material-icons" role="presentation">home</i>'.$row['nav_home'].'</a>';
                  echo '<a class="mdl-navigation__link" href="?c=reports"><i class="mdl-color-text--blue-grey-400 material-icons" role="presentation">insert_chart</i>'.$row['nav_report'].'</a>';
                  echo '<a class="mdl-navigation__link" href="?c=map"><i class="mdl-color-text--blue-grey-400 material-icons" role="presentation">share</i>'.$row['nav_map'].'</a>';
                  echo '<a class="mdl-navigation__link" href="?c=list"><i class="mdl-color-text--blue-grey-400 material-icons" role="presentation">view_list</i>'.$row['nav_list'].'</a>';
                  echo '<a class="mdl-navigation__link" href="?c=categories"><i class="mdl-color-text--blue-grey-400 material-icons" role="presentation">view_module</i>'.$row['nav_category'].'</a>';
                  echo '<a class="mdl-navigation__link" href="?c=catalog"><i class="mdl-color-text--blue-grey-400 material-icons" role="presentation">local_library</i>'.$row['nav_catalog'].'</a>';
                  echo '<a class="mdl-navigation__link" href="?c=imprint"><i class="mdl-color-text--blue-grey-400 material-icons" role="presentation">assignment</i>'.$row['nav_privacy'].'</a>';
              }
          }
      ?>
    <!--<a class="mdl-navigation__link" href="?c=portfolio"><i class="mdl-color-text--blue-grey-400 material-icons" role="presentation">dashboard</i>Portfolio</a>-->
    <div class="mdl-layout-spacer"></div>
    <a class="mdl-navigation__link" href="https://github.com/ITcatalog/WebApp/wiki" target="_blank"><i class="mdl-color-text--blue-grey-400 material-icons" role="presentation">help_outline</i><span class="visuallyhidden">Help</span></a>
  </nav>
</div>
