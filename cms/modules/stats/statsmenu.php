<?
	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2002 Sven Weih, FZI Research Center for Information Technologies
	 *	www.fzi.de
	 *
	 *	This file is part of N/X.
	 *	The initial has been setup as a small diploma thesis (Studienarbeit) at the FZI.
	 *	It was be coached by Prof. Werner Zorn and Dipl.-Inform Thomas Gauweiler.
	 *
	 *	N/X is free software; you can redistribute it and/or modify
	 *	it under the terms of the GNU General Public License as published by
	 *	the Free Software Foundation; either version 2 of the License, or
	 *	(at your option) any later version.
	 *
	 *	N/X is distributed in the hope that it will be useful,
	 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
	 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	 *	GNU General Public License for more details.
	 *
	 *	You should have received a copy of the GNU General Public License
	 *	along with N/X; if not, write to the Free Software
	 *	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
	 **********************************************************************/

	/**
	* Draws a menubox an adds two dateinputs to the form
	* text input
	* @package WebUserInterface
	*/
	class StatsMenu extends StdMenu {

		var $topSelector = false;
		
		/**
		* standard constructor
		 * @param string Text displayed in the title-bar of the filter box
		* @param Filter reference to a Filter-Object.
		*/
		function StatsMenu() {
			global $lang;
			StdMenu::StdMenu($lang->get("statistics", "Statistics"));
		}

		
		/**
		 * Draw the timeframe selection
		 */
		function draw_timeframemenu() {
			global $lang, $sid, $statsinfo;
			echo getFormHeadline($lang->get("tf", "Time Frame"));
			echo "<tr><td>";
			echo "<table width=\"100%\" cellpadding=\"3\" cellspacing=\"3\" class=\"sidebar_menubox\"><tr><td>";
			echo "<table width='100%' cellpadding='0' cellspacing='0' border='0'>";
			$selected = ($statsinfo->dateview=="predefined")? 'checked': '';
			echo '<tr><td class="bcopy" style="border-bottom:#e0e0e0 solid 1px;"><input type="radio" name="dateview" value="predefined" id="def1" onClick="document.getElementById(\'predef\').style.display=\'block\';document.getElementById(\'individual\').style.display=\'none\';" '.$selected.'>&nbsp;'.$lang->get('predef_time', 'Predefined timeframe').'</td></tr>';
			$visible = ($statsinfo->dateview=="predefined")? '': ' style="display:none" ';
			echo '<tr><td id="predef" '.$visible.'>';
			echo '<table width="100%" border="0" cellpadding="1" cellspacing="0" class="bcopy" style="background-color:#f8f8f8;">';			
			//$checked = ($statsinfo->preDefRange=="total")? 'checked': '';
			//echo '<tr><td>'.drawSpacer(20,1).'<input type="radio" name="itf" value="total" '.$checked.'>'.$lang->get('total', 'Total').'</td></tr>';
			$checked = ($statsinfo->preDefRange=="today")? 'checked': '';
			echo '<tr><td>'.drawSpacer(20,1).'<input type="radio" name="itf" value="today" '.$checked.'>'.$lang->get('today', 'Today').'</td></tr>';
			$checked = ($statsinfo->preDefRange=="yesterday")? 'checked': '';
			echo '<tr><td>'.drawSpacer(20,1).'<input type="radio" name="itf" value="yesterday" '.$checked.'>'.$lang->get('yesterday', 'Yesterday').'</td></tr>';
			$checked = ($statsinfo->preDefRange=="current_week")? 'checked': '';
			echo '<tr><td>'.drawSpacer(20,1).'<input type="radio" name="itf" value="current_week" '.$checked.'>'.$lang->get('thisweek', 'This Week').'</td></tr>';
			$checked = ($statsinfo->preDefRange=="previous_week")? 'checked': '';
			echo '<tr><td>'.drawSpacer(20,1).'<input type="radio" name="itf" value="previous_week" '.$checked.'>'.$lang->get('lastweek', 'Last Week').'</td></tr>';
			$checked = ($statsinfo->preDefRange=="current_month")? 'checked': '';
			echo '<tr><td>'.drawSpacer(20,1).'<input type="radio" name="itf" value="current_month" '.$checked.'>'.$lang->get('thismonth', 'This Month').'</td></tr>';
			$checked = ($statsinfo->preDefRange=="previous_month")? 'checked': '';
			echo '<tr><td>'.drawSpacer(20,1).'<input type="radio" name="itf" value="previous_month" '.$checked.'>'.$lang->get('lastmonth', 'Last Month').'</td></tr>';
			$checked = ($statsinfo->preDefRange=="current_year")? 'checked': '';
			echo '<tr><td>'.drawSpacer(20,1).'<input type="radio" name="itf" value="current_year" '.$checked.'>'.$lang->get('thisyear', 'This Year').'</td></tr>';
			$checked = ($statsinfo->preDefRange=="previous_year")? 'checked': '';
			echo '<tr><td style="border-bottom:#e0e0e0 solid 1px;">'.drawSpacer(20,1).'<input type="radio" name="itf" value="previous_year" '.$checked.'>'.$lang->get('lastyear', 'Last Year').'</td></tr>';
			echo '</table>';
			echo '</td></tr>';
			$selected = ($statsinfo->dateview=="individual")? 'checked': '';
			echo '<tr><td class="bcopy" style="border-bottom:#e0e0e0 solid 1px;"><input type="radio" name="dateview" value="individual" id="def2" onClick="document.getElementById(\'predef\').style.display=\'none\';document.getElementById(\'individual\').style.display=\'block\';" '.$selected.'>&nbsp;'.$lang->get('ind_time', 'Individual timeframe').'</td></tr>';
			$visible = ($statsinfo->dateview=="individual")? '': ' style="display:none" ';
			echo '<tr><td id="individual"'.$visible.'>';
			echo '<table width="100%" border="0" cellpadding="1" cellspacing="0" class="bcopy" style="background-color:#f0f0f0; border-bottom:#e0e0e0 1px solid;">';			
			$ds = new DateBox("startdate", $statsinfo->indStart, "bcopy", 1);
			echo '<tr><td>'.drawSpacer(20,1).'</td>';
			$ds->draw();
			echo '</tr>';
			$ds = new DateBox("enddate", $statsinfo->indEnd, "bcopy", 1);
			echo '<tr><td>'.drawSpacer(20,1).'</td>';
			$ds->draw();
			echo '</tr>';
			echo '</table>';
			echo '</td></tr>';
			echo '<tr><td align="center" style="padding:4px; margin:4px;"><br>';
			$lbi = new ButtonInline("changedate", $lang->get('change','Change'), 'navelement', 'submit', '', 'filter');
			echo $lbi->draw();
			echo '</td></tr>';
			echo "</table>";
			echo "</td></tr></table>";
			echo '<input type="hidden" name="changedate" value="0"><br>';
			echo "</td></tr>";			
		}
		
		/**
		 * writes HTML for the Top-Selector
		 */
		 function draw_topselector() {
		   if ($this->topSelector) {
		   	global $lang, $statsinfo;
		   	echo getFormHeadline($lang->get('top','Limit data'));	
		   	tableStart();
		   	echo "<tr><td>";
		   	$values = array (
  				array (
  					'Show Top 10',
  					10
  				),
  				array (
	  				'Show Top 20',
  					20
  				),
  				array (
  					'Show Top 50',
	  				50
  				),
  				array (
  					'Show Top 100',
  					100
	  			),
  				array (
  					'Show All',
  					2000
  				)
  			);
			echo "<table width=\"100%\" cellpadding=\"3\" cellspacing=\"3\"><tr><td>";
			echo "<table width='100%' cellpadding='3' cellspacing='0' border='0'>";
			echo "<tr>";
			$dropdown = new Dropdown('limit', $values, 'standardwhite', $statsinfo->limit, 110, 1);
			$dropdown->draw();
			echo "</td><td>";
			$lbi = new ButtonInline("changetop", $lang->get('change', 'Change'), 'navelement', 'submit', '', 'filter');
			echo $lbi->draw();
			echo '<input type="hidden" name="changetop" value="">';
			echo "</td></tr>";
			echo "</tr></table>";
			echo "</td></tr></table>";
			echo "</td></tr>";
		   }
		   
		 }
		
		/**
		 * writes HTML for Menu.
		 */
		function draw_contents() {		
			$this->draw_menu();
			br();
			br();
			$this->draw_timeframemenu();
			$this->draw_topselector();
		}
	
			
	}
?>