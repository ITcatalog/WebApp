<?php
if (!isset($_GET['action'])) {
    $_GET['action'] = 'categories';
}

$sparql = '
    SELECT ?category_tab ?catalog_tab ?provider_tab
    {    
        GRAPH ?g {
            itcat_app:category_tab itcat_app:name ?category_tab .
            itcat_app:catalog_tab itcat_app:name ?catalog_tab .
            itcat_app:provider_tab itcat_app:name ?provider_tab .
            FILTER (langMatches(lang(?category_tab),"' . LANG . '"))
            FILTER (langMatches(lang(?catalog_tab),"' . LANG . '"))
            FILTER (langMatches(lang(?provider_tab),"' . LANG . '"))
        }
    }';

$result = $db->query($sparql);
if (!$result) {
    print $db->errno() . ": " . $db->error() . "\n";
    exit;
}

$category_tab = '';
$catalog_tab = '';
$provider_tab = '';
if ($result->num_rows() > 0) {
    while ($row = $result->fetch_array()) {
        $category_tab = $row['category_tab'];
        $catalog_tab = $row['catalog_tab'];
        $provider_tab = $row['provider_tab'];
    }
}

?>

<div class="mdl-cell mdl-cell--12-col mdl-grid">
    <div class="mdl-tabs">
        <div class="mdl-tabs__tab-bar">
            <?php

            ?>
            <a href="?c=home&action=categories"
               class="mdl-tabs__tab <?php if (isset($_GET['action']) && $_GET['action'] == 'categories') {
                   echo 'homeTabBarActive';
               } ?>"><?php echo $category_tab ?></a>
            <a href="?c=home&action=catalog"
               class="mdl-tabs__tab <?php if (isset($_GET['action']) && $_GET['action'] == 'catalog') {
                   echo 'homeTabBarActive';
               } ?>"><?php echo $catalog_tab ?></a>
            <a href="?c=home&action=provider"
               class="mdl-tabs__tab <?php if (isset($_GET['action']) && $_GET['action'] == 'provider') {
                   echo 'homeTabBarActive';
               } ?>"><?php echo $provider_tab ?></a>
        </div>
    </div>
</div>


<?php


if (isset($_GET['action'])) {

    switch ($_GET['action']) {

        case 'categories':
            include('content/categories.php');
            break;

        case 'catalog':
            include('content/catalog.php');
            break;

        case 'provider':
            include('content/provider.php');
            break;

    }

} else {
    include('content/categories.php');
}


?>
