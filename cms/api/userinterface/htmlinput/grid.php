<?
	/**
	 * HTML: Creates a Grid for displaying any other WUI Elements.
	 * @package WebUserInterface
	 */
	class NXGrid extends WUIObject {
		var $contents = null;

		var $cols = 0;
		var $rows = "0";
		var $border = "0";
		var $defaultClass = "grid";
		var $spacers = array ();
		var $colTitles;

		/**
		   * standard constructor
		   * @param string the name of the Grid, used internally only.
		   * @param string $style sets the styles, which will be used for drawing
		   * @param integer $cells Cellspan of the element in Table.
		   */
		function NXGrid($name, $columns = 2, $style = "standard", $cells = 2) {
			WUIObject::WUIObject($name, "", "", $style, $cells);

			$this->cols = $columns;
		}

		/**
		  * Set the Title Row
		  * @param array array of string with row headers
		  */
		function addTitleRow($content) {
			if (is_array($content)) {				
				if (count($content) == $this->cols) {				
					$this->colTitles = $content;
				}
			}
		}
		
		/**
		  * Add a new row to the Grid.
		  * @param array array with exactly #columns Elements of DBO or WUI
		  */
		function addRow($content) {
			if (is_array($content)) {				
				if (count($content) == $this->cols) {				
					for ($i = 0; $i < count($content); $i++) {
						$this->contents[$this->rows][$i] = $content[$i];
						//$this->contents[$this->rows][$i]->style = "gridelement";
					}

					$this->rows++;
				}
			}
		}

		/**
		  * Setup the Grid and draw spacers.
		  * @param $spacers array of integer values in pixel with spacers.
		  */
		function setRatio($spacers) { $this->spacers = $spacers; }

		/**
		   * Draw the Grid
		   */
		function draw() {			
			echo '<td colspan="' . $this->columns . '">';
			echo '<table width="100%" cellspacing="0" border="0" align="center">';
			echo '<tr>';
			
			// draw Headers
			for ($i=0; $i < count($this->colTitles); $i++) {          		
           		echo "<td class=\"gridtitle\" $style valign=\"middle\">".$this->colTitles[$i]."</a></td>";
         	}
         	echo "</tr>\n";
         	
			// draw the form
			for ($i = 0; $i < $this->rows; $i++) {
  			   echo "<tr class=\"grid\" onMouseOver='this.style.backgroundColor=\"#ffffcc\";' onMouseOut='this.style.backgroundColor=\"#e8eef7\";'>";				
				for ($j = 0; $j < $this->cols; $j++) {
					echo "<td style=\"padding:4px; border-bottom:1px solid #cccccc;\">";
					echo tableStart("100%", "label", "0", "0");					
					$this->contents[$i][$j]->draw();
					echo tableEnd();
					echo tde();
				}
				
				
				echo "</tr>";
			}

			echo '</table>';
			echo '</td>';			
			return $this->columns;
		}
	}
?>