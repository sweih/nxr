<?
	/**
	  * HTML: Creates an empty Cell. Use for spacers in forms.
	  * @package WebUserInterface
	  */
	class IDWrapper extends WUIObject {

		var $obj = null;
		var $payload = "";
		
		/**
		  * standard constructor
		  * @param string the name of the Wrapper. Used in id-Attribute
		  * @param mixed DBO or WUI-Object to draw.
		  * @param string sets the styles, which will be used for drawing
		  * @param string Add additional payload to the tag. for styles or so on.
		  * @param integer $cells Cellspan of the element in Table.
		  */
		function IDWrapper($name, $obj, $style = "standard", $payload="", $cells = 1) { 
			WUIObject::WUIObject($name, "", "", $style, $cells);
			$this->obj = $obj;
			$this->payload = $payload; 
		}
	
		/**
		 * checking
		 */
		 function check() {
		   $this->obj->check();
		 }
		
		/**
		 * processing
		 */
		function process() {
		  $this->obj->process();	
		}

		/**
		 * Write HTML for the WUI-Object.
		 */
		function draw() {
			$output = "<td colspan=\"$this->columns\" class=\"$this->style\" valign=\"top\" id=\"".$this->name."\" ".$this->payload.">";
			$output.= '<table width="100%" cellpadding="0" cellspacing="0" border="0"><tr>';
			echo $output;
			$this->obj->draw();
		    $output = '</tr></table>';
			$output.= "</td>";
			echo $output;
			return $this->columns;
		}
	}
?>