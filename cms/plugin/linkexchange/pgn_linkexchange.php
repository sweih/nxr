<?
	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2007 Sven Weih, FZI Research Center for Information Technologies
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
	 * Link Exchange Mashup
	 * Version 1.0
	 *
	 * @package Plugins
	 */
	class pgnLinkExchange extends Plugin {
		
	  var $pluginType = 5;
	  var $globalConfigPage = "plugin/linkexchange/overview.php";
	  var $globalConfigRoles = "ADMINISTRATOR|LINKEXCHANGE";
						

		
		/**
		   * This function is used for drawing the html-code out to the templates.
		   * It just returns the code
		   * @param 		string	Optional parameters for the draw-function. There are none supported.
		   * @return		string	HTML-CODE to be written into the template.
		   */
		function draw($param = "") {			
			global $cds, $db;
			$label = getDBCell("pgn_config_store", "TEXT1", "CLTI_ID=".$this->fkid);
			echo "<br><h2>$label</h2>";
			$label = getDBCell("pgn_config_store", "TEXT3", "CLTI_ID=".$this->fkid);
			echo $label;
			br(); br();
			// draw the linklist...
			$sql = "Select * FROM pgn_linkexchange WHERE APPROVED=1 AND SOURCEID=".$cds->pageId. " ORDER BY INSERTTIMESTAMP DESC";
			$query = new query($db, $sql);
			$counter = 0;
			while ($query->getrow()) {			  			  
			  $title = $query->field("TITLE");
			  $description = $query->field("DESCRIPTION");
			  $url = $query->field("URL");
			  echo '<b><a href="'.$url.'" target="_blank">'.$title.'</a></b><br>';
			  echo $description;
			  br();
			  echo '<a style="font-size:11px;text-decoration:none;" href="'.$url.'" target="blank"><span style="color:#008000;">'.$url.'</span></a>';
			  br();
			  br();
			}
			
			
			
				echo '<script language="javascript" type="text/javascript">
	<!--
	var win=null;
	
	function NewWindow2(mypage,myname,w,h,scroll,pos){
		if(pos=="random")
			{LeftPosition=(screen.width)?Math.floor(Math.random()*(screen.width-w)):100;
			TopPosition=(screen.height)?Math.floor(Math.random()*((screen.height-h)-75)):100;}
		if(pos=="center")
			{LeftPosition=(screen.width)?(screen.width-w)/2:100;
			TopPosition=(screen.height)?(screen.height-h)/2:100;}
	
		else if((pos!="center" && pos!="random") || pos==null){LeftPosition=0;TopPosition=20}
	settings=\'width=\'+w+\',height=\'+h+\',top=\'+TopPosition+\',left=\'+LeftPosition+\',scrollbars=\'+scroll+\',location=no,directories=no,status=no,menubar=no,toolbar=no,resizable=yes\';
	win=window.open(mypage,myname,settings);}
	// -->
	</script>';
		$label = getDBCell("pgn_config_store", "TEXT2", "CLTI_ID=".$this->fkid);
		
	 	echo '<a class="breadcrumb" href="#" onclick="NewWindow'."('".$cds->docroot."sys/linkexchange/addurl.php?id=".$cds->pageId."','addurl','600','450','no','center');return false".'" onfocus="this.blur()">'.$label.'</a>&nbsp;&nbsp;';	
		
			
		}	
		
		/**
		 * Set the configuration-widgets for a cluster-content item.
		 */
		function edit(&$form) {
			global $lang;
			$form->add(new Subtitle("st", $lang->get("config", "Configuration")));
			$form->add( new TextInput($lang->get("title", "Title"), "pgn_config_store", "TEXT1", "CLTI_ID = ".$this->cltiid, "type:text,size:256,width:300"));		
			$form->add( new TextInput($lang->get("entrylink", "Add URL Link text"), "pgn_config_store", "TEXT2", "CLTI_ID = ".$this->cltiid, "type:text,size:256,width:300"));		
			$form->add( new TextInput($lang->get("intro", "Introduction"), "pgn_config_store", "TEXT3", "CLTI_ID = ".$this->cltiid, "type:text,size:256,width:300"));		
		}
		
		    /**
         * Define function tree that will be created...
         */
        function getSystemFunctions() {
           return array("PLUGINS_M" => array(array("LINKEXCHANGE", "Linkexchange Administration", "Linkexchange Plugin")));
        }
            

								    	  

		/**
		   * Specifies information for installation and deinstallation of the plugin.
		   */
		function registration() {
			global $auth;

			// Authentification is require_onced for changing system configuration. Do not change.
			if ($auth->checkPermission("ADMINISTRATOR")) {

				// parent registration function for initializing. Do not change.
				Plugin::registration();

				// Name of the Plugin. The name will be displayed in the WCMS for selection
				$this->name = "Linkexchange";
				// A short description, what the Plugin is for.
				$this->description = "Enables a visitor to place reciprocal links on his website.";
				// Version of the plugin. Use integer numbers only. Is important for future releases.
				$this->version = 1;

				// Every module can have its own and individual META-Data in NX. The following Handler is
				// for creating a META-Data-Template and for assigning it to the Plugin.
				// IF you do not want to declare an individual META-Scheme, then set $mtid=0 and delete
				// everything between del1 and /del1!


				/**** change nothing beyond this point ****/
				global $source, $classname; // the source path has to be provided by the calling template
				$modId = nextGUID();
				$mtid  = nextGUID();	      
				$this->installHandler->addDBAction("CREATE TABLE `pgn_linkexchange` (`ID` BIGINT NOT NULL , `SOURCEID` BIGINT NOT NULL, `TITLE` VARCHAR( 256 ) NULL ,`URL` VARCHAR( 256 ) NULL ,`DESCRIPTION` VARCHAR( 1024 ) NULL ,`INSERTTIMESTAMP` DATETIME NOT NULL ,`EMAIL` VARCHAR( 128 ) NULL ,`KEYWORDS` VARCHAR( 1024 ) NULL ,`USERNAME` VARCHAR( 64 ) NULL ,`PASSWORD` VARCHAR( 64 ) NULL ,`RECIPROCALURL` VARCHAR( 256 ) NULL,APPROVED TINYINT(1) NOT NULL DEFAULT 0 ,PRIMARY KEY ( `ID` )) ENGINE = MYISAM ;");
				
				$this->installHandler->addDBAction("INSERT INTO modules (MODULE_ID, MODULE_NAME, DESCRIPTION, VERSION, MT_ID, CLASS, SOURCE, MODULE_TYPE_ID) VALUES ($modId, '$this->name', '$this->description', $this->version, $mtid, '$classname', '$source', $this->pluginType)");
			}
		}
	}
?>