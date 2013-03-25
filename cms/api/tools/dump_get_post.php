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
	
	CREATE TABLE `var_log` (
   `NAME` VARCHAR( 32 ) NOT NULL ,
   PRIMARY KEY ( `NAME` ) 
   );
	*/
	// Database structure for dumping all POST and GET Variable names
	if (is_array($_GET)) {
		foreach ($_GET as $key => $value) {
			if (getDBCell("var_log", "NAME", "NAME = '$key'") == "" && (is_numeric($value) || $value == 0)) {
				$query = new query($db, "INSERT INTO var_log (NAME) VALUES ('$key')");

				$query->free();
			}
		}
	}

	if (is_array($_POST)) {
		foreach ($_POST as $key => $value) {
			if (getDBCell("var_log", "NAME", "NAME = '$key'") == "" && (is_numeric($value) || $value == 0)) {
				$query = new query($db, "INSERT INTO var_log (NAME) VALUES ('$key')");

				$query->free();
			}
		}
	}

	if (is_array($_SESSION)) {
		foreach ($_SESSION as $key => $value) {
			if (getDBCell("var_log", "NAME", "NAME = '$key'") == "" && (is_numeric($value) || $value == 0)) {
				$query = new query($db, "INSERT INTO var_log (NAME) VALUES ('$key')");

				$query->free();
			}
		}
	}
?>