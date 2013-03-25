<?php
	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2004 Sven Weih
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
	 * Send a message to a specific user
	 * @param integer ID of the Sender, 0 for System
	 * @param integer ID of the Recipient
	 * @param string Subject of the message
	 * @param string Message
	 */
	function sendMessage($fromGUID, $toGUID, $subject, $message) {
		global  $db;
		$toName = getDBCell("users", "FULL_NAME", "USER_ID = $toGUID");
		if ($fromGUID==0) {
			$fromName = "System";
		} else {
			$fromName = getDBCell("users", "FULL_NAME", "USER_ID = $fromGUID");	
		}
		$sql = "INSERT INTO messaging (GUID, SENDER_NAME, RECIPIENT_NAME, SENDER_GUID, RECIPIENT_GUID, SUBJECT, BODY, CREATED) ";
		$sql.="                VALUES (".nextGUID().", '$fromName', '$toName', $fromGUID, $toGUID, '$subject', '$message', NOW()+0)";
		$query = new query($db, $sql);
	}
	
	/**
	 * Delete a message
	 * @param integer ID of the message
	 */
	 function deleteMessage($guid) {
	 	deleteRow("messaging", "GUID=$guid");
	 }
	 
	 /**
	  * Mark a message as read
	  */
	  function markAsRead($guid") {
	    global $db;
	    $sql = "UPDATE messaging SET VIEWED=1 WHERE GUID=$guid";
	    $query = new query($db, $sql); 
	  }
	 
	 

?>