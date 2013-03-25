<?
	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2003 Sven Weih
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
	 * Class for logging events in the database.
	 */
	class DBLog {
		var $category = "";

		var $userId = 0;
		var $message = "";
		var $system = false;
		var $target1 = 0;
		var $target2 = 0;

		/**
		   * Creates the class which is used for logging information within N/X
		   * @param $logtype string NAME of a logfile type
		   */
		function DBLog($category) {
			global $auth;

			$this->category = strtoupper($category);

			if (isset($auth)) {
				$this->userId = $auth->userId;
			} else {
				$this->userId = "0";
			}
		}

		/**
		   * Write a simple log-entry in the database
		 * @param $message The message of the log
		 */
		function log($message) {
			$this->clear();

			$this->message = $message;
			$this->writeLog();
		}

		/**
		   * Write a simple log-entry in the database. The Source-Process is not the user here
		   * but the system.
		 * @param $message The message of the log
		 */
		function systemLog($message) {
			$this->clear();

			$this->message = $message;
			$this->system = true;
			$this->writeLog();
		}

		/**
		   * Write a log-entry including one or two objects, to whom the log belongs.
		   * @param $message The message of the log
		   * @param $target1 ID of object 1
		   * @param $target2 ID of object 2
		   */
		function logTarget($message, $target1 = 0, $target2 = 0) {
			$this->clear();

			$this->message = $message;
			$this->target1 = $target1;
			$this->target2 = $target1;
			$this->writeLog();
		}

		/**
		   * Write a log entry to the database
		   * @access private
		   */
		function writeLog() {
			global $db;

			$logId = $db->nextid("log");
			$cr = new CreateSet("log");
			$cr->add("LOG_ID", $logId, "NUMBER");
			$cr->add("CATEGORY", $this->category, "TEXT");
			$cr->add("MESSAGE", $this->message, "TEXT");

			if ($this->system) {
				$cr->add("USER_ID", 0, "NUMBER");
			} else {
				$cr->add("USER_ID", $this->userId, "NUMBER");
			}

			if ($this->target1 != 0) {
				$cr->add("TARGET1_ID", $this->target1, "NUMBER");
			}

			if ($this->target2 != 0) {
				$cr->add("TARGET2_ID", $this->target2, "NUMBER");
			}

			$cr->execute();
			unset ($cr);
		}

		/**
		 * Clear all log-specific dynamic values before setting up a new log
		 * @access private
		 */
		function clear() {
			$this->message = "";

			$this->system = false;
			$this->target1 = 0;
			$this->target2 = 0;
		}
	}
	
	
	/**
	 * Clear the N/X internal Log
	 */
	function resetNXLogs() {
	  global $db;
	  $sql = "DELETE FROM log WHERE 1";
	  $query = new query($db, $sql);
	  $query->free();	
	}
?>