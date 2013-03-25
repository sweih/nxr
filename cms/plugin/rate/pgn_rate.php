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
	 * Rate PlugIn
	 * Version 1.0
	 *
	 * @package Plugins
	 */
	class pgnRate extends Plugin {

		// Name of the Management's Table Primary Key
		var $pk_name = "SOURCEID";
        var $globalConfigPage = "plugin/rate/rate_results.php";
		// Name of the Plugin's Management Table. All tables should start with pgn_
		var $management_table = "pgn_rate";
        var $pluginType = 3; //CDS-API-Extension
        
		/**
		   * This function is used for drawing the html-code out to the templates.
		   * It just returns the code
		   * @param 		string	Optional parameters for the draw-function. There are none supported.
		   * @return		string	HTML-CODE to be written into the template.
		   */
		function draw($param = "") {
			return new CDS_Rate($param["id"]);
		}
	

		/**
		 * returns array with names, which need to be deployed when the plugin is installed
		 */
		function getInstallationFiles() {
		  return array("rate0.gif", "rate1.gif", "rater.php");
		}
		
		/**
		   * Specifies information for installation and deinstallation of the plugin.
		   */
		function registration() {
			global $auth;	
			if ($auth->checkPermission("ADMINISTRATOR")) {
				Plugin::registration();
				$this->name = "Rate";
				$this->description = "CDS-API-Extension for rating items.";
				$this->version = 1;
				$mtid = nextGUID(); // getting next GUID.
				$this->installHandler->addDBAction("CREATE TABLE `pgn_rating` (  `RATINGID` bigint(20) NOT NULL auto_increment,  `SOURCEID` bigint(20) NOT NULL default '0',  `VOTE` tinyint(4) NOT NULL default '0',  `COMMENT` text NOT NULL,  `timestamp` timestamp(14) NOT NULL,  PSEUDO varchar(64) null, EMail varchar(128) null, Published tinyint(1) not null default 0, PRIMARY KEY  (`RATINGID`)) TYPE=MyISAM AUTO_INCREMENT=1 ;");
				$this->uninstallHandler->addDBAction("DROP TABLE `pgn_rating`");
				global $source, $classname; // the source path has to be provided by the calling template
				$modId = nextGUID();
				$this->installHandler->addDBAction("INSERT INTO modules (MODULE_ID, MODULE_NAME, DESCRIPTION, VERSION, MT_ID, CLASS, SOURCE, MODULE_TYPE_ID) VALUES ($modId, '$this->name', '$this->description', $this->version, $mtid, '$classname', '$source', 3)");
			}
		}
	}
	
	/**
	 * CDS-API for Rate-Plugin
	 */
	 class CDS_Rate {
	 
	   var $sourceId = 0;
	   
	   /**
	    * standard constucor
	    */
	   function CDS_Rate($sourceId) {
	     $this->sourceId = $sourceId;
	   }
	   
	   /**
	    * Draw the link which opens the rating form.
	    * @param string title of the link
	    */
	   function drawForm($title='Write a review now' ) {
          global $cds;
		   $out= '<a href="'.$cds->docroot.'sys/rater.php?source='.$this->sourceId.'" target="_blank">'.$title.'</a>';          
           return $out;
	   }
	 
	   /**
	    * Draw result list
	    */
	   function drawRating($average="Average Rating", $count="Ratings") {
	      $out = '<table class="rate_table">'."\n";
	      $out.= '<tr>';
	      $out.= '<td valign="middle" class="rate_label">'.$average."</td>\n";
	      $data = $this->getData();
	      for ($i=0; $i< $data["average"]; $i++) {
	         $out.= '<td valign="middle" class="rate_label"><img src="sys/rate1.gif" border="0" width="16" height="16"></td>'."\n";
	      }
	      for ($i=0; $i< (10 - $data["average"]); $i++) {
	         $out.= '<td valign="middle" class="rate_label"><img src="sys/rate0.gif" border="0" width="16" height="16"></td>'."\n";
	      }
	      $out.= '<td valign="middle" class="rate_label">('.sprintf("%01.2f",$data["average"]).")</td>\n";
	      $out.= '<td colspan="11" class="rate_copy"><img src="sys/ptrans.gif" border="0" height="5" width="25">'."</td>\n";
	      $out.= '<td valign="middle" class="rate_label">'.$count.": ".$data["ratings"]."</td>\n";
	      $out.= '</tr>';
	      $out.= '</table>'."\n";    
	      return $out;
	   }
	 
		function drawComments() {
			$data = $this->getComments(20);
			$out = '';
			for ($i=0; $i<count($data); $i++) {
				$name = $data[$i]["name"];
				if ($name=="") $name = "Anonym";
				$out.='<b>Kommentar von '.$name.' am  '.$data[$i]["date"].':</b><br>';
				$out.=$data[$i]["comment"].'<br><br>';
			}
			return $out;
		}
   
	   /**
	    * Returns array with rated data.
	    * ["ratings"] # of ratings
	    * ["average"] average rating
	    */
	   function getData() {
	        global $db;
	        $sql = "SELECT AVG(VOTE) AS CALC FROM pgn_rating WHERE SOURCEID = ".$this->sourceId;
	        $query = new query($db, $sql);
	        if ($query->getrow()) 
	          $out["average"] = $query->field("CALC");
	   
	        $sql = "SELECT COUNT(VOTE) AS CALC FROM pgn_rating WHERE SOURCEID = ".$this->sourceId;
	        $query = new query($db, $sql);
	        if ($query->getrow()) 
	          $out["ratings"] = $query->field("CALC");
	        $query->free();
	        return $out;
	   }
	   
	   /**
	    * Get comments last posted
	    * @param integer Number of Posts to limit+
	    */
	   function getComments($limit=5) {
	     global $db;
	     $result = array();
	     $sql= "SELECT COMMENT, PSEUDO, DATE_FORMAT(timestamp, '%d.%m.%Y') as time1 FROM pgn_rating WHERE COMMENT <> '' and Published=1 AND SOURCEID=".$this->sourceId."  ORDER BY TIMESTAMP DESC LIMIT ".$limit;
	     $query = new query($db, $sql);
	     while ($query->getrow()) {
	       $result[] = array("comment" => $query->field("COMMENT"),"name"=>$query->field('PSEUDO'), "date" => $query->field("time1"));
	     }
	     
	     $query->free();
	     return $result;
	   }
	 }

?>