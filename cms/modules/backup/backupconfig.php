<?php
	// ---------------------------------------------------------------------
	// phpSiteBackup 0.1
	// ---------------------------------------------------------------------
	// BY  : MiK aka Random
	// WWW : http://truecore.ath.cx
	// MAiL: random@numericable.fr
	// ---------------------------------------------------------------------
	// This program is free software; you can redistribute it and/or
	// modify it under the terms of the GNU General Public License
	// as published by the Free Software Foundation; either version 2
	// of the License, or (at your option) any later version.
	// 
	// This program is distributed in the hope that it will be useful,
	// but WITHOUT ANY WARRANTY; without even the implied warranty of
	// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	// GNU General Public License for more details.
	//
	// You should have received a copy of the GNU General Public License
	// along with this program; if not, write to the Free Software
	// Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-130
	// ---------------------------------------------------------------------

	/**
	 * Configuration
	 */
	$backupSite = 1;

	$backupSiteHomedir = $c["basepath"];
	$backupSiteExclude = array ();
	$backupSiteExcludeFiles = array ();
	$backupSiteExcludeDir = array (
        "CVS",
        "sql",
        "cms",
		"backup",
		"devtools"
	);

	$backupDatabase = 1;
	$backupDatabaseUsername = $c["dbuser"];
	$backupDatabasePassword = $c["dbpasswd"];
	$backupDatabaseServer = $c["dbhost"];
	$backupDatabaseDbName = array ( $c["database"] );

	$backupFtpsend = $c["storeBackupFTP"];
	$backupFtpsendUsername = $c["ftpUsername"];
	$backupFtpsendPassword = $c["ftpPassword"];
	$backupFtpsendServer = $c["ftpServer"];
	$backupFtpsendDirectory = $c["ftpDir"];
	$backupFtpsendPort = $c["ftpPort"];

	#$backupMailNotify = 0;
	#$backupMailNotifyAdress = 'someone@email.com';
	$backupFileDelete = $c["deleteAfterFTP"] && $c["storeBackupFTP"];
	$backupFileDir = $c["backupStore"]; // Repertoire local ou enregistrer les backups (Doit être chmodé en 777)
?>