<?php

if (isset($_POST['en'])) {
    setcookie("lang", "en", 0, "/");
}

if (isset($_POST['de'])) {
    setcookie("lang", "de", 0, "/");
}

if (!isset($_COOKIE['lang'])) {
    //setcookie("lang", "de", 0, "/");
    define('LANG', 'de');
} else {
    define('LANG', $_COOKIE['lang']);
}

require_once('./inc/config.php');
require_once('./inc/functions.php');

?>
<!doctype html>
<html lang="en">
<head>
    <?php if (isset($_GET['input'])) echo '<base href="./">'; ?>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="IT Service Catalog Demo">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ITcat</title>

    <!-- Add to homescreen for Chrome on Android -->
    <meta name="mobile-web-app-capable" content="yes">
    <!--<link rel="icon" sizes="192x192" href="images/touch/chrome-touch-icon-192x192.png">-->
    <link rel="shortcut icon" href="thb_favicon.ico">

    <!-- Add to homescreen for Safari on iOS -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-title" content="Material Design Lite">
    <link rel="apple-touch-icon-precomposed" href="apple-touch-icon-precomposed.png">

    <!-- Tile icon for Win8 (144x144 + tile color) -->
    <meta name="msapplication-TileImage" content="images/touch/ms-touch-icon-144x144-precomposed.png">
    <meta name="msapplication-TileColor" content="#3372DF">

    <link rel="icon" type="image/png" href="assets/images/thb_logo_rgb.png"/>

    <link rel="stylesheet" href="./bower_components/material-design-lite/material.min.css">

    <link
        href="https://fonts.googleapis.com/css?family=Roboto:regular,bold,italic,thin,light,bolditalic,black,medium&amp;lang=en"
        rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">

    <!-- Color -->
    <link rel="stylesheet" href="https://storage.googleapis.com/code.getmdl.io/1.0.2/material.grey-red.min.css"/>

    <script src="//code.jquery.com/jquery-1.11.3.min.js"></script>

    <!-- VIS.js -->

    <script src="bower_components/vis/dist/vis.js"></script>
    <link href="bower_components/vis/dist/vis.css" rel="stylesheet" type="text/css"/>

    <!-- ITcat styles -->
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/itcat.css">
    <link rel="stylesheet" href="assets/css/custom.css">

</head>
<body>

<div class="demo-layout mdl-layout mdl-js-layout mdl-layout--fixed-header">
    <?php
    include('inc/header.php');
    include('inc/navigation.php');
    include('inc/main.php');
    ?>
</div>


<script src="./bower_components/material-design-lite/material.min.js"></script>

<script src="./assets/js/jquery.truncate.min.js"></script>
<script src="./assets/js/custom.js"></script>

</body>
</html>
