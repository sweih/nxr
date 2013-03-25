<?
	/**
	 * HTML: Creates a Label for diplaying texts in a form.
	 * @package WebUserInterface
	 */
	class Subtitle extends WUIObject {

		/**
		   * standard constructor
		   * @param string the name of the Label, used internally only.
		   * @param string sets the text that will be displayed
		   * @param integer $cells Cellspan of the element in Table.
		   */
		function Subtitle($name, $text, $cells = 2) { WUIObject::WUIObject($name, $text, "", "", $cells); }

		/**
		 * Write HTML for the WUI-Object.
		 *
		 */
		function draw() {
			$dr3 = new Label("lbl", $this->text, "headbox", $this->columns);
			echo "<tr>";
			$dr3->draw();
			echo "</tr>";

			return $this->columns;
		}
	}
?>