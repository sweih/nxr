<?
  /*
   * Settings of N/X. This is the only file you should edit 
   * within the CMS.
   * @module Configuration
   * @package Management
   */

// base paths
$c_basepath = "%PATH%";
$c_host = "%SERVER%";     // address of your web-server.
$c_basedocroot = $c_host."%DOCROOT%";
 
//available backend languages:
$c_languages[1] = "English";
$c_languages[2] = "Deutsch";
$c_languages[3] = "Mandarin";
$c_languages[4] = "Italian";
$c_languages[5] = "Polish";

$c_lang = %DEFLANG%;	    		// Set default language to English!
 
// Database variables.
$c_dbhost = "%DBSERVER%";	// name of the mysql-Database. Standard-port is used.
$c_database = "%DB%";		// name of the database.
$c_dbuser = "%DBUSER%";			// name of the database user.
$c_dbpasswd = "%DBPASSWD%";		// password of the database user.
        
$c_dbnow = "NOW()";			// for future versions. Do not change.
$c_dbcode = "3dYqpm8UhF";	// for future versions. Do not change.

// CDS Configuration
$c_stdVariation = 1;		// use this variation, if a content is not available in another variation.
$c_pageTracking = %STATS%;	// enable page-statistics here. Experimental!!
$c_siteCaching = %SCACHE%;  	// enable Site-Caching here. Experimental !!
$c_rebuildCache_timeout = 180; 	// max time in seconds the process of rebuilding a site-cache may take
$c_standardEncoding = "text/html; charset=utf-8"; // set standard coding scheme for CDS-Output

// AUTH
$c_disableHostChecking = %HOSTAUTH%; // Disabled auth-check, if user still has same IP or host-address. Required for some firewalls.

// Dynamic Cache
$JPCACHE_ON           =   %DYNCACHE%;   // Turn caching on/off
$JPCACHE_TIME  = 900; // Default number of seconds to cache a page
$JPCACHE_USE_GZIP = 1;   // Whether or not to use GZIP


// Server paths of N/X. These are examples.
// Note: except $c_host all variables must end with a slash!
$c_path = $c_basepath."nx/";          		       // path on your server-drive, where the backoffice is. You may chmod this for execute privs only.
$c_docroot = $c_basedocroot."nx/";                 // docroot of your web-server where nx is located.

// Editing is only necessary, if you changed any paths manually!
$c_templatepath = $c_basepath."templates/";	       // path on your server-drive, where developers put templates
$c_templatedocroot = $c_basedocroot."templates/";  // docroot of your webserver where templates can be located.
$c_pubpath = $c_basepath."www/";		           // path on server-drive, where the launched website will be copied to.
$c_pubdocroot = $c_basedocroot."www/";		       // docroot of your web-server where the launched pages are to be located
$c_uploadpath = $c_basepath."images/";     	       // path on server-drive, where images and other files are to be uploaded to.
$c_uploaddocroot = $c_basedocroot."images/";	   // docroot of your web-server where the uploaded files can be located.
$c_uplpubpath = $c_basepath."www/images/"; 	       // path of the folder on your web-server, where uploaded files are copied to when launching
$c_uplpubdocroot = $c_basedocroot."www/images/";   // docroot of your web-server where the uploaded files are to be stored.
$c_pubcachepath = $c_pubpath."pages/";	           // path on server-drive, where cached sites will be saved in.
$c_pubcacheroot = $c_pubdocroot."pages/";	       // docroot of your web-server where the cached pages are to be located
$c_tmpcachepath = $c_pubcachepath."tmp/";	       // path on server-drive, where the cached pages are to be stored while cache-generation is in progress
$c_dyncachepath = $c_pubcachepath."dyncache/";     // path on server-drive, where dynamic cache is built.
?>