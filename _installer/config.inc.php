<?
	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2002-2007 Sven Weih, 
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

	// Database variables.
	$c["dbhost"] = "%DBSERVER%";  				// name of the mysql-Database. Standard-port is used.
	$c["database"] = "%DB%";   					// name of the database. 
	$c["dbuser"] = "%DBUSER%";       				// name of the database user.
	$c["dbpasswd"] = "%DBPASSWD%";         			// password of the database user.
	$c["dbdriver"] = "mysql";    				// type of your database. Do not change.
	$c["dbnow"] = "NOW()";       				// for future versions. Do not change.
	$c["dbcode"] = "3dYqpm8UhF"; 				// for future versions. Do not change.
	
	
	// Backend language
	$c_lang = "%DEFLANG%";                				// Set default backend-language to English! 
												// Recommended even if you use other languages
	//Page after Login
	$c["pageAfterLogin"] = "modules/sitepages/sitepagebrowser.php"; // Page which will be displayed after login.
												
												
	// Website configuration (CDS)
	$c["stdvariation"] = 1;                     // use this variation (language), if a content is not available 
												// in another variation. You can retrieve the ID from database table variations
												// Defaul: standard.
	$c["timeout"] = 360;                        // max time in seconds the process of rebuilding a site-cache may take
	$c["standardencoding"] = "%ENCODING%"; // set standard encoding scheme for the Website

	// Static Cache (generate html-files)
	$c["renderstatichtml"] = %SCACHE%;              // enable Rendering of Static HTML-Code (Caching)

	// Dynamic Cache
	$JPCACHE_ON = %DYNCACHE%;    					// Turn dynamic page caching on/off
	$JPCACHE_TIME = 3600;   						// Default number of seconds to cache a page
	$JPCACHE_USE_GZIP = 0; 						// Whether or not to use GZIP for transmission of the page to a browser

	// CDS-Query-Cache
	$QUERY_CACHE = false; 						// Cache database queries done for rendering the website.
	$QUERY_CACHE_TIME = 900; 					// Default number of seconds to cache a query

	
	// CDS Configuration

	
  	// statistics
  	$c["pagetracking"] = %STATS%;                   // enable page-statistics
  	$c["usewebbug"] = true;					 	 // Use Webbug, do not log implicitly (no referers are logged)
  	$c["trackexitpages"] = true;				 // Modify links, so that exit pages are tracked.
  	$c["usettf"] = false;					     // Do not use ttf-fonts for analysis. TTF makes problems on windows

	// Syndication
	$c["provider"] = "localhost";				 // this is used for exchanging content or data. 
	
	// Email
	$c["webmastermail"] = 'webmaster@mail.address';
	
	// Do not change when it has been setup once!
	
	// AUTH
	$c["disalbehostchecking"] = %HOSTAUTH%; 			 // Disabled auth-check, if user still has same IP or host-address. 
												 // Required for some firewalls and proxy servers.

	// ImageMagic Configuration
	$c["useimagemagick"] = false;				 // switch image-magick on or off. Switch on only, if you set the correct path!
	$c["imagemagickpath"] = "";

	// User-Interface;
	$c["disableClustersInTree"] = false;		 // Disable cluster-node-view in the tree in Content->Cluster. Set to true for bigger sites.
	
	$c["theme"] = "grey";					 // Take look into the cms/api/userinterface/themes-folder for available backend themes.
   // other installed themes are "standard" and "light" and "grey"
    

											    // to use a directory, which is not accessible from the webserver!!!!
	$c["storeBackupFTP"] = false;                // Enable, if backup should be stored via ftp.
	$c["ftpUsername"] = '';
	$c["ftpPassword"] = '';
	$c["ftpServer"] = '';
	$c["ftpDir"] = '/';
	$c["ftpPort"] = '21';
	$c["deleteAfterFTP"] = false;                // Delete the backup-file locally after ftp-upload?

	
	// ATTENTION! You need to comment out either the simpleconfig or the expert config part of
	// this file. We would recommend using simple config when you start first. You have to change nothing
	// then
	
	/**
	 * Simple path configuration
    */
	$simpleconfig = true;
	
	// base paths
	// attention: please read the readme file to know, which paths must be set writable! 
	// (all www and wwwdev + subfolders)
	$c["basepath"] = "%PATH%/";				// path of your nx-home-folder on harddrive. ends with slash.
	$c["basedocroot"] = "%DOCROOT%";		// docroot on your webserver, which corresponds 
											// to the path you just entered. starts and ends with a slash!
	$c["host"] = "%SERVER%"; 				// address of your web-server. Ends without slash
	$c["cmsfolder"] = "cms";					// name or relative path of the folder cms, if renamed.
    $c["temphomepage"] = "wwwdev";				// name of relative path the folder wwwdev, if renamed.	
	$c["livehomepage"] = "www";					// name of relative path the folder www, if renamed.	
												// set to "" if you moved all www-files to the nx-homefolder

												
	// attention: whereever the www folder is, you will need these childfolders in it:
   // [www]/pages
   // [www]/pages/tmp												
	// [www]/images
    		
	////
	////  automatic simple path configuration. change nothing here. Comment out if you use expert config.
	//// 	
	$c["path"] = $c["basepath"] . $c["cmsfolder"]."/"; 
	$c["spmthumbpath"] = $c["path"] . "modules/sitepages/thumbnails/";  
	$c["docroot"] = $c["basedocroot"] . $c["cmsfolder"]."/";
	$c["spmthumbdocroot"] =$c["docroot"] . "modules/sitepages/thumbnails/";    
	$c["devpath"] = $c["basepath"] . $c["temphomepage"]."/";
	$c["devdocroot"] = $c["basedocroot"] .$c["temphomepage"] ."/";
	$c["devfilespath"] = $c["devpath"] . "images/";                  
	$c["devfilesdocroot"] = $c["devdocroot"] . "images/";            
	$c["livepath"] = $c["basepath"] .$c["livehomepage"] ."/";                                
	$c["livedocroot"] = $c["basedocroot"] . $c["livehomepage"]. "/";                          
	$c["hostlivedocroot"] = $c["host"].$c["livedocroot"];
	$c["docroothtml"] = $c["livedocroot"];												
	$c["livefilespath"] = $c["livepath"] . "images/";                
	$c["livefilesdocroot"] =  $c["livedocroot"]."images/"; 
	$c["cachepath"] = $c["livepath"]."pages/";                           
	$c["cachedocroot"] = $c["livedocroot"]."pages/";                     
	$c["tmpcachepath"] = $c["cachepath"] . "tmp/";
	$c["dyncachepath"] = $c["cachepath"] . "tmp/"; 
	$c["themepath"] = $c["path"] . "api/userinterface/themes/".$c["theme"]."/";
	$c["themedocroot"] = $c["docroot"] . "api/userinterface/themes/".$c["theme"]."/";      	          
	// end auto config.
	/**
	 * End of simple config
	 */
	
	/**
	 Experts path configuration	 		
	 
	$expertconfig = false;
	
	// note: All variables except of the hosts must end with a slash!
	// All docroots start and end with a slash!
	
	// CMS-Backend Paths
	$c["cmshost"] = "http://jupiter";					 // Hostname, where cms-folder can be found
	$c["cmspath"] = "c:/web/homepage/cms/";					 // Path of cms-folder on harddrive
	$c["cmsdocroot"] = "/homepage/cms/";					 // folder on webserver (host), where cms can be found
		
	// Develepmont homepage paths
	$c["devhost"] = "http://jupiter";					 // Hostname, where your wwwdev-folder can be found			
	$c["devpath"] = "c:/web/homepage/wwwdev/";				 // path to your dev-folder on harddrive
	$c["devdocroot"] = "/homepage/wwwdev/"; 				 // folder on webserver (host), where wwwdev can be found
	$c["devfilespath"] = $c["devpath"] . "images/";		 // path of the upload-folder on harddrive
	$c["devfilesdocroot"] = $c["devdocroot"] . "images/";// upload-folder on your webserver
	
	// Livehomepage paths
	// edit also www/sys/exit.php and image.php to correct the relative paths to this file!!
	$c["livehost"] = "http://jupiter";					 // Hostname, where your www-folder can be found
	$c["livepath"] = "c:/web/homepage/";				 // path to your www-folder on harddrive
	$c["livedocroot"] = "/homepage/";	 				 // folder on webserver (host), where www can be found
	$c["docroothtml"] = $c["livedocroot"];				 // Sometimes you may want to replace the links and images with another path. Set this here. 
	$c["livefilespath"] = $c["livepath"] . "images/";	 // path of your upload folder
	$c["livefilesdocroot"] = $c["livedocroot"] . "images/";// server docroot of your upload folder
	$c["cachepath"] = $c["livepath"] . "pages/";		 // path of your static html-cache on harddrive
	$c["cachedocroot"] = $c["livedocroot"] . "pages/";   // docroot of your static html-cache on webserver.
	$c["tmpcachepath"] = $c["cachepath"] . "tmp/";		 // path of temp-cache on harddisk
	$c["dyncachepath"] = $c["cachepath"] . "tmp/";		 // path of dynamic-cache on harddisk	
	
	
	////
	////  Auto configuration for expert config. Change nothing.
	////
   	$c["path"] = $c["cmspath"];  	
   	$c["docroot"] = $c["cmshost"].$c["cmsdocroot"];
   	$c["hostlivedocroot"] = $c["livehost"].$c["livedocroot"];
   	$c["spmthumbpath"] = $c["cmspath"] . "modules/sitepages/thumbnails/";
	$c["spmthumbdocroot"] = $c["cmsdocroot"] . "modules/sitepages/thumbnails/";
	$c["themepath"] = $c["cmspath"] ."api/userinterface/themes/".$c["theme"]."/";
	$c["themedocroot"] = $c["cmsdocroot"] .	"api/userinterface/themes/".$c["theme"]."/";	
	//// End of Autoconfig.
		
    /**
     * End of experts configuration
     */	
	
	// Backup
	$c["backupPath"] = "c:/web/nxhp/";	       // Folder where the backup script shall start. Use your nx-root-folder for standard installation
	$c["backupStore"] = "c:/backups/"; // Directory, where backups shall be stored. You should stronly consider
	
	
	/*************************************************************************************************
	 *     !!!!!!!!!!!!!!!!!!!!!!Do not modify anything beyond this point !!!!!!!!!!!!!!!
	 *************************************************************************************************/
	if (!$c["checkconfig"]) {
		
	require_once $c["path"]."deployment.inc.php";	
	
	 // Server configuration
	set_magic_quotes_runtime(0);
	$c_magic_quotes_gpc = get_magic_quotes_gpc(); //disable magic quotes.

	// load modules.	
	require_once $c["path"] . "api/tools/datatypes.php";
	require_once $c["path"] . "plugin/plugin.inc.php";
	require_once $c["path"] . "api/database/lib.inc.php";
	require_once $c["path"] . "ext/adodb/session/adodb-session2.php";
	require_once $c["path"] . "api/auth/lib.inc.php";
	require_once $c["path"] . "api/userinterface/form/pagestate.php";
	require_once $c["path"] . "api/common/lib.inc.php";
	require_once $c["path"] . "api/common/initialize.php";	
	require_once $c["path"] . "plugin/plugin.inc.php";
	require_once $c["path"] . "api/tools/lib.inc.php";
	require_once $c["path"] . "api/userinterface/lib.inc.php";
	require_once $c["path"] . "api/cms/lib.inc.php";
	require_once $c["path"] . "api/common/prepare.php";
   	require_once $c["path"] . "api/xml/lib.inc.php";
   	require_once $c["path"] . "api/parser/lib.inc.php";
	

	$lang = new lang();
	
	} // permitconfig
?>