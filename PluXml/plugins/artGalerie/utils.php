<?php

class galerieUtils {

	private $galeriedesc = array();
	private $parameters = array();

private function listXML($dir) {
	
	$results = array();
	
	foreach(scandir($dir) as $v) {
		if($v[0] != '.'){
			$w=$dir.'/'.$v;
			if(is_dir($w)){
				$results = array_merge($results, $this->listXML($w) );
			}
			else {
				if (preg_match("/.xml$/i", $v)) {
						$results[]=$w;
					}
			}
		}
	}
	
	return $results;
}


	private function parseXML($filename) {
		
		if (!file_exists($filename)) { return; }
		
		# Mise en place du parseur XML
		$data = implode('',file($filename));
		$parser = xml_parser_create(PLX_CHARSET);
		xml_parser_set_option($parser,XML_OPTION_CASE_FOLDING,0);
		xml_parser_set_option($parser,XML_OPTION_SKIP_WHITE,0);
		xml_parse_into_struct($parser,$data,$values,$iTags);
		xml_parser_free($parser);
		
		if(isset($iTags['image']) && isset($iTags['name'])) {
			$nb = sizeof($iTags['name']);
			for($i=0;$i<$nb;$i++) {
				$img = plxUtils::getValue($iTags['name'][$i]);
				$desc = plxUtils::getValue($iTags['description'][$i]);
				$this->galeriedesc[$values[$img]['value']] = $values[$desc]['value'];
			}
		}
		if(isset($iTags['parametre'])){
			$nb = sizeof($iTags['parametre']);
			for($i = 0; $i < $nb; $i++) {
				$this->parameters[ $values[$iTags['parametre'][$i]]['attributes']['name'] ] = $values[ $iTags['parametre'][$i] ]['value'];
			}
		}
	}


	public function writenew($filename){
		
			if (empty($this->galeriedesc)) { return; };
			
			# On génére le fichier XML
			$xml = "<?xml version=\"1.0\" encoding=\"".PLX_CHARSET."\"?>\n";
			$xml .= "<document>\n";
			$xml .= isset($this->parameters['description'])? "\t<parametre name=\"description\">1</parametre>\n":"<parametre name=\"description\">0</parametre>\n";
			$xml .= isset($this->parameters['thumbdesc'])? "\t<parametre name=\"thumbdesc\">1</parametre>\n":"<parametre name=\"thumbdesc\">0</parametre>\n";			
			foreach($this->galeriedesc as $k => $v) {
				$xml .= "\t<image name=\"".$k."\"><![CDATA[".$v."]]></image>\n";
				
			}
			$xml .= "</document>";
			
			# On écrit le fichier
			if(plxUtils::write($xml, $filename))
				return plxMsg::Info(L_SAVE_SUCCESSFUL);
			else {
				return plxMsg::Error(L_SAVE_ERR.' '.$filename);
			}
			
		
	}
	
	public function convertgaleries($root_dir) {
	
		$files = $this->listXML($root_dir);
		foreach($files as $file) {
			$this->galeriedesc = array();
			$this->parameters = array();
			$this->parseXML($file);
			$this->writenew($file);
		}
}

	
}





?>
