<?php
/**********************************************************************
 *	phpScriptInstaller
 *	Copyright 2003-2006 Sven Weih (sven@weih.de)
 *
 *	This file is part phpScriptInstaller
 *
 *	phpScriptInstaller is free software; you can redistribute it and/or modify
 *	It under the terms of the GNU General Public License as published by
 *	the Free Software Foundation; either version 2 of the License, or
 *	(at your option) any later version.
 *
 *	phpScriptInstaller is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU General Public License for more details.
 *
 *	You should have received a copy of the GNU General Public License
 *	along with phpScriptInstaller; if not, write to the Free Software
 *	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 **********************************************************************/
 
 require "_installer/classes_wui.php";
 require "_installer/classes_db.php";
 
 $installer = new Installer("_installer/styles.css");
 $installer->setTitle("N/X 5.0");
 $installer->setTitleHTML("N/X 5.0 Instalation");
 
 $page1 = new Page("Introduction");
 $page1->addWidget(new Information("", "./_installer/introduction.txt"));
 
 $page2 = new Page("License Agreement");
 $page2->addWidget(new Label("The GNU General Publich License (GPL)", "bold"));
 $page2->addWidget(new LicenseAgreement("./_installer/license.txt"));

 
 $page3 = new Page("Create Database");
 $page3->addWidget(new CreateMYSQL("./sql/demohomepage.sql"));
 
  
 // set dropdowns...
 $onOff[0]["name"] = "On";
 $onOff[0]["value"] = "true";
 $onOff[1]["name"] = "Off";
 $onOff[1]["value"] = "false";
 
 $languages = array( array("name" => "English", "value" => "EN"), array("name" => "German", "value" => "DE"));
 
 $page4 = new Page("System Configuration");
 $cfp = new ConfigFileParser();
 $cfp->addFileToParse("./_installer/config.inc.php", "./cms/config.inc.php");
 
 
 $cfp->addWidget(new Label("Path Configuration", "headline2"));
 $cfp->addWidget(new Path("Path on drive, where N/X is installed, e.g. c:/Web/nx4", "PATH", ""));
 $cfp->addWidget(new WebServer("Address of your Webserver, e.g. http://localhost", "SERVER"));
 $cfp->addWidget(new Docroot("Docroot of the system, e.g. /nx4/", "DOCROOT", ""));
 $cfp->addWidget(new Label( "<br>", "standard"));
 
 
 $cfp->addWidget(new Label("Caching", "headline2"));
 $cfp->addWidget(new Label("Static caching creates HTML-Files on Demand, which are always present and only expire, when one expires them in backend. Dynamic cache is built on demand. When a user request a page, the cache checks if it is still buffered (15 minutes default). If so, the cached page is sent to the user. If not, the page is rendered, stored and sent afterwards.<br><br>"));
 $cfp->addWidget(new SelectBox("Static Caching: <br>(Default = Off)", "SCACHE", "false", $onOff));
 $cfp->addWidget(new SelectBox("Dynamic Caching: <br>(Default = On)", "DYNCACHE", "true", $onOff));
 $cfp->addWidget(new Label( "<br>", "standard"));
 
 $cfp->addWidget(new Label("Tools & Security", "headline2"));
 $cfp->addWidget(new Label("Traffic statics are used for recording page impressions and visits. Host authorization is used when authorizing a user. Once a session is created, it is locked to a host. Sometimes this gives you trouble, especially if you are behind a firewall using multiplie IP-Adresses. Disable this function then.<br>"));
 $cfp->addWidget(new SelectBox("Traffic Statistics: <br>(Default = On)", "STATS", "true", $onOff));
 $cfp->addWidget(new SelectBox("Host Authorization: <br>(Default = On)", "HOSTAUTH", "true", $onOff));
 $cfp->addWidget(new Label( "<br>", "standard"));
 
 $cfp->addWidget(new Label("Locale Settings", "headline2"));
 $cfp->addWidget(new SelectBox("Default Language of the Backend:", "DEFLANG", 1, $languages));
 $cfp->addWidget(new TextInput("Standard Encoding:", "ENCODING", "text/html; charset=utf-8"));
 $cfp->addWidget(new Retain("DB"));
 $cfp->addWidget(new Retain("DBSERVER"));
 $cfp->addWidget(new Retain("DBUSER"));
 $cfp->addWidget(new Retain("DBPASSWD"));
 
 $page4->addWidget($cfp);
 
 $page5 = new Page("Finished");
 $page5->addWidget(new Information("", "./_installer/finished.txt"));
 
 
 $installer->addPage($page1);
 $installer->addPage($page2);
 $installer->addPage($page3);
 $installer->addPage($page4);
 $installer->addPage($page5);
 
 $installer->start();
 
 ?>