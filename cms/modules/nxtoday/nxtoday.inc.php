<?
	/**
	 * N/X Today-Functions
	 * @module NXToday
	 * @package Tools
	 */

	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2004 Fabian König, fabian@nxsystems.org
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
	 
	 require_once "../../config.inc.php";
	 
	class NXToday extends WUIInterface {
		var $QuickLinks;
		var $untranslatedPages;
		
		function NXToday() {
			$this->QuickLinks = new NXToday_QuickLinks();
			$this->untranslatedPages = new NXToday_UntranslatedPages();
		}

		/**
		 * draws NXToday-Output
		 */
		function draw() {
			echo '<table width="1000" cellpadding="4" cellspacing="2" broder="0">';
			echo '<tr>';
			// 1. Spalte
			echo '<td width="33%" valign="top">';
			
			echo '</td>';
			// 2. Spalte
			echo '<td width="33%" valign="top">';
			echo $this->drawABox('Bookmarks', $this->QuickLinks->draw());
			echo '</td>';
			// 3.Spalte
			echo '<td width="33%" valign="top">';
			  // Datum.
			  
			  echo '<h2>'.date('D Y-M-d H:i').'</h2>';
			  echo 'Week: '.date('W');
			echo '</td>';
			echo '</tr>';
			echo '</table>';
		}
		
   
  /**
   * Get the HTML Output for a box in the cockpit.
   */
  function drawABox($headline, $content) {
    echo getFormHeadline($headline);
    echo $content;
    echo getFormFooterLine();
    br();
    br();    
  }
		
	}
	
	
	
	/**
	 * Creates a list of pages which are potentially untranslated.
	 */
	class NXToday_UntranslatedPages {
		
		var $pages;
		
		/**
		 * Standard constructor
		 */
		function NXToday_UntranslatedPages() {
		  $this->getUntranslatedPages();			  				
		}
		
		/**
		 * Determine, which pages have not been translated yet.
		 */
		function getUntranslatedPages() {
		  	global $db, $c;
		  	$this->pages = array();
		  	$sql1 = "SELECT cv.CLNID, cv.LAST_CHANGED, sp.SPID FROM cluster_variations cv, sitepage sp WHERE sp.CLNID = cv.CLNID AND cv.VARIATION_ID =". $c["stdvariation"] ." AND LAST_CHANGED IS  NOT  NULL";
		  	$query = new query($db, $sql1);
		  	while ($query->getrow()) {
		  	  $clnid = $query->field("CLNID");
		  	  $lc = $query->field("LAST_CHANGED");	
			  $sql2 = "SELECT VARIATION_ID FROM cluster_variations WHERE LAST_CHANGED < $lc AND CLNID = $clnid AND VARIATION_ID <> ".$c["stdvariation"]." AND DELETED=0"; 	  		
			  //echo $sql2;
			  $query2 = new query($db, $sql);
			  while ($query2->getrow()) {
			  	 $variation = $query2->field("VARIATION_ID");
			  	 $this->pages[] = array($query->field("SPID"), $variation);
			  }
			  $query2->free();
		  	}	  			  				  
		}
		
		/**
		 * Draw links to pages
		 */
		function draw() {
	      global $c, $sid;
		  $output = "";
	      //echo "there we are..";
	      //var_dump($this->pages);
		  for ($i=0; $i<count($this->pages); $i++) {
	      	$spid = $this->pages[$i][0];
		  	$menuId = getDBCell("sitepage", "MENU_ID", "SPID = ".$spid);
			$spname = getDBCell("sitemap", "NAME", "MENU_ID = $menuId");
			
			$output.= "<a href=\"".$c["docroot"]."modules/sitepages/sitepagebrowser.php?oid=$spid&jump=1&sid=$sid"."\">".$spname."</a>";
	      }   
	      return $output;
		}
		
	}
	
	
	
	class NXToday_QuickLinks {
		var $types;
		var $SPLinks;
		var $ChannelLinks;
		
		function NXToday_QuickLinks() {
			$this->types = array("SPLinks", "ChannelLinks");
			$this->SPLinks = array();
			$this->ChannelLinks = array();
		}
		
		/**
		 * adds a new Element to the current NXToday-QuickLinks.
		 * @param Object Element to add to NXToday-QuickLinks
		 */
		function add(&$element) {
			switch ($element->type) {
				case "SPLink":
					array_push($this->SPLinks, $element);
					break;
				case "ChannelLink":
					array_push($this->ChannelLinks, $element);
					break;
			}
		}
		
		/**
		 * draws the QuickLinks of the current NXToday-Object.
		 * @param String optional specifies the Type of NXToday-Elements to draw. SPLink and ChannelLink are currently supported.
		 * @returns String output of QuickLinks
		 */
		function draw($type="") {
			switch ($type) {
				case "SPLink":
					$output .= "	<tr>\n";
					$output .= "		<td class=\"nxtoday_title\">Sitepages</td>\n";
					$output .= "	</tr>\n";
					for ($i=0; $i<count($this->SPLinks); $i++) {
						$output .= "	<tr>\n";
						$output .= "		<td class=\"nxtoday_element\">";
							$output .= $this->SPLinks[$i]->draw();
							$output .= "</td>\n";
						$output .= "	</tr>\n";
					}
					break;
				case "ChannelLink":
					$output .= "	<tr>\n";
					$output .= "		<td class=\"nxtoday_title\">Channels</td>\n";
					$output .= "	</tr>\n";
					for ($i=0; $i<count($this->ChannelLinks); $i++) {
						$output .= "	<tr>\n";
						$output .= " 		<td class=\"nxtoday_element\">";
							$output .= $this->ChannelLinks[$i]->draw();
							$output .= "</td>\n";
						$output .= "	</tr>\n";
					}
					break;
				case "":
					$output = "<table border=\"0\" width=\"100%\">\n";
					$output .= $this->draw("SPLink");
					$output .= $this->draw("ChannelLink");
					$output .= "</table>\n";
					break;
			}
			return $output;
		}
	}
	
	class NXToday_Element {
		var $type;
		var $output;
		
		function NXToday_Element() { }
		
		/**
		 * returns output of NXToday-Element
		 */
		function draw() { 
			return $this->output;
		}
		
	}
	
	class NXToday_SPLink extends NXToday_Element {
			
		function NXToday_SPLink($spid) {
			global $c, $sid;
			
			$this->type = "SPLink";
			
			$menuId = getDBCell("sitepage", "MENU_ID", "SPID = $spid");
			$spname = getDBCell("sitemap", "NAME", "MENU_ID = $menuId");
			
			$this->output = "<a href=\"".$c["docroot"]."modules/sitepages/sitepagebrowser.php?oid=$spid&jump=1&sid=$sid"."\">".$spname."</a>";
		}

	}
	
	class NXToday_ChannelLink extends NXToday_Element {
		
		function NXToday_ChannelLink($chname) {
			global $c, $sid;
			
			$this->type = "ChannelLink";
			
			$chid = getDBCell("channels", "CHID", "UPPER(NAME) = UPPER('$chname')");
			
			$this->output = "<a href=\"".$c["docroot"]."modules/channels/overview.php?chsel=$chid&jump=1&sid=$sid"."\">Channel \"".$chname."\"</a>";
		}
		
			
	}
	
?>