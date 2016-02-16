<?php

function showCardTemplate ($uri, $title, $description, $numberOfServices, $bgColor, $urlParameter, $cardSize = 4, $colorValue = 300, $callToActionValue = 'Öffnen') {

  $uri = urlencode($uri);

  if($title == '' || !isset($title)){
    $title = 'Kein Title';
  }

  if($description == '' || !isset($description)){
    $description = 'Keine Beschreibung vorhanden';
  }

  if($bgColor == '' || !isset($bgColor)){
    $bgColor = 'grey';
  }

  if($numberOfServices == '' || !isset($numberOfServices)){
    $badge = '';
  }
  else{
    $badge = 'mdl-badge badge-btn" data-badge="'. $numberOfServices;
  }


  if($colorValue != ''){
    $colorValue = '-'.$colorValue;
  }

?>

<div class="itcat-category mdl-card mdl-shadow--2dp mdl-cell mdl-cell--<?php echo $cardSize; ?>-col mdl-cell--12-col-phone mdl-grid mdl-grid--no-spacing">
    <a class="mdl-card__title mdl-card--expand mdl-color--<?php echo $bgColor .  $colorValue; ?>" href="<?php echo $urlParameter . $uri;?>">
      <h2 class="mdl-card__title-text <?php echo $badge; ?>">
        <?php echo $title; ?>
      </h2>
    </a>
    <?php
    echo '<div class="mdl-card__supporting-text">';
      echo $description;
    echo '</div>';
    ?>
    <div class="mdl-card__actions mdl-card--border">
      <a class="mdl-button mdl-button--colored mdl-js-button mdl-js-ripple-effect" href="<?php echo $urlParameter . $uri;?>">
         <?php echo $callToActionValue; ?>
      </a>
    </div>
</div>

<?php
}
?>