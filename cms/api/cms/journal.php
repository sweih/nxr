<?
	/**
	 * Journaling
	 * Logs system events
	 * @module Journaling
	 * @package ContentManagement
	 */

	// Copyright 2002 S.Weih, F.Koenig
	class journal {
		var $itemStatus;

		var $id;
		var $userId;

		function journal($id, $isLive = false) {
			global $db, $auth, $insertstatements;

			if ($isLive) {
				$id = translateState($id, 10, false);
			}

			$this->id = $id;
			$this->userId = $auth->userId;

			$jnlCreated = getDBCell("journal", "CREATED", "ITEM_ID = $this->id");

			if ($jnlCreated == 0) {
				$nextSlot = count($insertstatements);

				$journalInsert = new InsertSet("journal");

				$journalInsert->setPK("JOURNAL_ID");

				$journalInsert->add("ITEM_ID", $this->id, "NUMBER");
				$journalInsert->add("CREATED", time(), "NUMBER");

				$insertstatements[$nextSlot] = $journalInsert;

				$this->itemStatus["created"]["time"] = 0;
				//$this->itemStatus["created"]["by"] = $rstJournal->getValue("CREATED_BY");
				$this->itemStatus["launched"]["time"] = 0;
				//$this->itemStatus["launched"]["by"] = $rstJournal->getValue("LAUNCHED_BY");
				$this->itemStatus["expired"]["time"] = 0;
				//$this->itemStatus["expired"]["by"] = $rstJournal->getValue("EXPIRED_BY");
				$this->itemStatus["changed"]["time"] = 0;
				//$this->itemStatus["changed"]["by"] = $rstJournal->getValue("CHANGED_BY");
				$this->itemStatus["deleted"]["time"] = 0;
				//$this->itemStatus["deleted"]["by"] = $rstJournal->getValue("DELETED_BY");
				$this->itemStatus["staged"]["time"] = 0;
				//$this->itemStatus["staged"]["by"] = $rstJournal->getValue("STAGED_BY");
				$this->itemStatus["locked"]["time"] = 0;
				$this->itemStatus["locked"]["by"] = 0;
			} else {
				$rstJournal = new Recordset("journal", "*", "ITEM_ID = $this->id");

				$this->itemStatus["created"]["time"] = $rstJournal->getValue("CREATED");
				//$this->itemStatus["created"]["by"] = $rstJournal->getValue("CREATED_BY");
				$this->itemStatus["launched"]["time"] = $rstJournal->getValue("LAUNCHED");
				//$this->itemStatus["launched"]["by"] = $rstJournal->getValue("LAUNCHED_BY");
				$this->itemStatus["expired"]["time"] = $rstJournal->getValue("EXPIRED");
				//$this->itemStatus["expired"]["by"] = $rstJournal->getValue("EXPIRED_BY");
				$this->itemStatus["changed"]["time"] = $rstJournal->getValue("CHANGED");
				//$this->itemStatus["changed"]["by"] = $rstJournal->getValue("CHANGED_BY");
				$this->itemStatus["deleted"]["time"] = $rstJournal->getValue("DELETED");
				//$this->itemStatus["deleted"]["by"] = $rstJournal->getValue("DELETED_BY");
				$this->itemStatus["staged"]["time"] = $rstJournal->getValue("STAGED");
				//$this->itemStatus["staged"]["by"] = $rstJournal->getValue("STAGED_BY");
				$this->itemStatus["locked"]["time"] = $rstJournal->getValue("LOCKED");
				$this->itemStatus["locked"]["by"] = $rstJournal->getValue("LOCKED_BY");
			}
		}

		function commit() { processSaveSets(); }

		/**
		 * Checks wether an item is currently launched (live) or not
		 * @param	integer specifies the id of the item to check
		 * @returns array["time"], array["by"] in case it is live or false in case it is not.
		 */
		function isLive() {
			// echo "<br>launched:".$this->itemStatus["launched"]["time"]."|<br>";
			// echo "<br>expired:".$this->itemStatus["expired"]["time"]."|<br>";
			if ($this->itemStatus["launched"]["time"] > $this->itemStatus["expired"]["time"]) {
				return $this->itemStatus["launched"];
			} else {
				return false;
			}
		}

		/**
		 * Checks wether an item has been changed (is dirty) since last launch
		 * @param	integer specifies the id of the item to check
		 * @returns array["time"], array["by"] in case it is dirty or false in case it is not.
		 */
		function isDirty() {
			if ($this->itemStatus["changed"]["time"] > $this->itemStatus["launched"]["time"]) {
				return $this->itemStatus["changed"];
			} else {
				return false;
			}
		}

		/**
		 * Checks wether an item has been deleted since last launch
		 * @param	integer specifies the id of the item to check
		 * @returns array["time"], array["by"] in case it has been deleted or false in case it has not.
		 */
		function isDeleted() {
			if ($this->itemStatus["deleted"]["time"] > $this->itemStatus["launched"]["time"]) {
				return $this->itemStatus["deleted"];
			} else {
				return false;
			}
		}

		/**
		 * Checks wether an item has been staged since last launch
		 * @param	integer specifies the id of the item to check
		 * @returns array["time"], array["by"] in case it has been staged or false in case it has not.
		 */
		function isStaged() {
			if ($this->itemStatus["staged"]["time"] > $this->itemStatus["launched"]["time"]) {
				return $this->itemStatus["staged"];
			} else {
				return false;
			}
		}

		/**
		 * Checks wether an item has been expired since last launch (opposite function of isLive)
		 * @param	integer specifies the id of the item to check
		 * @returns array["time"], array["by"] in case it has been expired or false in case it has not.
		 */
		function isExpired() {
			if ($this->itemStatus["expired"]["time"] > $this->itemStatus["launched"]["time"]) {
				return $this->itemStatus["expired"];
			} else {
				return false;
			}
		}

		/**
		 * Checks wether an item is locked
		 * @param	integer specifies the id of the item to check
		 * @returns array["time"], array["by"] in case it is locked or false in case it is not.
		 */
		function isLocked() {
			if ($this->itemStatus["locked"]["time"] > 0) {
				return $this->itemStatus["locked"];
			} else {
				return false;
			}
		}

		/**
		 * Locks an item.
		 * @param	integer specifies the id of the item to update.
		 * @param integer specifies the User-ID of the User who locks the item.
		 */
		function lock() {
			$locktime = time();

			addUpdate("journal", "LOCKED", $locktime, "ITEM_ID = $this->id", "NUMBER");
			addUpdate("journal", "LOCKED_BY", $this->userId, "ITEM_ID = $this->id", "NUMBER");
			$this->itemStatus["locked"]["time"] = $locktime;
			$this->itemStatus["locked"]["by"] = $this->userId;
		}

		/**
		 * Unlocks an item.
		 * @returns true if unlock succeeded, false if not.
		 */
		function unlock() {
			$dounlock = false;

			if ($journal["locked"]["by"] == $this->$userId) {
				$dounlock = true;
			} else {
				if (time() - ($journal["locked"]["time"]) > 1500) {
					$dounlock = true;
				}
			}

			if ($dounlock) {
				addUpdate("journal", "LOCKED", 0, "ITEM_ID = $this->id", "NUMBER");

				addUpdate("journal", "LOCKED_BY", $this->userId, "ITEM_ID = $this->id", "NUMBER");
				$this->itemStatus["locked"]["time"] = 0;
				$this->itemStatus["locked"]["by"] = $this->userId;
				return true;
			} else {
				return false;
			}
		}

		/**
		 * Unlocks all items.
		 */
		function unlockAll() {
			addUpdate("journal", "LOCKED_BY", $this->userId, "LOCKED = 1", "NUMBER");

			addUpdate("journal", "LOCKED", 0, "LOCKED = 1", "NUMBER");
			$this->itemStatus["locked"]["time"] = 0;
			$this->itemStatus["locked"]["by"] = $this->userId;
		}

		/**
		 * Unlocks all items locked by a user.
		 * @param integer specifies the id of the user whos records shall be unlocked
		 */
		function unlockByUser($id_user) {
			addUpdate("journal", "LOCKED_BY", $this->userId, "LOCKED = 1 AND LOCKED_BY = $id_user", "NUMBER");

			addUpdate("journal", "LOCKED", 0, "LOCKED = 1 AND LOCKED_BY = $id_user", "NUMBER");
			$this->itemStatus["locked"]["time"] = 0;
			$this->itemStatus["locked"]["by"] = $this->userId;
		}

		/**
		 * Sets an item to be dirty.
		 * @param	integer specifies the id of the item to update.
		 * @param integer specifies the User-ID of the User who changed the item.
		 */
		function modify() {
			$modifytime = time();

			addUpdate("journal", "CHANGED", $modifytime, "ITEM_ID = $this->id", "NUMBER");
			addUpdate("journal", "CHANGED_BY", $this->userId, "ITEM_ID = $this->id", "NUMBER");
			$this->itemStatus["modify"]["time"] = $modifytime;
			$this->itemStatus["modify"]["by"] = $this->userId;
		}

		/**
		 * Sets an item to be live.
		 * @param integer specifies the id of the launched version of the item.
		 */
		function launch() {
			$launchedid = translateState($this->id, 10, false);

			$launchtime = time();
			addUpdate("journal", "LAUNCHED", $launchtime, "ITEM_ID = $this->id", "NUMBER");
			addUpdate("journal", "LAUNCHED_BY", $this->userId, "ITEM_ID = $this->id", "NUMBER");
			addUpdate("journal", "LAUNCHED_ID", $launchedid, "ITEM_ID = $this->id", "NUMBER");
			$this->itemStatus["launched"]["time"] = $launchtime;
			$this->itemStatus["launched"]["by"] = $this->userId;
		// echo "Journal entry launched";
		}

		/**
		 * Sets an item to be expired.
		 * @param	integer specifies the id of the item to update.
		 * @param integer specifies the User-ID of the User who expired the item.
		 */
		function expire() {
			$expiretime = time();

			addUpdate("journal", "EXPIRED", $expiretime, "ITEM_ID = $this->id", "NUMBER");
			addUpdate("journal", "EXPIRED_BY", $this->userId, "ITEM_ID = $this->id", "NUMBER");
			$this->itemStatus["expired"]["time"] = $expiretime;
			$this->itemStatus["expired"]["by"] = $this->userId;
		}

		/**
		 * Sets an item to be deleted.
		 * @param	integer specifies the id of the item to update.
		 * @param integer specifies the User-ID of the User who deleted the item.
		 */
		function delete() {
			$deletetime = time();

			addUpdate("journal", "DELETED", $deletetime, "ITEM_ID = $this->id", "NUMBER");
			addUpdate("journal", "DELETED_BY", $this->userId, "ITEM_ID = $this->id", "NUMBER");
			$this->itemStatus["deleted"]["time"] = $deletetime;
			$this->itemStatus["deleted"]["by"] = $this->userId;
		}

		/**
		 * Sets an item to be staged.
		 * @param	integer specifies the id of the item to update.
		 * @param integer specifies the User-ID of the User who staged the item.
		 */
		function stage() {
			$stagetime = time();

			addUpdate("journal", "STAGED", $stagetime, "ITEM_ID = $this->id", "NUMBER");
			addUpdate("journal", "STAGED_BY", $this->userId, "ITEM_ID = $this->id", "NUMBER");
			$this->itemStatus["staged"]["time"] = $stagetime;
			$this->itemStatus["staged"]["by"] = $this->userId;
		}

		/**
		 * Removes a journal-Entry.
		 * @param	integer specifies the id of the item whos journal-entry is to be removed.
		 */
		function removeEntry() { addDelete("journal", "ITEM_ID = $this->id"); }
	}
?>