<?php

#Change permission sudo chown www-data:www-data -R WebApp/

if($_SERVER["SERVER_ADDR"] == '::1' || $_SERVER["SERVER_ADDR"] == '127.0.0.1') {
  echo 'Running on localhost.';
}
else{

  $output = exec('cd /var/www/html/git/WebApp/ && git pull origin master');
  #echo '' . json_encode($output) . '';
  echo $output;

}

function version() {
    exec('git describe --always',$version_mini_hash);
    exec('git rev-list HEAD | wc -l',$version_number);
    exec('git log -1',$line);
    $version['short'] = "".trim($version_number[0]).".".$version_mini_hash[0];
    $version['full'] = "".trim($version_number[0]).".$version_mini_hash[0] (".str_replace('commit ','',$line[0]).")";
    return $version;
  }
//echo version()['short'];
?>
