<?php

/*
Class handels literals

*/

class serviceController{

	var $literalArray;
	var $service;
	var $db;

	public function __construct($db, $service)
    {
      $this->db = $db;
			$this->service = $service;
			$this->readLiteral();
    }

	function readLiteral(){
		$sparql = '
    SELECT ?prop ?prefLabel ?value
		WHERE {
			<'.$this->service.'> ?prop ?valueLang.
			{
				?prop a owl:AnnotationProperty.
			}
			UNION{
				?prop a owl:DatatypeProperty.
			}
			?prop skos:prefLabel ?propLabelLang.
			FILTER (langMatches(lang(?propLabelLang),"'.LANG.'"))
			BIND (str(?propLabelLang) AS ?prefLabel)
			BIND (str(?valueLang) AS ?value)
		}
		';

		$result = $this->db->query( $sparql );
		if( !$result ) { print $this->db->errno() . ": " . $this->db->error(). "\n"; exit; }
		while($row = $result->fetch_array()){
			$this->literalArray[$row['prop']] = $row;
		}
	}

	public function getLiteralProperty ($type, $property){
		$exp = explode(":", $type);
		$n = $exp[0];
		$p = $exp[1];
		return $this->literalArray[$this->db->getNs($n).$p][$property];
	}

	public function showLiteralItem($property){

		echo '<div class="service-attribute">';
			echo '<div class="service-attribute__title">';
				echo $this->getLiteralProperty ($property, 'prefLabel');
			echo '</div>';

			echo '<div class="service-attribute__value">';
				echo $this->getLiteralProperty ($property, 'value');
			echo '</div>';
		echo '</div>';

	}

  public function getObjectProperty ($property){
    $sparql = '
  	SELECT ?uri ?prefLabel
  	WHERE {
    	<'.$this->service.'> rdf:type schema:Service;
      '.$property.' ?uri.
    	?uri skos:prefLabel ?prefLabelLang.
    	FILTER (langMatches(lang(?prefLabelLang),"'.LANG.'") || langMatches(lang(?prefLabelLang),""))
  		BIND (str(?prefLabelLang) AS ?prefLabel)
  	}
    ';

    $result = $this->db->query( $sparql );
    if( !$result ) { print $this->db->errno() . ": " . $this->db->error(). "\n"; exit; }


    $return = array(
      'num' => $result->num_rows(),
      'result' => $result,
    );

    return $return;

  }

}