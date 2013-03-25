<?

	/**
	  * @package WebUserInterface
	  * splits one form into two optical forms.
	  */
	class FormSplitter {
		var $title;

		var $icon;
		var $width = 560;
		/**
		  * standard constructor
		  * @param string Title of the second form
		  * @param string icon of the second form
		  */
		function FormSplitter($title, $icon) {
			$this->title = $title;

			$this->icon = $icon;
		}

		/**
		  * internal
		  */
		function process() {
			//empty
			}
		/** For Down-Grade-Compatibility only **/
		function proccess() { $this->process(); }

		/**
		  * draw the splitter
		  */
		function draw() {
			echo "</td></tr>\n</table>\n</td></tr>\n</table></td></tr></table><br>\n";

			echo "<table width=\"" . ($this->width + 2) . "\" class=\"border\" cellpadding=\"1\" cellspacing=\"0\">\n<tr><td>\n";
			echo "<table width=\"" . $this->width . "\" class=\"white\" cellpadding=\"0\" cellspacing=\"0\">\n";
			echo "<tr><td width=\"20\" class=\"formtitle\" cellpadding=\"0\" cellspacing=\"0\">";

			if ($this->icon != "") {
				echo drawImage($this->icon, 20, 20);
			} else {
				echo drawSpacer(20, 20);
			}

			echo "</td><td width=\"" . ($this->width - 20) . "\" class=\"formtitle\" cellpadding=\"0\" cellspacing=\"0\">$this->title</td></tr>\n<tr><td colspan=\"2\" class=\"white\">\n";
			echo "<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
			echo "<tr>";
			$cl1 = new Cell("cl1", "border", 1, ceil($this->width / 3), 1);
			$cl2 = new Cell("cl2", "border", 1, ceil(($this->width / 3) * 2), 1);
			$cl1->draw();
			$cl2->draw();
			echo "</tr>";
		}

		function check() {
			//empty
			}
	}
?>