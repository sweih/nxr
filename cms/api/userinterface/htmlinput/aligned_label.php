<?
	/**
	 * HTML: Creates an Aligned Label for diplaying texts in a form.
	 * @package WebUserInterface
	 */
	class AlignedLabel extends WUIObject {
		var $align;

		/**
		   * standard constructor
		   * @param string the name of the Label, used internally only.
		   * @param string sets the text that will be displayed
		   * @param string alignment: right, left, center
		   * @param string $style sets the styles, which will be used for drawing
		   * @param integer $cells Cellspan of the element in Table.
		   */
		function AlignedLabel($name, $text, $align = "right", $style = "standard", $cells = 1) {
			WUIObject::WUIObject($name, $text, "", $style, $cells);

			$this->align = $align;
		}

		/**
		 * Write HTML for the WUI-Object.
		 *
		 */
		function draw() {
			$output = $this->std_header();

			$output .= $this->text;
			$output .= WUIObject::std_footer();
			echo $output;
			return $this->columns;
		}

		function std_header() {
			if ($this->width != 0) {
				return "<td width=\"$this->width\" colspan=\"$this->columns\" class=\"$this->style\" valign=\"top\" align=\"" . $this->align . "\">";
			} else {
				return "<td colspan=\"$this->columns\" class=\"$this->style\" valign=\"top\" align=\"" . $this->align . "\">";
			}
		}
	}
?>