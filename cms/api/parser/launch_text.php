<?php 

/** 
* LaunchText
* Convert N/X Tags IDs into Live-IDs
**/ 
class LaunchText extends NXParser  { 
     
    var $launchImages;
    var $variation;
     
    /** 
     * constructor 
     **/ 
    function LaunchText($variation=null, $launchImages=false){ 
    	global $c;
    	if ($variation==null) $variation = $c["stdvariation"];
    	$this->variation = $variation;
    	$this->launchImages = $launchImages;
    }     

    /*
     * Parse the a tag
     * @param array $attr=null attributes array 
     * @param string $data="" input data 
     * @return string 
     **/ 
   	function link($attr=null,$data="") {   		
   		if (is_numeric($attr["PAGE"])) {   			
   			$newpage = translateState($attr["PAGE"], 10, false);
   			$output = '{NX:LINK PAGE="'.$newpage.'"';
			$manualattribs = array("PAGE", "HREF", "VARIATION", "LAUNCHIMAGES");			
			foreach ($attr as $key => $value) {
				if (!in_array(strtoupper($key), $manualattribs))
					$output .= " ".strtoupper($key)."=\"".$value."\"";
			}
			$output.= '}';		
   		}
   		return $output;
   	}
     
    /*
     * Parse the image tag
     * @param array $attr=null attributes array      
     * @return string 
     **/ 
    function image($attr=null){         
        if (is_numeric($attr["ID"])) {
        	$newid = translateState($attr["ID"], 10, false);
        	$output = '{NX:IMAGE ID="'.$newid.'"';
        	
			$manualattribs = array("ID", "SRC", "VARIATION");
			foreach ($attr as $key => $value) {
				if (!in_array(strtoupper($key), $manualattribs))
		 			$output .= " ".strtoupper($key)."=\"".$value."\"";
			}
		  	$output.= '}';	        	

	  		launchContent($attr["ID"], 	10, $attr["VARIATION"]);
        }
    	return $output; 
    }
     

} 

?>