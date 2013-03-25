<?php 

/** 
* NX 2 HTML
* Convert texts with N/X Tags to HTML
**/ 
class NX2HTML extends NXParser{ 
        
    var $variation;
     
    /** 
     * constructor 
     * @param integer Variation-ID for parsing.
     **/ 
    function NX2HTML($variation=null){ 
    	global $c;
    	if ($variation==null) $variation = $c["stdvariation"];
    	$this->variation = $variation;
    }     

	/*
	 * Parse the link tag
     * @param array $attr=null attributes array      
     * @return string 
     **/ 
    function link($attr=null) {
		global $c;
		if (is_numeric($attr["PAGE"]) && $attr["PAGE"] != "") {
		  $spm = getDBCell("sitepage", "SPM_ID", "SPID = ".$attr["PAGE"]);
		  if ($spm != "") {
		  	$template = getDBCell("sitepage_master", "TEMPLATE_PATH", "SPM_ID = ".$spm);		
		  	$live = getDBCell("state_translation", "OUT_ID", "OUT_ID = ".$attr["PAGE"]." AND LEVEL = 10 AND EXPIRED=0");

	
		  	if ($live != "")
				$path = $c["livedocroot"];
		  	else 
				$path = $c["devdocroot"];
		
		
				// first, we'll use standard-variation if nothing has been set in the original link
				if (!isset($attr["V"])) $attr["V"] = $this->variation;
				
				// if the target sitepage is expired, we'll directly link to the www folder.
				if ($live && isSPExpired($attr["PAGE"], $attr["V"])) {
					$output = "<a href=\"".$path."\"";
				} else {
					// we need to manually add N/X-GetVars like page and v
					$output = "<a href=\"".$path.$template."?page=".$attr["PAGE"]."&v=".$attr["V"];
					// append additional getvars.
					$output .= $attr["GETVARS"]."\"";
				}
				
				// other attributes are readded if they haven't already been manually treated
				$manualattribs = array("HREF", "PAGE", "V", "GETVARS", "VARIATION");
				foreach ($attr as $key => $value) {
					if (!in_array($key, $manualattribs))
					$output .= " ".$key."=\"".$value."\"";
				}
				
				$output .= ">";
				return $output;
		  }
		}		
	} 
     
    /*
     * Parse the image tag
     * @param array $attr=null attributes array      
     * @return string 
     **/ 
    function image($attr=null){ 
    	global $c;    	
    	if (is_numeric($attr["ID"])) {          
    	  $pgnRef = createPGNRef2($attr["ID"], $this->variation);     
          if (! is_object($pgnRef)) return " ";
          if (!$pgnRef->exists()) {
            $pgnRef = createPGNRef2($attr["ID"], $c["stdvariation"]);
          }                 

          $image = $pgnRef->draw("ALL");
          $manualattribs = array("WIDTH", "HEIGHT", "PATH", "ID", "VARIATION");
          $output = "<img src=\"".$image["path"]."\"";
			(array_key_exists("WIDTH", $attr)) ? $output .= " width=\"".$attr["WIDTH"]."\"" : $output .= " width=\"".$image["width"]."\"";
			(array_key_exists("HEIGHT", $attr)) ? $output .= " height=\"".$attr["HEIGHT"]."\"" : $output .= " height=\"".$image["height"]."\"";
          foreach ($attr as $key => $value) {
        	if (!in_array(strtoupper($key), $manualattribs))
        		$output .= " $key=\"$value\"";
          }
          $output .= ">";
          return $output; 
        }
    }     
} 

?>