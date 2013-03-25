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

	require $c["path"]."plugin/calendar/phpcal.php";
	
	/**
	 * Calendar PlugIn
	 * Version 1.0
	 *
	 * @package Plugins
	 */
	class pgnCalendar extends Plugin {

		// Name of the Management's Table Primary Key
		var $pk_name = "APID";
        	var $globalConfigPage = "plugin/calendar/edit.php";
		// Name of the Plugin's Management Table. All tables should start with pgn_
		var $management_table = "pgn_cal_appointment";
       	var $pluginType = 3; //CDS-API-Extension
       	var $globalConfigRoles = "CALENDAR";
       	var $helpfile = "calendar/help.pdf";
       	
		/**
		   * This function is used for drawing the html-code out to the templates.
		   * It just returns the code
		   * @param 		string	Optional parameters for the draw-function. There are none supported.
		   * @return		string	HTML-CODE to be written into the template.
		   */
		function draw($param = "") {
			return new CDS_Calendar($param["calendar"]);
		}

        /**
         * Define function tree that will be created...
         */
        function getSystemFunctions() {
           return array("PLUGINS_M" => array(array("CALENDAR", "Calendar", "Calendar Plugin")), "CALENDAR" => array(array("CALENDAR_CREATE", "Administration", "Create Calendars and Categories"), array("CALENDAR_EDIT", "Edit", "Edit the calendar")));
        }
		/**
		   * Specifies information for installation and deinstallation of the plugin.
		   */
		function registration() {
			global $auth;
            		$this->name = "Calendar";
            		$this->description = "CDS-API-Extension for creating calendars.";
            		$this->version = 1;
			if ($auth->checkPermission("ADMINISTRATOR")) {
				Plugin::registration();
				$mtid = nextGUID(); // getting next GUID.
                		$this->installHandler->addDBAction("CREATE TABLE `pgn_cal_appointment` (  `APID` bigint(20) NOT NULL default '0',  `CALID` bigint(20) NOT NULL default '0',  `CATID` bigint(20) NOT NULL default '0',  `TITLE` varchar(255) NOT NULL default '',  `DESCRIPTION` longtext default NULL,  `STARTDATE` date NOT NULL default '0000-00-00',  `STARTTIME` time NOT NULL default '00:00:00',  `ENDDATE` date NOT NULL default '0000-00-00',  `ENDTIME` time NOT NULL default '00:00:00',  `IMAGE` bigint(20) default NULL,  `LINK` bigint(20) default NULL, `VERSION` tinyint(4) NOT NULL default '0',  PRIMARY KEY  (`APID`)) TYPE=MyISAM;");
                		$this->installHandler->addDBAction("CREATE TABLE `pgn_cal_calendars` (  `CALID` bigint(20) NOT NULL default '0',  `NAME` varchar(64) NOT NULL default '',  PRIMARY KEY  (`CALID`),  UNIQUE KEY `NAME` (`NAME`)) TYPE=MyISAM;");
                		$this->installHandler->addDBAction("CREATE TABLE `pgn_cal_categories` (  `CATID` bigint(20) NOT NULL default '0',  `CALID` bigint(20) NOT NULL default '0',  `NAME` varchar(64) NOT NULL default '',  `DESCRIPTION` varchar(255) default NULL, `COLOR` varchar(8) NOT NULL default '#ffffff',  PRIMARY KEY  (`CATID`)) TYPE=MyISAM;");
                		$id1 = nextGUID();
                		$this->installHandler->addDBAction("INSERT INTO `pgn_cal_calendars` (`CALID`, `NAME`) VALUES (".$id1.", 'My Calendar');");
                		$this->installHandler->addDBAction("INSERT INTO `pgn_cal_categories` (`CATID`, `CALID`, `NAME`, `DESCRIPTION`, `COLOR`) VALUES (".nextGUID().", $id1, 'Event', 'Events are public. All visitors are welcome.', '#f0f0f0');");
                		$this->uninstallHandler->addDBAction("DROP TABLE `pgn_cal_categories`");
                		$this->uninstallHandler->addDBAction("DROP TABLE `pgn_cal_calendars`");
                		$this->uninstallHandler->addDBAction("DROP TABLE `pgn_cal_appointment`");
                		$this->uninstallHandler->addDBAction("DELETE FROM temp_vars WHERE NAME='calsel'");
				global $source, $classname; // the source path has to be provided by the calling template
				$modId = nextGUID();
				$this->installHandler->addDBAction("INSERT INTO modules (MODULE_ID, MODULE_NAME, DESCRIPTION, VERSION, MT_ID, CLASS, SOURCE, MODULE_TYPE_ID) VALUES ($modId, '$this->name', '$this->description', $this->version, $mtid, '$classname', '$source', 3);");
			}
		}
	}
	
	/**
	 * CDS-Class for retrieving information of the calender-plugin
	 */ 
	class CDS_Calendar {
	
		var $calendar = "";
		var $calendarId = 0;
		var $dateFormat = "d.m.Y";
		var $timeFormat = "H:i \h";
		
		/**
		 * Standard constructor
		 * @param string name of the caleder
		 */
		function CDS_Calendar	($calendar) {		  
		  session_register("calendarid");
		  $this->calendar = strtoupper($calendar); 	
		  if ($this->calendar != "") 
		    $this->calendarId = getDBCell("pgn_cal_calendars", "CALID", "UPPER(NAME) = \"".$this->calendar."\"");		 
		}
		
		
		
		/**
		 * Returns a list with all the calendars in the system
		 */
		function getCalendars($orderBy="ORDER BY NAME DESC") {			
			return createNameValueArrayEx("pgn_cal_calendars", "NAME", "CALID", "1", $orderBy);			
		}
		
		/**
		 * Draws a dropdown to select one calendar and tries to get the calendarId of the active Calendar.
		 */
		function drawCalendarSelector($selectButton = "Go") {
			global $calendarid;
			$calId = value("calendar", "NUMERIC", "0");
			$calendars = $this->getCalendars();
			
			if ($calId == '0') {
			  if (isset($_SESSION["calendarid"])) {			  	
			  	$calId = $_SESSION["calendarid"];
			  } else {
				$calId = $calendars[0][1];				
			  }
			}
			  
			$_SESSION["calendarid"] = $calId;
			
			echo '<form name="calendarform" method="POST">';
			echo '<select name="calendar" id="calendar">';
			for ($i=0; $i < count($calendars); $i++) {
				if ($calId == $calendars[$i][1]) {
					$selected = ' selected';
				} else {
					$selected = '';
				}
				echo '<option value="'.$calendars[$i][1].'" '.$selected.'>'.$calendars[$i][0].'</option>';
			}
			echo '</select>&nbsp;';						
			echo '<input type="submit" name="setcal" value="'.$selectButton.'">';
			echo '</form>';
			$this->setCalendar($calId);
			return $calId;
		}
		
		/**
		 * Set the ID of the actual calendar
		 * @param integer ID of the actual calendar
		 */
		function setCalendar($calId) {
			 $this->calendarId = $calId; 
		}
		
		/**
		 * Retrieve the Categories of the Calendar
		 */
		function getCategories() {
		  global $db, $cds;

		  $result = array();
		  $sql = "SELECT * FROM pgn_cal_categories WHERE CALID = ".$this->calendarId;
		  $query = new query($db, $sql);
		  while ($query->getrow()) {
		  	$category = array("NAME" => $query->field("NAME"), "DESCRIPTION" => $query->field("DESCRIPTION"), "CATID" => $query->field("CATID"), "COLOR" => $query->field("COLOR"));	
		  	array_push($result, $category);
		  }
		  $query->free();
		  return $result;
		}
		
		/**
		 * Retrieve all Appointements/Events of the Calendar in given time and/or category
		 * @param array Categories to select or "ALL"
		 * @param string startdate or all dates starting by now if left empty
		 * @param string enddate of query or no end if left empty
		 * @param string starttime of the query or 00:00:00 if left empty
		 * @param string endtime Endtime of the query or 23:59:59 if left empty
		 * @param number limit of the events.
		 */
		function getEvents($categories = "ALL", $startdate="", $enddate="", $starttime="", $endtime="", $limit=50) {
		  global $db, $cds;
		  
		  $result = array();
		  $variation = value("v");
		  if ($variation == "0") $variation = $cds->variation; 
		  if ($startdate=="") $startdate = date("Y-m-d");
		  if ($starttime=="") $starttime = "00:00:00";
		  if ($endtime == "") $endtime = "23:59:00";
		  
		  $whereClause = "";
		  if ($this->calendarId != -1)
		    $whereClause.="CALID = ".$this->calendarId." AND ";
		  
		  $whereClause.= "STARTDATE >= '$startdate' AND STARTTIME >= '$starttime' AND ENDTIME <= '$endtime'";
		  if ($enddate != "") $whereClause.= " AND ENDDATE <= '$enddate'";
		  if ($categories != "ALL" && is_array($categories)) {
		     $catsel = "";
		     for ($i=0; $i < count($categories); $i++) {
			if ($i != 0) $catsel.=" OR ";
			$catsel.= " CATID = ".$categories[$i];		     			     	
		     }	
		     $whereClause.= " AND ($catsel)";
		  }
		  
		 
		  $whereClause.=" ORDER BY STARTDATE, STARTTIME ASC LIMIT 0,$limit";
		  $sql = "SELECT * FROM pgn_cal_appointment WHERE ".$whereClause;
		  $query = new query($db, $sql);
		  $parser = new NX2HTML($variation);
		  while ($query->getrow()) {
		  		$compareDate = date('Y-m-d', strtotime($query->field("STARTDATE")));
			    $date = date('Y-m-d');
			    $over = $compareDate < $date;
		  		$event = array(	"TITLE" => $query->field("TITLE"), 
		  					"DESCRIPTION" => applyFilterPlugins($parser->parseText($query->field("DESCRIPTION"))), 
		  					"STARTDATE" => date('d.m.Y', strtotime($query->field("STARTDATE"))), 
		  					"STARTTIME" => substr($query->field("STARTTIME"),0,5), 
		  					"ENDDATE" => date('d.m.Y', strtotime($query ->field("ENDDATE"))), 
		  					"ENDTIME" =>  substr($query->field("ENDTIME"),0,5), 
		  					"APID" => $query->field("APID"), 
		  					"REPORT" => applyFilterPlugins($parser->parseText($query->field("REPORT"))), 
		  					"GALLERY" => $query->field("GALLERY"), 
		  					"OVER" => $over,
		  					"CATID" => $query->field("CATID"));	
		  	array_push($result, $event);
		  }
		  $query->free();
		  return $result;	 	
		}
					
		/**
		 * Automatically determine clicks created with CalendarObject.
		 * returns null if no events or array with data if event-day clicked
		 */
		function dispatchCalendar() {
		  $result = null;
		  if(value("day", "NUMERIC") != "0") {
		  	$month = value("month", "NUMERIC");
		  	$day = value("day", "NUMERIC");
		  	$year = value("year", "NUMERIC");		  	  			  	
		  	$result = createDBCArray("pgn_cal_appointment", "APID", "CALID = ".$this->calendarId." AND STARTDATE <= '$year-$month-$day' AND ENDDATE >= '$year-$month-$day' ORDER BY STARTDATE, STARTTIME");	  	
		  }
		  return $result;	
		}
		
		/**
		 * returns the data for an event
		 * @param integer ID of the event
		 */
		 function getEventData($eventId) {
			global $db, $cds;
			$variation = value("v");
		    if ($variation == "0") $variation = $cds->variation;
			$sql = "SELECT * FROM pgn_cal_appointment WHERE APID = ".$eventId;
		  	$query = new query($db, $sql);
		  	$parser= new NX2HTML($variation);
		  	while ($query->getrow()) {
		  		$compareDate = date('Y-m-d', strtotime($query->field("STARTDATE")));
			    $date = date('Y-m-d');
			    $over = $compareDate < $date;
		  		$event = array(	"TITLE" => $query->field("TITLE"), 
		  					"DESCRIPTION" => applyFilterPlugins($parser->parseText($query->field("DESCRIPTION"))), 		  					
		  					"STARTDATE" => date('d.m.Y', strtotime($query->field("STARTDATE"))), 
		  					"STARTTIME" => substr($query->field("STARTTIME"),0,5), 
		  					"ENDDATE" => date('d.m.Y', strtotime($query ->field("ENDDATE"))), 
		  					"ENDTIME" =>  substr($query->field("ENDTIME"),0,5), 
		  					"UNIXSTART" => $query->field("STARTDATE") .' '. $query->field("STARTTIME"), 
		  					"UNIXEND" => $query->field("ENDDATE") .' '. $query->field("ENDTIME"), 
		  					"APID" => $query->field("APID"), 
		  					"REPORT" => applyFilterPlugins($parser->parseText($query->field("REPORT"))), 
		  					"GALLERY" => $query->field("GALLERY"), 
		  					"OVER" => $over,
		  					"CATID" => $query->field("CATID"));			  					
			  }
			  			  
			$query->free();
			return $event;
		 }
		
		/**
		 * Draw an overview of dates by the given array created wether by dispatchCalenar or getEvents.
		 * @param mixed Event-data input.
		 * @param boolean Want to display time?
		 * @param boolean Want to display end-Date and time?
		 * @param string CSS-Style class for Event-Title
		 * @param string CSS-Style class for Event-Description
		 * @param string CSS-Style class for Links
		 */
		function drawEventData($events, $displayTime=true, $displayEndDate=false,$titleStyle="", $descriptionStyle="", $dateStyle="", $linkStyle="") {
			global $cds;
			if (count($events) > 0) {
				echo '<table width="100%" cellpadding="0" cellspacing="0" border="0">'."\n";
				for ($i=0; $i < count($events); $i++) {				   	
					$data = $this->getEventData($events[$i]);
				   	$color = getDBCell("pgn_cal_categories", "COLOR", "CATID = ".$data["CATID"]);
					if ($color != "") {
			    		 	$color = "background-color:$color;";	
			   	    	}
			   				
			   		echo '<tr><td style="'.$color.'" valign="top">'."\n";
			   		$dispTime = "";			   		
			   		var_dump($data);
			   		$startTS = strtotime($data["STARTDATE"]);
					$endTS = strtotime($data["ENDDATE"]);
			   		$startT = strtotime($data["STARTTIME"]);
					$endT = strtotime($data["ENDTIME"]);
			   		
			   		$dispTime = date($this->dateFormat, $startTS);
			   		if ($displayTime && ($startT != $endT || $startTS != $endTS) && $startT!=0) {
			   			$dispTime.=" ".date($this->timeFormat, $startT);
			   		} 
			   		$dash = false;
			   		if ($displayEndDate && ($startTS != $endTS)) {
			   		  	$dispTime.= " - ".date($this->dateFormat, $endTS);
			   		  	$dash = true;
			   		}
			   		if ($displayEndDate && $displayTime  && ($startTS != $endTS || $startT != $endT)) {
			   			if (! $dash) $dispTime.=" - ";
			   			$dispTime.=" ".date($this->timeFormat, $endT);
			   		}
			   		echo '<span class="'.$dateStyle.'">'.$dispTime.': </span>'."\n";		   		
			   		echo '<span class="'.$titleStyle.'">'.$data["TITLE"].'</span>'."\n";
			   		br();
			   		if (is_array($data["IMAGE"])) {
			 			echo '<span style="float:left;">';
			 			echo $cds->layout->getImageTag($data["IMAGE"], false);	
			 			echo '</span>';
			   		}
			   		echo '<span class="'.$descriptionStyle.'">'.$data["DESCRIPTION"].'</span>'."\n";			   				
			   		br();
			   		if (is_array($data["LINK"])) {
			   			echo '<span>';
			   			echo $cds->layout->getLinkTag($data["LINK"], $linkStyle);
			   			echo '</span>';
			   		}
			   		echo '</td></tr>';
			   		echo '<tr><td colspan="2" style="background-color:#ffffff;">'.drawSpacer(2,2)."</td></tr>\n";
				}
				echo '</table>'."\n";
			}
		}
		
		/**
		 * Draws the calendar of the given month in given year.
		 * @param array categories or all to select all categories
		 * @param string month as number 1-12
		 * @param string year in format YYYY
		 */
		function getCalendarObject($month, $year, $categories="ALL") {
			if (isset($_GET["month"])) {
				$calendarObj = new phpCal($_GET["month"], $_GET["year"]);
				$month = $_GET["month"];
				$year = $_GET["year"];
			} else { 
				$calendarObj = new phpCal($month, $year);
			}
			$events = $this->getEvents($categories, "01-$month-$year", "31-$month-$year"); 	
			$eventsCleaned=array();
			for ($i=0; $i<count($events); $i++) {
				$startTS = strtotime($events[$i]["STARTDATE"]);
				$endTS = strtotime($events[$i]["ENDDATE"]);
				for ($j=$startTS; $j <= $endTS; $j+= 86400) {
					array_push($eventsCleaned, str_replace("-", "/", date("m-d-Y", $j)));				
				}
			}
			$calendarObj->setLinks(doc(), doc());
			$calendarObj->setEventDays($eventsCleaned);
			return $calendarObj;			
		}
		 
		
	}
?>