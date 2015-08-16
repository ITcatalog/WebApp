<main class="mdl-layout__content mdl-color--grey-100">
  <div class="mdl-grid demo-content">

<?php

if(isset($_GET['c'])){
  switch ($_GET['c']){

    case 'catagories':
      include ('content/catagories.php');
      break;

    case 'category':
      include ('content/category.php');
      break;

    case 'service':
      include ('content/service.php');
      break;

  }
}
else {

  #include ('content/catagories.php');

}


if(isset($_GET['include'])){

  include('content/' . $_GET['include']);

}

?>


  </div>
</main>
