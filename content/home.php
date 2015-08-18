<?php
if(!isset($_GET['action'])){
  $_GET['action'] = 'categories';
}
 ?>

<div class="mdl-cell mdl-cell--12-col mdl-grid">

  <div class="mdl-tabs">
    <div class="mdl-tabs__tab-bar">
      <a href="?c=home&action=categories" class="mdl-tabs__tab <?php if(isset($_GET['action']) && $_GET['action'] == 'categories'){echo 'homeTabBarActive';}?>">Kategorien</a>
      <a href="?c=home&action=catalog" class="mdl-tabs__tab <?php if(isset($_GET['action']) && $_GET['action'] == 'catalog'){echo 'homeTabBarActive';}?>">Kataloge</a>
    </div>
  </div>

</div>


<?php


if(isset($_GET['action'])){

  switch($_GET['action']){

    case 'categories':
      include('content/categories.php');
      break;

    case 'catalog':
      include('content/catalog.php');
      break;


  }

}
else{
  include('content/categories.php');
}


 ?>
