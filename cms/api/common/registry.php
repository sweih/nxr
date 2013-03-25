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
	 * Central Configuration Library of the Web-Application
	 * @package Internals
	 */

	/**
	 * Deletes the key from the registry.
	 * @param $key string Key in format: folder1/folder11/keyname
	 */
	function reg_delete($key) {
		global $db, $auth;

		$pk = reg_getpk($key);

		if (countRows("registry", "REGID", "PARENTREGID=$pk") == 0) {
			$sql = "DELETE FROM registry WHERE REGID=$pk";

			$query = new query($db, $sql);
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Loads a key from the registry.
	 * @param $key string Key in format: folder1/folder11/keyname
	 */
	function reg_load($key) {
		$pk = reg_getpk($key);
		return getDBCell("registry", "VALUE", "REGID=$pk");
	}

	/**
	 * Saves a key to the registry.
	 * @param $key string Key in format: folder1/folder11/keyname
	 * @param $value string Value of the key to save
	 */
	function reg_save($key, $value) {
		global $db;

		$key = strtoupper($key);

		if (substr($key, 0, 1) == '/')
			$key = substr($key, 1);

		if ($key == "")
			return false;

		$paths = explode('/', $key);
		$parentKey = 0;

		for ($i = 0; $i < count($paths); $i++) {
			// check if key already exists.
			$sql = "SELECT * FROM registry WHERE PARENTREGID = $parentKey AND REGNAME = '" . $paths[$i] . "'";

			$query = new query($db, $sql);

			if ($i < (count($paths) - 1)) {
				// update path...	
				if ($query->getrow()) {
					$parentKey = $query->field("REGID");
				} else {
					$newKey = nextGUID();

					$sql = "INSERT INTO registry (REGID, PARENTREGID, REGNAME, VALUE) VALUES ($newKey,$parentKey,'" . $paths[$i] . "','')";
					$parentKey = $newKey;
					$query = new query($db, $sql);
				}
			} else {
				// update key...
				if ($query->getrow()) {
					$regid = $query->field("REGID");

					$sql = "UPDATE registry SET VALUE='$value' WHERE REGID = $regid";
				} else {
					$regid = nextGUID();

					$sql = "INSERT INTO registry (REGID, PARENTREGID, REGNAME, VALUE) VALUES ($regid,$parentKey,'" . $paths[$i] . "','$value')";
				}

				$query = new query($db, $sql);
			}

			$query->free();
		}
	}

	/**
	 * Determines a PKID from the registry.
	 * @param $key string Key in format: folder1/folder11/keyname
	 */
	function reg_getpk($key) {
		global $db;

		$key = strtoupper($key);

		if (substr($key, 0, 1) == '/')
			$key = substr($key, 1);

		if ($key == "")
			return 0;

		$paths = explode('/', $key);
		$parentKey = 0;

		for ($i = 0; $i < count($paths); $i++) {
			// check if key already exists.
			$sql = "SELECT * FROM registry WHERE PARENTREGID = $parentKey AND REGNAME = '" . $paths[$i] . "'";

			$query = new query($db, $sql);

			if ($query->getrow()) {
				$parentKey = $query->field("REGID");
			} else
				return 0;
		}

		return $parentKey;
	}
?>