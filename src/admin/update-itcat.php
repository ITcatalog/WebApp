<?php

function sendFileToSparqlHTTP($file, $method){
  $sparqlEndpoint = 'http://fbwsvcdev.fh-brandenburg.de:8080/fuseki/itcat/data?default';
  $file_name_with_full_path = realpath($file);

  $POST = array(
    'file_contents' => '@'.$file_name_with_full_path
  );
  $HTTPheader = array(
    'Content-Type: text/turtle'
  );


  $file = fopen($file_name_with_full_path, 'r');
  $size = filesize($file_name_with_full_path);
  $filedata = fread($file,$size);

  $curl = curl_init();
  curl_setopt ($curl, CURLOPT_URL, $sparqlEndpoint);

  if($method == 'POST'){
    curl_setopt($curl, CURLOPT_POST, 1);
  }
  elseif ($method == 'PUT'){
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
  }

  curl_setopt($curl, CURLOPT_HTTPHEADER, $HTTPheader);

  //curl_setopt($curl, CURLOPT_BINARYTRANSFER, TRUE);

  curl_setopt($curl, CURLOPT_POSTFIELDS, $filedata);


  curl_setopt($curl, CURLINFO_HEADER_OUT, true); // enable tracking
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

  $result = curl_exec ($curl);

  $headerSent = curl_getinfo($curl, CURLINFO_HEADER_OUT ); // request headers

  echo '<pre>';
    print_r($headerSent);

    curl_close ($curl);

    print $result;
  echo '</pre>';
}


if($_SERVER["SERVER_ADDR"] == '::1' || $_SERVER["SERVER_ADDR"] == '127.0.0.1') {
  echo 'Running on localhost.';
}
else{

  if(!isset($_GET['branch'])){
      $branch = 'master';
  }
  else{
    $branch = $_GET['branch'];
  }

  $output = exec('cd ./../../../ITcat/ && git pull origin ' . $branch .'');
  #echo '' . json_encode($output) . '';
  echo '<h2>' . $output . '<h2>';
}
if($output != 'Already up-to-date.'){
  echo '<h2>SchemaGraph & Datagraph</h2>';
  #sendFileToSparqlHTTP('../../ITcat/Ontology/SchemaDataGraph.ttl', 'PUT');


  echo '<h2>SchemaGraph</h2>';
  sendFileToSparqlHTTP('../../ITcat/Ontology/SchemaGraph.ttl', 'PUT');

  echo '<h2>DataGraph</h2>';
  sendFileToSparqlHTTP('../../ITcat/Ontology/DataGraph.ttl', 'POST');
}