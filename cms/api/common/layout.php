<?
	/**
	 * @module HTMLLayout
	 * @package Layout
	 */

	
/**
 * Draws a Textbox with rounded corners
 *
 * @param string $text
 * @param string $style
 */
function getBoxedText($text, $style="headerinfo", $width="70%")	 {
	$out = '<div class="'.$style.'" style="width:'.$width.';">';
	$out.= '<div class="active1"><div class="active2"><div></div></div></div>';
  	$out.= '<div class="'.$style.'">&nbsp;&nbsp;';
  	$out.= $text;
  	$out.= '</div>';
  	$out.= '<div class="active4"><div class="active5"><div></div></div></div>';
  	$out.= '</div>';
	return $out;
}

/**
 * Get the header of a form
 *
 * @param string $title Title of the form.
 * @param integer $width Width of the formheader
 */
function getFormHeadline($title, $width="100%", $link="") {
  $out = '<table width="'.$width.'" border="0" cellpadding="0" cellspacing="0">';
  $out.= '<tr><td colspan="2" class="line"></td></tr>';
  $out.= '<tr><td class="headline"><span class="h2">'.$title.'</span></td>';
  $out.= '<td align="right" class="headline">&nbsp;'.$link.'</td></tr>';
  $out.='</table>';	   
  return $out;
}

/**
 * Get the footerline of a form
 *
 */
function getFormFooterline($width="100%") {
  $out = '<table width="'.$width.'" border="0" cellpadding="0" cellspacing="0">';
  $out.= '<tr><td class="line"> </td></tr>';  
  $out.='</table>';	
  return $out;
}


/**
 * Get a button
 *
 * @param string $name
 * @param string $value
 * @param string $style
 * @param string $type
 * @param string $js
 * @param string $form
 */
function getButton($name, $value, $style="navelement", $type="button", $js="", $form="") {
  $out = '<input type="'.$type.'" name="'.$name.'" value="'.$value.'" style="'.$style.'" onClick="'.$js.'"/>';
 return $out;	
}


/**
 * Get a button, which writes its submitaction to a hidden field and then submits the form.
 *
 * @param string $name
 * @param string $value
 * @param string $style
 * @param string $type
 * @param string $js
 * @param string $form
 */
function getActionButton($name, $value, $style="navelement", $type="button", $js="", $form="") {
	$myJS = "return false;";
	if ($type == "submit") {
		$myJS = "document." . $form . "." . $name . ".value = '" . $value . "'; document." . $form . ".submit(); return false;";
	} else if ($this->type == "reset") {
		$myJS = "document." . $form . ".reset(); return false;";
	}
	if ($js != "")
	  $myJS.= $js.';'.$myJS;
	$out = '<input type="'.$type.'" name="'.$name.'" value="'.$value.'" style="'.$style.'" onClick="'.$myJS.'"/>';
 return $out;	
}
		
		
	
/**
 * Writing a central box for displaying content.
 *
 * @param string $content
 */
function cCenter($content) {
  echo '<div class="contentcenter">';
  echo $content;
  echo "</div>";
}
	
/**
 * Writing a Box to the right for displaying content
 *
 * @param string $content
 */
function cRight($content) {
 echo '<div class="contentright">';
 echo $content;
 echo "</div>";
}

/**
 * Writing a Box to the left for displaying content
 *
 * @param string $content
 */
function cLeft($content) {
 echo '<div class="contentleft">';
 echo $content;
 echo "</div>";
}
	
	
	/**
	 * Echos a HTML Line-Break 
	 */
	function br() { 
		echo "<br/>"; 
	}

   /**
    * opens a td 
    * @param string css class
    */
    function td($css="standardlight") {
    	echo "<td class=\"$css\">";
    }
    
    /**
     * closes a td
     */
    function tde() {
      echo "</td>";	
    }
   
   /**
    * closes a table row and opens a new one
    */
   function tr() { echo "</tr><tr>"; }
    
    /**
	 * Echos the header of a table
	 */
	function tableStart($width = "100%", $css = "", $cellpadding = "0", $cellspacing = "0") {
		$out = '<table width="'.$width.'" cellpadding="'.$cellpadding.'" cellspacing="'.$cellspacing.'" border="0" class="'.$css.'">'."\n";
		$out.= '  <tr>'."\n";
		return $out;
	}

	/**
	 *  Echos the end of a table
	 */
	function tableEnd() {return '</tr></table>'; }

	/**
	 * Create a Menü breadcrumb
	 * @param integer MENU_ID
	 */
	function menuBreadCrumb($menu) {
		global $db;

		if ($menu == "0" || $menu == "")
			return "&gt;";

		$result = getDBCell("sitemap", "NAME", "MENU_ID = $menu");
		$parent = getDBCell("sitemap", "PARENT_ID", "MENU_ID = $menu");

		if ($parent != 0 && $parent != "" && $parent != "0" && $parent != $menu)
			$result = backTrail($parent). "&nbsp;&gt;&nbsp;" . $result;

		return $result;
	}
	
	/**
	 * JS for Enable the Waitbox and hide the form
	 * @param string Name of the form to hide.
	 */
	function getWaitupScreen($formname="form1") {
	  $result = "document.getElementById('fo".$formname."').style.display='none'; document.getElementById('waitbox').style.display='block';";	
	  return $result;
	}

    /**
     * Format mySQL-Timestamp
     * @param string MySQL-Timestamp
     */
    function formatDBTimestamp($timestamp) {
       $out="-";
       if ($timestamp != "") $out = substr($timestamp, 0, 4)."-".substr($timestamp, 4, 2)."-".substr($timestamp,6,2)." ".substr($timestamp,8,2).":".substr($timestamp, 10,2);
       return $out;
    }

    /**
	* draws the tag for an image, so you must do nothing.
	* use in backend only, images must be in folder common/img or an
	* subfolder in common/img!
	* @param string Filename of the image
	* @param integer Width of the image
	* @param integer Height of the image
	*/
	function drawImage($name, $width=0, $height=0) {
		global $c;
		if ($width==0 || $height==0) {
		  $output = "<img src=\"" . $c["docroot"] . 'img/' . $name . "\"  border=\"0\">";
		} else {
		  $output = "<img src=\"" . $c["docroot"] . 'img/' . $name . "\" width=\"$width\" height=\"$height\" border=\"0\">";
		}
		return $output;
	}

	/**
	 * draws the tag for an theme-image, so you must do nothing.
	 * use in backend only, images must be in theme-folder  or an
	 * subfolder.
	 * @param string Filename of the image
	 * @param string optional tooltip text
	 */
	function drawThemeImage($name, $tooltip="") {
		global $c;

		$output = "<img src=\"" . $c["docroot"] .'img/'. $name . "\" border=\"0\" title=\"$tooltip\">";
		return $output;
	}

	
	/**
	 * draws the tag for a tree-image, so you must do nothing.
	 * use in backend only, images must be in theme-folder  or an
	 * subfolder.
	 * @param string Filename of the image
	 * @param string optional tooltip text
	 */
	function drawTreeImage($name, $tooltip="") {
		global $c;

		$output = "<img src=\"" . $c["docroot"] .'api/userinterface/tree/images/' . $name . "\" border=\"0\" title=\"$tooltip\">";
		return $output;
	}
	
	/**
	 * draws the tag for an theme-image and a text. image must be in theme-folder/icons and must be of type gif and start with si_
	 * @param string name of the image
	 * @param string Text to display on the button.
	 */
	function iconText($icon, $text) {
		$output = drawImage("icons/si_" . $icon . ".gif"). "&nbsp;&nbsp;" . str_replace(" ", "&nbsp;", $text);

		return $output;
	}

	/**
	 * Replace Whitespaces and - with &nbsp for
	 * @param string Text to do the replace in.
	 */
	function clrSPC($title) { return str_replace(" ", "&nbsp;", str_replace("-", "&nbsp;", $title)); }

	/**
	 * Draw a Link looking like a button
	 * @param string Title of the Button
	 * @param string Link of the Button to point to.
	 */
	function buttonLink($text, $href = "#", $onClick = "") {
		$text = clrSPC($text);

		$ret = "<a href=\"" . $href . "\" class=\"navelement\"";

		if ($onClick != "")
			$ret .= " onClick=\"$onClick\"";

		$ret .= ">$text</a>";
		return $ret;
	}

	/**
	 * draws html for a spacer.
	 * @param integer x-space, you want to insert
	 * @param integer y-space, you want to insert
	 */
	function drawSpacer($width, $height = 1) { return drawImage("ptrans.gif", $width, $height); }

	/**
	 * draws a line
	 * @param integer width of the line
	 * @param integer height of the line
	 */
	function drawLine($width, $height = 1) { return drawImage("pline.gif", $width, $height); }


	/**
	 * Adds an input type hidden field to your html-code.
	 * Use, if you cannot make use of the Hidden-Class for
	 * Forms!
	 * @param string name of hidden-tag
	 * @param string value of hidden-tag.
	 */
	function retain($key, $value = "") { echo "<input type=\"hidden\" name=\"$key\" value=\"$value\">"; }

	/**
	 * Creates the HTML-Code for a link and returns it
	 * @param string $title Title of the link
	 * @param string $href HyperReference of the link
	 * @param string $css CSS-Class of the link
	 */
	function crLink($title, $href, $css = "navelement") {
		$result = "<a href=\"";

		$result .= $href . "\" class=\"$css\">";
		$result .= $title . "</a>";
		return $result;
	}
/**
	 * Creates the HTML-Code for a header-link and returns it
	 * @param string $title Title of the link
	 * @param string $href HyperReference of the link
	 * @param string $css CSS-Class of the link
	 */
	function crHeaderLink($title, $href, $css = "navelement") {
		global $c;
		$result = "<span class=\"h2\"><a href=\"";
		$result .= $c['docroot'].$href . "\" style=\"text-decoration:none;\" class=\"$css\">";
		$result .= '['.$title . "]</a>&nbsp;&nbsp;</span>";
		return $result;
	}

	/**
	 * Writes the css include tag checking if netscape or another browser ist used
	 */
	function echoCSS() {
		global $c;		
		echo "<link rel=stylesheet type=\"text/css\" href=\"" . $c["docroot"] . "css/styles.css\">";
	}

	
	// create backtrail...
	function backTrail($menu) {
		global $db;

		$result = getDBCell("sitemap", "NAME", "MENU_ID = $menu");
		$parent = getDBCell("sitemap", "PARENT_ID", "MENU_ID = $menu");

		if ($parent != 0 && $parent != "" && $parent != "0" && $parent != $menu)
			$result = backTrail($parent). "&nbsp;&gt;&nbsp;" . $result;

		return $result;
	}

?>