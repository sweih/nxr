<?
	/**
	 * HTML: Creates a Label for diplaying texts in a form.
	 * @package WebUserInterface
	 */
	class Label extends WUIObject {

		/**
		   * standard constructor
		   * @param string the name of the Label, used internally only.
		   * @param string sets the text that will be displayed
		   * @param string $style sets the styles, which will be used for drawing
		   * @param integer $cells Cellspan of the element in Table.
		   */
		function Label($name, $text, $style = "label", $cells = 1) { WUIObject::WUIObject($name, $text, "", $style, $cells); }

		/**
		 * Write HTML for the WUI-Object.
		 *
		 */
		function draw() {
			$output = WUIObject::std_header();

			$output .= $this->text;
			$output .= WUIObject::std_footer();
			echo $output;
			return $this->columns;
		}
	}
?>