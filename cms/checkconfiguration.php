<?php
/**********************************************************************
 *	N/X - Web Content Management System
 *	Copyright 2004 Sven Weih
 *
 *      $Id: checkconfiguration.php,v 1.1 2004/10/13 15:00:05 sven_weih Exp $ *
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
 ?>
 <html>
 <head>
 <title>N/X WCMS Configuration Check</title>
 <style type="text/css">
   body {
   	font-family : Tahoma,Verdana,Arial,Sans;
   	font-size: 10pt;
   	font-color: #333333;
   } 
 </style>
 </head>
 <body>
 
 <?php
   require_once "api/tools/dircheck.php";
   require_once "api/tools/docrootcheck.php";
   $c["checkconfig"] = true;

   echo "<h1>N/X WCMS configuration check</h1>";
   echo "<h2>Checking directory configuration</h2>";
  ?>
  <b>Hint for permission configruation:</b><br/>
    cd /usr/www/htdocs/nxhomefolder/<br/>
	chown -R wwwrun:nogroup .<br/>
    find . -type d -print0 | xargs -0 chmod 700<br/>
    find . -type f -print0 | xargs -0 chmod 600<br/>
    chmod -R u+rwX {cms/cache,cms/api/tools,wwwdev,www}<br/><br/>
  
  <?php
   require_once "config.inc.php"; 
   
   if ($simpleconfig) {
	   echo "Using simple config mode:<br><br>";
	   echo "<li>CMS-Path (c[basepath], c[cmspath]): ";
	   $check = new DirCheck($c["path"], "700");
	   
	   echo "<li>Databasesupportpath: ";
	   $check = new DirCheck($c["path"]."api/tools", "777");
	   
	   echo "<li>CMS-Cache-Path: ";   
	   $check = new DirCheck($c["path"]."cache", "777");
	   
	   echo "<li>Live-Path (c[livehomepage]): ";
	   $check = new DirCheck($c["livepath"], "777");
	   
	   echo "<li>Live-Files-Path: ";
	   $check = new DirCheck($c["livefilespath"], "777");
	  
	   echo "<li>Live-System-Path: ";
	   $check = new DirCheck($c["livepath"]."sys", "777");
	     
	   echo "<li>Live-Cache: ";
	   $check = new DirCheck($c["cachepath"], "777");
	   
	   echo "<li>Live-Temp-Path: ";
	   $check = new DirCheck($c["tmpcachepath"], "777");
	   
	   echo "<li>Dynamic-Cache-Path: ";
	   $check = new DirCheck($c["dyncachepath"], "777");
	    echo "<br>";
	   echo "<h2>Checking Server Docroot configuration</h2>";
	?>
	Please check the variables and paths for unwanted double slashes (//) also. This might get worse! <br/><br/>	   
	<?php   
		
	   echo "<li>Checking CMS-Docroot (c[host], cms[basedocroot], c[cmsfolder]): ";
	   $check = new DocrootCheck($c["host"].$c["docroot"]);
	   
	   echo "<li>Checking DEV-Docroot (c[tempfolder]): ";
	   $check = new DocrootCheck($c["host"].$c["devdocroot"]);

	   echo "<li>Checking DEV-Uploads-Docroot (c[tempfolder]): ";
	   $check = new DocrootCheck($c["host"].$c["devfilesdocroot"]);

	   echo "<li>Checking LIVE-Docroot (c[livefolder]): ";
	   $check = new DocrootCheck($c["host"].$c["livedocroot"]);

	   echo "<li>Checking LIVE-Uploads-Docroot (c[livefolder]): ";
	   $check = new DocrootCheck($c["host"].$c["livefilesdocroot"]);	     	   	
	   
	   echo "<li>Checking  Static HTML Cache Docroot (c[cachedocroot]): ";
	   $check = new DocrootCheck($c["host"].$c["cachedocroot"]);	   	   
   }
   
   if ($expertconfig) {
	   echo "Using expert config mode:<br><br>";
	   echo "<li>CMS-Path (c[basepath], c[cmspath]): ";
	   $check = new DirCheck($c["path"], "700");
	   
	   echo "<li>Databasesupportpath: ";
	   $check = new DirCheck($c["path"]."api/tools", "777");
	   
	   echo "<li>CMS-Cache-Path: ";   
	   $check = new DirCheck($c["path"]."cache", "777");
	   
	   echo "<li>Live-Path (c[livehomepage]): ";
	   $check = new DirCheck($c["livepath"], "777");
	   
	   echo "<li>Live-Files-Path: ";
	   $check = new DirCheck($c["livefilespath"], "777");
	  
	   echo "<li>Live-System-Path: ";
	   $check = new DirCheck($c["livepath"]."sys", "777");
	     
	   echo "<li>Live-Cache: ";
	   $check = new DirCheck($c["cachepath"], "777");
	   
	   echo "<li>Live-Temp-Path: ";
	   $check = new DirCheck($c["tmpcachepath"], "777");
	   
	   echo "<li>Dynamic-Cache-Path: ";
	   $check = new DirCheck($c["dyncachepath"], "777");
	
	   echo "<br>";
	   echo "<h2>Checking Server Docroot configuration</h2>";
	?>
	Please check the variables and paths for unwanted double slashes (//) also. This might get worse! <br/><br/>	   
	<?php   
		
	   echo "<li>Checking CMS-Docroot (c[cmshost], cms[docroot]): ";
	   $check = new DocrootCheck($c["docroot"]);
	   
	   echo "<li>Checking DEV-Docroot (c[devhost], cms[devdocroot]): ";
	   $check = new DocrootCheck($c["devhost"].$c["devdocroot"]);

	   echo "<li>Checking DEV-Uploads-Docroot (c[devfilesdocroot]): ";
	   $check = new DocrootCheck($c["devhost"].$c["devfilesdocroot"]);

	   echo "<li>Checking LIVE-Docroot (c[devhost], cms[livedocroot]): ";
	   $check = new DocrootCheck($c["livehost"].$c["livedocroot"]);

	   echo "<li>Checking LIVE-Uploads-Docroot (c[livefilesdocroot]): ";
	   $check = new DocrootCheck($c["livehost"].$c["livefilesdocroot"]);	     	   	
	   
	   echo "<li>Checking  Static HTML Cache Docroot (c[cachedocroot]): ";
	   $check = new DocrootCheck($c["livehost"].$c["cachedocroot"]);
   }
   

 ?>
 </body>
 </html>