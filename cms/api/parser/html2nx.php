<?php 

/** 
* HTML 2 NX
* Convert html texts into a N/X format with internal images
**/ 
class HTML2NX extends NXParser { 
      
     
    /** 
     * constructor 
     **/ 
    function HTML2NX(){ 
      $this->startStr='<';
  	  $this->endStr='>';
    }     


    /*
     * Parse the a tag
     * @param array $attr=null attributes array     
     * @return string
     **/
    function a($attr=null) {
    	$fileinfo = pathinfo($attr["HREF"]);
    	$template = $fileinfo["basename"];
    	$exp = explode("?", $template);
    	$template = $exp[0];
    	// is it internal link?
    	$getstring = substr($fileinfo["basename"], strpos($fileinfo["basename"], "?") + 1, strlen($fileinfo["basename"]) - strpos($fileinfo["basename"], "?"));
    	$getattribs = explode("&", $getstring);


    	$output = "{NX:LINK";    	
    	foreach ($attr as $key => $value) {
    		if (!in_array(strtoupper($key), array("HREF"))) {
    			$output .= " ".strtoupper($key)."=\"$value\"";
    			// echo "there we are:".$key.":";
    		}
    	}
    	
    	// adding get-attributes to the link
    	$getvars = " GETVARS=\"";
    	for ($i=0; $i<count($getattribs); $i++) {
    		$nameval = explode("=", $getattribs[$i]);
    		$outattribs[strtoupper($nameval[0])] = $nameval[1];
    		$nxvalues = array("PAGE", "V");
    		// add page and v directly to the NX:LINK-Tag as autonomous attributes
    		// other additional get-attributes are handed over in a single NX:LINK-Attribute.
    		if (in_array(strtoupper($nameval[0]), $nxvalues)) {
    			$output .= " ".strtoupper($nameval[0])."=\"".$nameval[1]."\"";
    		} else {
    			$getvars .= "&".$nameval[0]."=".$nameval[1];
    		}
    	}
    	$getvars .= "\"";
    	$output .= $getvars."}";

    	// check whether the link points to a N/X sitepage. Return original link if not.
    	if (isset($outattribs["PAGE"]) && (getDBCell("sitepage", "SPM_ID", "SPID = ".$outattribs["PAGE"]))) {
    		return $output;
    	} else {
    		$oldlink = "<a";
    		foreach ($attr as $key => $value) {
    			$oldlink .= " $key=\"$value\"";
    		}
    		$oldlink .= ">";
    		return $oldlink;
    	}
    }    
    
     
    /*
     * Parse the image tag
     * @param array $attr=null attributes array      
     * @return string 
     **/ 
    function img($attr=null){ 
    	$fileinfo = pathinfo($attr["SRC"]);
    	$id = substr($fileinfo["basename"], 0 , strlen($fileinfo["basename"]) - 1 - strlen($fileinfo["extension"]));
    	if (is_numeric($id)) {
		  $cid = getDBCell("content_variations", "CID", "FK_ID = ".$id);
    	}
    	if (is_numeric($cid)) {
		  $output = '{NX:IMAGE ID="'.$cid.'"';
		  $manualattribs = array("ID", "SRC");
		  foreach ($attr as $key => $value) {
		  	if (!in_array(strtoupper($key), $manualattribs) && strlen($key)>0)
		  		$output .= " ".strtoupper($key)."=\"".$value."\"";
		  }
		  $output.= '}';	
		} else {
			$oldlink = "<img";
			foreach ($attr as $key => $value) {
				$oldlink .= " $key=\"$value\"";
			}
			$oldlink .= ">";
			return $oldlink;
		}
		return $output; 
    }    
}

?>