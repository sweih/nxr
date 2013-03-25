<?
	/**
	 * HTML: Creates a Link for Linking pages
	 * @package WebUserInterface
	 */
	class LinkLabel extends WUIObject {
		var $href;

		var $target;
		/**
		   * standard constructor
		   * @param string the name of the Label, used internally only.
		   * @param string sets the text that will be displayed
		   * @param string $style sets the styles, which will be used for drawing
		   * @param integer $cells Cellspan of the element in Table.
		   */
		function LinkLabel($name, $text, $href, $target = "_self", $style = "standard", $cells = 1) {
			global $c;

			WUIObject::WUIObject($name, $text, "", $style, $cells);
			$this->href = $c["docroot"] . $href;
			$this->target = $target;
		}

		/**
		 * Write HTML for the WUI-Object.
		 *
		 */
		function draw() {
			$output = "<td colspan=\"$this->columns\" class=\"" . $this->style . "\" valign=\"top\" align=\"right\">";

			$output .= '<a href="' . $this->href . '" target="' . $this->target . '" style="align:right;" class="' . $this->style . '">' . $this->text . '&gt;&gt;</a>';
			$output .= WUIObject::std_footer();
			echo $output;
			return $this->columns;
		}
	}
?>