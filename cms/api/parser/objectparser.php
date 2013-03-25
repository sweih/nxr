<?php


/** 
* ObjectParser
* Converts object code to objects.
**/ 
class ObjectParser {
	
	/**
	 *  Parses the placeholders [xxx] and replaces them with code.
	 *
	 * @param string $text text to parse;
	 */
	function parse($text) {
	  global $cds, $db;
	  if (is_object($cds)) {	  	
	  	$live = ($cds->level==10);
	  } else {
	  	$live = false;
	  }
	  if (preg_match_all('/\[(.+?)\]/is', $text, $matches)) {
	  	$tags = $matches[1];
  		  foreach($tags as $tag) {
  		    $id="";
  		  	if ($live) {
  		      $sql = 'SELECT c.CID FROM content c, state_translation t Where c.CID=t.OUT_ID AND t.LEVEL=10 AND t.EXPIRED=0 AND UPPER(c.ACCESSKEY)="'.strtoupper($tag).'"';
  		    } else {
              $sql = 'SELECT c.CID FROM content c, state_translation t Where c.CID<>t.OUT_ID AND UPPER(c.ACCESSKEY)="'.strtoupper($tag).'"';  		    	  		    	
  		    }
  		    $query = new query($db, $sql);
			if ($query->getrow()) {
				$id = $query->field('CID');
			}
  		    if ($id != "") {
  		    	$pgnRef = createPGNRef2($id);     
            	if (! is_object($pgnRef)) return " ";
            	if (!$pgnRef->exists()) {
              	  $pgnRef = createPGNRef2($id, $c["stdvariation"]);
          		}
          		$res = $pgnRef->draw();
          		$text = str_ireplace('['.$tag.']', $res, $text);                 
  		    }	
  		 }
	  }	
	  return $text;
	}
	
	/**
	 *  Parses the placeholders [xxx] and launches the coresponding contents.
	 *
	 * @param string $text text to parse;
	 * @param integer $variation Variation which is to be launched.
	 */
	function launch($text, $variation) {	
		global $db;
		if (preg_match_all('/\[(.+?)\]/is', $text, $matches)) {
	  	$tags = $matches[1];
  		  foreach($tags as $tag) {
  		    $sql = 'SELECT c.CID FROM content c, state_translation t Where c.CID<>t.OUT_ID AND UPPER(c.ACCESSKEY)="'.strtoupper($tag).'"';  		    	  		    	 		  
  		    $query = new query($db, $sql);
			if ($query->getrow()) {
				$id = $query->field('CID');
			}
  		    if ($id != "") {
  		    	launchContent($id, 10, $variation);
  		    }	
  		 }
	  }	
	  return $text;	
	}
	
	
}

?>