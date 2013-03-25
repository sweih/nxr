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
	 * Main program
	 */
	require_once "../../config.inc.php";

	$auth = new auth("BACKUP");
	include ('pcltar.lib.php');
	include ('backupconfig.php');

	define('psdVersion', '0.1');
	$ftpsendTasks = array ();

	if ($backupSite == 1) {
		function EnumDirectory($from = '') {
			global $arrayFiles, $backupSiteExcludeFiles, $backupSiteExcludeDir, $backupSiteExclude;

			if ($dir = opendir($from)) {
				while ($file = readdir($dir)) {
					if (($file != '.') && ($file != '..')) {
						if (is_dir($from . $file) && !in_array($file, $backupSiteExcludeDir))
							EnumDirectory ($from . $file . '/');
						elseif (is_file($from . $file) && !in_array($file, $backupSiteExcludeFiles) && !in_array(substr(strrchr($file, '.'), 1), $backupSiteExclude))
							array_push($arrayFiles, $from . $file);
					}
				}
			}
		}

		$arrayFiles = array ();
		$tarName = $backupFileDir . '/' . date('d-m-Y-'). 'at' . date('-h-i'). '-files.tar.gz';

		EnumDirectory ($backupSiteHomedir . '/');

		if (PclTarCreate($tarName, $arrayFiles, 'tgz', '', $backupSiteHomedir)) {
			array_push($ftpsendTasks, $tarName);
		}
	}

	if ($backupDatabase == 1) {
		function ListTables($db, $cnx) {
			$list = array ();

			$_res = mysql_list_tables($db, $cnx);
			$i = 0;
			$ntables = (($_res) ? @mysql_num_rows($_res) : 0);

			while ($i < $ntables) {
				array_push($list, mysql_tablename($_res, $i));

				$i++;
			}

			return $list;
		}

		function DumpTables($list, $cnx) {
			$dump = array ();

			foreach ($list as $table) {
				$buff = 'DROP TABLE IF EXISTS '.$table.";\n";
				  $buff .= 'CREATE TABLE '.$table ."(\n";
				$_res = mysql_query('SHOW FIELDS FROM '.$table, $cnx);
				
						
						while($rec = mysql_fetch_array($_res)) {
							$buff .= ' '.$rec[Field].' '.$rec[Type];
							   if ($rec['Default'] != '') 
							$buff .= ' DEFAULT \''.$rec['Default'].'\'';
							if ($rew['Null'] != 'YES') 
							$buff .= ' NOT NULL';
							   if ($row[Extra] != "") 
							$buff .= ' '.$row['Extra'];
							$buff .= ','."\n";
						}
						// - Clées primaires / index
			array_push($dump, ereg_replace(',$',');',$buff));
			}
			return $dump;
		}

		function DumpDatas($table, $cnx) {
			$dump = array ();

			$_res = mysql_query('SELECT * FROM ' . $table, $cnx);

			while ($rec = mysql_fetch_array($_res)) {
				$buff = 'INSERT INTO ' . $table . ' VALUES (';

				for ($i = 0; $i < mysql_num_fields($_res); $i++) {
					if (!isset($rec[$i]))
						$buff .= 'NULL,';
					else
						$buff .= '\'' . addslashes($rec[$i]). '\',';
				}

				$buff .= 'END);';

				array_push($dump, str_replace(',END);', ');', $buff));
			}

			return $dump;
		}

		if ($cnx = mysql_connect($backupDatabaseServer, $backupDatabaseUsername, $backupDatabasePassword)) {
			$dump = '# ' . "\n";

			$dump .= '# N/X 2004 Backup ' . "\n";
			$dump .= '# ' . "\n";

			foreach ($backupDatabaseDbName as $dbname) {
				if (mysql_select_db($dbname)) {
					$dump .= '# Database ' . $dbname . "\n";

					if (sizeof($backupDatabaseDbName) > 1)
						$dump .= 'USE ' . $dbname . "\n";

					$t = ListTables($dbname, $cnx);

					foreach ($t as $ct) {
						$d = array_merge(DumpTables($t, $cnx), DumpDatas($ct, $cnx));

						foreach ($d as $v)$dump .= $v . "\n";
					}
				}
			}

			$dt = date('d-m-Y-'). 'at' . date('-h-i');
			$dumpName = $backupFileDir . '/dbbackup-' . $dt . '.sql';
			$tarName = $backupFileDir . '/' . $dt . '-db.tar.gz';

			fputs(fopen($dumpName, 'w'), $dump);

			if (PclTarCreate($tarName, $dumpName, 'tgz', '', $backupFileDir)) {
				array_push($ftpsendTasks, $tarName);

				unlink ($dumpName);
			}
		}
	}

	if ($backupFtpsend == 1) {
		$dt = date('d-m-Y-'). 'at' . date('-h-i');

		$tarName = $backupFileDir . '/backup-' . $dt . '.tar.gz';

		if (PclTarCreate($tarName, $ftpsendTasks, 'tgz', '', $backupFileDir)) {
			foreach ($ftpsendTasks as $file) {
				unlink ($file);
			}

			$fp = ftp_connect($backupFtpsendServer, $backupFtpsendPort);

			if (ftp_login($fp, $backupFtpsendUsername, $backupFtpsendPassword)) {
				ftp_chdir($fp, $backupFtpsendDirectory);

				/***********************************************************************
						foreach ($ftpsendTasks as $file) {
								
								 echo "\nFTP: $file\n";
							 $ret = ftp_nb_put($fp,basename($file),$file,FTP_BINARY);
							
							 while ($ret == FTP_MOREDATA)
								  $ret = ftp_nb_continue ($fp); 
						}
				*************************************************************************/
				$ret = ftp_put($fp, basename($tarName), $tarName, FTP_BINARY);
				ftp_quit ($fp);

				if ($backupFileDelete == 1) {
					unlink ($tarName);
				}
			}
		}
	}

	if ($backupMailNotify) {
		$message = "Bonjour,
   phpSiteBackup vient de terminer le backup
   
   Bonne journée
   --
   Le robot phpSiteBackup";

		if (mail($backupMailNotifyAdress, 'Rapport de phpSiteBackup', $message)) { }
	}

	$db->close();
	header ("Location: " . $c["docroot"] . "modules/backup/backup.php?sid=$sid");
?>