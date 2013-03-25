<?
	/**
	  * HTML: Creates a separator line.
	  * @package WebUserInterface
	  */
	class Separator extends Cell {

		/**
		  * standard constructor
		  * @param string sets the styles, which will be used for drawing
		  * @param integer $cells Cellspan of the element in Table.
		  */
		function Separator($style = "separator", $cells = 2, $width = 540, $height = 2) { Cell::Cell("sep", $style, $cells, $width, $height); }
	}
?>