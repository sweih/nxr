<?php
	/**********************************************************************
	 * N/X - Web Content Management System
	 * Copyright 2002 Sven Weih, FZI Research Center for Information Technologies
	 * www.fzi.de
	 *
	 * This file is part of N/X.
	 * The initial has been setup as a small diploma thesis (Studienarbeit) at the FZI.
	 * It was be coached by Prof. Werner Zorn and Dipl.-Inform Thomas Gauweiler.
	 *
	 * N/X is free software; you can redistribute it and/or modify
	 * it under the terms of the GNU General Public License as published by
	 * the Free Software Foundation; either version 2 of the License, or
	 * (at your option) any later version.
	 *
	 * N/X is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	 * GNU General Public License for more details.
	 *
	 * You should have received a copy of the GNU General Public License
	 * along with N/X; if not, write to the Free Software
	 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
	 **********************************************************************/

	/**
	 * Base class for all plugins. Use this class, to extend the capabilities
	 * of the N/X WCMS. 
	 * Version 3.0
	 *
	 * @package Plugins
	 */
	class Plugin {
		var $fkid;
		var $cltiid;

		// system handlers. used for installation and deinstallation.
		var $installHandler;
		var $metaInstallHandler;
		var $uninstallHandler;
		var $upgradeHandler;
		var $helpfile;
		
		// system variables
		var $name;
		var $description;
		var $version;

		// configuration Variables
		var $globalConfigPage = "";
		var $globalConfigRoles = "DEVELOPER|ADMINISTRATOR";
		var $isSingleConfig = false;
		var $pluginType = 1;   // 1= Content Plugin, 2= System Extension, 3= CDS-API-Extension., 4= Textfilter-Extension
		var $myLangFile = "";  // Language File, to include.
		var $contentIcon = ""; // Optional: Specify an Icon which will be displayed with the content in future.	
		var $allowLiveAuthoring = true;
		var $allowSearch = false;
		
		// holds config widgets
		var $configWidgets;

		/**
		 * Standard constructor for initializing the Class with the
		 * Primary key ID given by the system.
		 * @params integer Genuine ID (GUID) for accessing one item of the Plugin.
		 */
		function Plugin($fkid, $cltiid=0) {
			$this->fkid = $fkid;
			$this->cltiid = $cltiid;
			
			if ($this->cltiid != 0) {
				$this->loadProperties($this->cltiid);
			}

			global $lang, $c;

			if ($this->myLangFile != "")
				include_once $c["path"] . "plugin/" . $this->myLangFile;
		} // function Plugin($fkid)

		/**
		 * Checks if the record exists
		 */		 
		function exists() {
		  $result = (getDBCell($this->management_table, $this->pk_name, $this->pk_name." = ".$this->fkid) != "");		    
		  return $result;
		}
		 
		/**
		 * Get a option from the table
		 * @param string Name of the option		 
		 */
		 function getOption($name) {
		 	if ($this->cltiid > 0 ) {		    
		 		$result = getDBCell("pgn_config_store", $name, "CLTI_ID = ".$this->cltiid);	
		 		return $result;
		 	} else {
		 	  return "";	
		 	}
		 }
		 
		
		/**
		 * This function is used for drawing the html-code out to the templates.
		 * It just returns the code
		 * @param 		string	Optional parameters for the draw-function. There are none supported.
		 * @returns		string	HTML-CODE to be written into the template.
		 */
		function draw($param = "") { return ""; } // function draw($param="")

		/**
		 * This function is used for drawing the html-code out to the templates in live-authoring mode.
		 * It just returns the code
		 * @param 		string	Optional parameters for the draw-function. There are none supported.
		 * @returns		string	HTML-CODE to be written into the template.
		 */
		function drawLiveAuthoring($param = "") { return $this->draw($param); } // function draw($param="")

		/**
		 * Used for filtering texts in type-4-plugins
		 * This filter is applied before the parsers for images etc. run
		 * @param string Text to parse
		 */
		 function parseText($text) {
		   return $text;
		 }
		  		 		 
		
		/**
		 * Creates the input fields for editing content
		 * @param integer &$form link to the form the input-fields are to be created in 
		 */
		function edit(&$form) {
			// empty, needs to be overwritten
			} // function edit(&$form)

		
		/**
		 * Input-fields for individual configuration are defined here
		 */	
		function editConfig() {
			//
		}
		
		/**
		 * If a plugin needs to add javascript code to the header of a page, then you can return this code
		 * in the getHTMLHeader() method. Please note, that the code will be added only on pages, which use
		 * the plugin type as native content.
		 * Otherwise you have to do this yourself by calling $cds->layout->initPlugin("PluginName")
		 */
		function getHTMLHeader() {
		  return "";
		}
		
		/**
		 * Creates the input fields for editing the individual Configuration.
		 * @param integer &$form link to the form the input-fields are to be created in 
		 */
		function _editConfig(&$form) {
			global $lang;
			if ($this->isSingleConfig) {
				$this->editConfig();
				if (count($this->configWidgets) > 0 ) {
				  $form->add(new Subtitle($st, $lang->get("indiv_config", "Individual configuration for this item"), 2))	;
				}
				
				for ($i=0; $i < count($this->configWidgets); $i++) {
					$form->add($this->configWidgets[$i]);	
				}
				
			}
		} // function editConfig(&$form)
			
		/**
		 * Store the configuration of editConfig
		 */
		 function processConfig() {
		    global $db;
		 	if ($this->isSingleConfig) {
			 	$check = getDBCell("pgn_config_store", "CLTI_ID", "CLTI_ID = ".$this->cltiid);
			 	if ($check=="") {
			 	  $sql = "INSERT INTO pgn_config_store (CLTI_ID) VALUES (".$this->cltiid.")";
			 	  $query = new query($db, $sql);	
			 	}
		 	 	
			 	for ($i=0; $i < count($this->configWidgets); $i++) {
			    	$this->configWidgets[$i]->process();	
		    	}
		    	processSaveSets();
		    }
		 }

		/** 
		 * Used, for painting a preview of the content in the cms. Note, that the data
		 * is to be drawn in a table cell. Therefore you may design any html output, but
		 * you must return it as return value!
		 */
		function preview() {
			// empty, needs to be overwritten
			} // function preview()
                        
                        
                /**
                 * Import data to the plugin
                 * @paran mixed Array with form fieldname => value. To be specified by plugin
                 */
        function import($data) {                
        }
                
        /**
         * Export data to an array.
         * @return mixed Array with form fieldname => value. To be specified by plugin
         */
        function export() {
        }
                
		/**
		* This Function provides all actions for deleting a complete recordset
		* of a plugin. It shoul use the $this->fkid for identifying the record.
		*/
		function deleteRecord() {
			if ($this->pluginType == 1) {
				$deleteHandler = new ActionHandler("DELETE");

				$deleteHandler->addDBAction("DELETE FROM $this->management_table WHERE $this->pk_name = $this->fkid");
				$deleteHandler->process("DELETE");
			}
		} // function deleteRecord()

		/**
		 * Create a version of the selected object
		 * @param integer ID of new Version.
		 * @param boolen specify if property-settings shall be applied to the contents (default) or not.
		 *               properties are usually applied when drawing in dev-state or when launching.
		 */
		function createVersion($newid, $applyProperties=true) {
		  global $db;
			$change[$this->pk_name] = $newid;
			copyRow($this->management_table, "$this->pk_name = $this->fkid", $change);								
			return '';
			
		} // function createVersion($newid)

		/**
		  * Create a new Record with the given $this->fkid in the database.
		  * Initialize with standard values!
		  */
		function createRecord() {
			$createHandler = new ActionHandler("CREATE");
			$createHandler->addDBAction("INSERT INTO $this->management_table ($this->pk_name) VALUES ($this->fkid)");
			$createHandler->process("CREATE");
		}

		/**
		 * Copy this record and all its data to new id.
		 * @param integer id which is used as PK for new record.
		 */
		function copyRecord($newid) { 
			global $db;
			$change[$this->pk_name] = $newid;
			copyRow($this->management_table, "$this->pk_name = $this->fkid", $change);								
			return '';
		}

		/**
		 * returns array with names, which need to be deployed when the plugin is installed
		 */
		function getInstallationFiles() {
		  return array();
		}

        /**
         * returns array with systemfunctions that will be created or deleted.
         */
         function getSystemFunctions() {
           return array();
         }

		/**
		 * Searches for keywords in the plugin.
		 * Used for search method
		 * @param array strings in a 1dim array to search for.
		 * @param array options to concat  AND|OR
		 * @returns array 2d Array with search results and fkid or null if no result found.
		 */
		function search($searchStrings, $searchOptions) { return null; }

		/**
		 * Specifies information for installation and deinstallation of the plugin.
		 */
		function registration() {
			$this->installHandler = new ActionHandler("INSTALL");

			$this->metaInstallHandler = new ActionHandler("METAINSTALL");
			$this->uninstallHandler = new ActionHandler("UNINSTALL");
			$this->upgradeHandler = new ActionHandler("UPGRADE");
		} // function registration()

		/**
		 * DEPRECATED - do not use
		 * retrieves Content-Properties from Database. Internally used only.
		 * @returns array 2d Array with property-name and value
		 */
		function loadProperties($id) {

		}

		/**
		 * DEPRECATED - do not use
		 * configures Content-Properties
		 * @param integer CLCID
		 * @param object form-object
		 * @returns nothing
		 */
		function configureProperties($id, &$form) {

		}


        /**
         * Create system functions
         */
        function _createSystemFunctions() {
          $func = $this->getSystemFunctions();
          foreach ($func as $key=>$value) {
            for ($i=0; $i < count($value); $i++) {
              createSysFunction($value[$i][0], $value[$i][1], $value[$i][2], $key);
            }
          }
        }

        /**
         * Create system functions
         */
        function _removeSystemFunctions() {
          $func = $this->getSystemFunctions();
          foreach ($func as $key=>$value) {
            for ($i=0; $i < count($value); $i++) {
              deleteSysFunction($value[$i][0]);
            }
          }
        }

	/**
	 * Copy necessary files for plugin installation. Interally used only
	 */
	    function _copy_files() {
		    global $c;

            $files = $this->getInstallationFiles();
		    $error = false;

		    for ($i = 0; $i < count($files); $i++) {
			    nxCopy($c["path"] . "plugin/".strtolower($this->name) ."/". $files[$i], $c["livepath"] . "sys/" , $files[$i]);

			    if (!copy($c["path"] . "plugin/".strtolower($this->name)."/". $files[$i], $c["devpath"] . "sys/" . $files[$i]))
				    $error = true;
		    }

		    if ($error == true)
			    echo "Warning: The files could not be copied!";
    	}

	    /**
	    * Delete necessary files. Internally used only.
	    */
	    function _remove_files() {
		    global $c;
    
            $files = $this->getInstallationFiles();
		    $error = false;

		    for ($i = 0; $i < count($files); $i++) {
			   nxDelete($c["livepath"] . "sys/" , $files[$i]);

			    if (!unlink($c["devpath"] . "sys/" . $files[$i]))
				    $error = true;
		    }

    		if ($error == true)
	    		echo "Warning: The files could not be removed!";
	    } 

		/**
		 * Internally used only!
		 * Used to create and and delete Recordsets as they are created and deleted
		 * in the CMS
		 * Uses fkid for identifying
		 */
		function sync() {
			global $db;

			if ($this->pluginType == 1) {
				// checking, if recordset already exists.
				$sql = "SELECT COUNT($this->pk_name) AS ANZ FROM $this->management_table WHERE $this->pk_name = $this->fkid";

				$query = new query($db, $sql);
				$query->getrow();
				$amount = $query->field("ANZ");
				$query->free();

				// if recordset does not exist, then create it.
				if ($amount == 0) {
					$this->createRecord();
				}				
			}
		} // function sync()

		/**
		 * Internally used only!
		 * Installs a Plugin in NX
		 */
		function _installer() {
			global $auth;
			if ($auth->checkPermission("ADMINISTRATOR")) {
				$this->registration();
				$this->installHandler->process("INSTALL");
				$this->metaInstallHandler->process("METAINSTALL");
				$this->_copy_files();
                $this->_createSystemFunctions();
			}
		} // function _installer()

		// not ready yet!!
		/**
		 * Internally used only!
		 * UnInstalls a Plugin in NX
		 */
		function _uninstaller() {
			global $auth;

			if ($auth->checkPermission("ADMINISTRATOR")) {
				$this->registration();

				$this->uninstallHandler->process("UNINSTALL");
				$this->_remove_files();
                $this->_removeSystemFunctions();
			}
		} // function _uninstaller()
	}     // class Plugin

	/**
	 * This function returns if a plugin is installed.
	 * It's used, if a new plugin or a designed website require_onces a certain plugin.
	 * @param 		string	The name of the desired plugin.
	 * @returns		int		the version number if installed, otherwise false
	 */
	function is_plugin_installed($name) {
		global $db;

		$sql = "SELECT VERSION FROM modules WHERE MODULE_NAME = '$name'";

		$query = new query($db, $sql);
		$amount = $query->count();

		if ($amount > 0):
			$query->getrow();

			$version = $query->field("VERSION");
		endif;

		$query->free();
		return (($amount > 0) ? $version : false);
	} // function is_plugin_installed($name)
?>