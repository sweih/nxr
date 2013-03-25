<?
	/**
	  * HTML: Creates an empty Cell. Use for spacers in forms.
	  * @package WebUserInterface
	  */
	class Cell extends WUIObject {

		/**
		  * standard constructor
		  * @param string the name of the Cell. Used internally only.
		  * @param string sets the styles, which will be used for drawing
		  * @param integer $cells Cellspan of the element in Table.
		  * @param integer $width Width of the Cell in Pixel.
		  * @param integer $height Height of the Cell in Pixel.
		  */
		function Cell($name, $style = "standard", $cells = 1, $width = 1, $height = 1) { WUIObject::WUIObject($name, "", "", $style, $cells, $width, $height); }

		/**
		 * Write HTML for the WUI-Object.
		 */
		function draw() {
			$output = WUIObject::std_header();
			$output .= drawSpacer($this->width, $this->height);
			$output .= WUIObject::std_footer();
			echo $output;
			return $this->columns;
		}
	}
	
	
	/**
	 *  Draw a 10px high Spacer
	 *
	 */
	class Spacer extends Cell {
		
		function Spacer($cells) {
		    Cell::Cell('clc', "",$cells, 10, 10);
		}
		
	
	}
?>