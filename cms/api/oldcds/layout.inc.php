<?
	/**
	 * @module Layout
	 * @package CDS
	 */

	/**
	 * draws html for a spacer.
	 * @param integer x-space, you want to insert
	 * @param integer y-space, you want to insert
	 */
	function Spacer($width, $height = 1) {
		global $c;

		$output = "<img src=\"" . $c["livefilesdocroot"] . "ptrans.gif\" width=\"$width\" height=\"$height\" border=\"0\">";
		echo $output;
	}

	/**
	 * Adds an input type hidden field to your html-code.
	 * Use, if you cannot make use of the Hidden-Class for
	 * Forms!
	 * @param string name of hidden-tag
	 * @param string value of hidden-tag.
	 */
	function saveHidden($key, $value) { echo "<input type=\"hidden\" name=\"$key\" value=\"$value\">"; }
?>